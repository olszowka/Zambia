import type { DaySegment, ScheduleItemData } from './types';

export interface DisplaySegment extends DaySegment {
  // A contiguous timeline with the gaps between days removed, used for vertical pixel positioning. displayStart
  // is the top of the segment's day-name row; contentDisplayStart is where the segment's actual start time
  // (startMinutes) lands, which is displayStart + leadInMinutes.
  // The day name needs a 30-minute-tall row of its own above the segment's first hour tick (see
  // buildHourTicks()). When the segment starts at least 30 minutes before its first whole hour (e.g. :30), that
  // first stretch of real time is unlabeled anyway and holds the day name, so leadInMinutes is 0. Otherwise
  // leadInMinutes reserves the shortfall above the real start: 30 for an on-the-hour start, 15 for :45.
  displayStart: number;
  contentDisplayStart: number;
  displayEnd: number;
  leadInMinutes: number;
  // The stretches of the segment that have grid rows, in order: one per configured day segment it was built
  // from. A same-day gap between two of them (e.g. a lunch break) stays on the time axis but has no grid rows of
  // its own -- it's a single empty row. See gridRowStart().
  gridRanges: GridRange[];
}

// Grid rows repeat every grid-line resolution minutes from anchor (the configured start of the day segment),
// within [start, end). The first range of a display segment starts at the segment's start and the last ends at
// its end, so any stretch added by coverScheduledSessions() continues that range's pattern.
export interface GridRange {
  anchor: number;
  start: number;
  end: number;
}

// See DisplaySegment.leadInMinutes.
const DAY_NAME_ROW_MINUTES = 30;

interface DaySpan extends DaySegment {
  gridRanges: GridRange[];
}

// Builds one segment per day (fewer, if one day's is stretched over the next's) from the configured day segments:
//  - Segments on the same day are joined: a gap between them stays on the time axis (see
//    DisplaySegment.gridRanges).
//  - Each scheduled session outside every day (e.g. scheduled through the Maintain Room Schedule page, or before
//    GRID_DAY_SEGMENTS was changed) stretches the nearest day to cover it, rounded out to the half hour, so it's
//    drawn at its real time instead of being clamped to a day's edge. A session in a gap needs no stretching.
//  - A day stretched into the next day's takes over the overlap, and the next day starts where it ends (or
//    disappears, if fully covered).
export function coverScheduledSessions(daySegments: DaySegment[], schedule: ScheduleItemData[]): DaySpan[] {
  const spans: DaySpan[] = [];
  for (const segment of daySegments) {
    const range = { anchor: segment.startMinutes, start: segment.startMinutes, end: segment.endMinutes };
    const previous = spans[spans.length - 1];
    if (previous && previous.day === segment.day) {
      previous.endMinutes = segment.endMinutes;
      previous.gridRanges.push(range);
    } else {
      spans.push({ ...segment, gridRanges: [range] });
    }
  }
  if (spans.length === 0) return spans;
  for (const item of schedule) {
    const start = item.startMinutes;
    const end = start + Math.max(item.durationMinutes, 1);
    if (spans.some((span) => span.startMinutes <= start && end <= span.endMinutes)) continue;
    let nearest = spans[0];
    let nearestDistance = Infinity;
    for (const span of spans) {
      const distance = Math.max(0, span.startMinutes - end, start - span.endMinutes);
      if (distance < nearestDistance) {
        nearest = span;
        nearestDistance = distance;
      }
    }
    nearest.startMinutes = Math.min(nearest.startMinutes, Math.floor(start / 30) * 30);
    nearest.endMinutes = Math.max(nearest.endMinutes, Math.ceil(end / 30) * 30);
  }
  const result: DaySpan[] = [];
  for (const span of spans) {
    const previous = result[result.length - 1];
    if (previous && span.startMinutes < previous.endMinutes) {
      if (span.endMinutes <= previous.endMinutes) continue;
      span.startMinutes = previous.endMinutes;
    }
    // Fit the ranges to the (possibly stretched or pushed-later) span.
    const ranges = span.gridRanges
      .filter((range, index, all) => index === all.length - 1 || range.end > span.startMinutes)
      .map((range) => ({ ...range }));
    ranges[0].start = span.startMinutes;
    ranges[ranges.length - 1].end = span.endMinutes;
    span.gridRanges = ranges;
    result.push(span);
  }
  return result;
}

export function buildDisplaySegments(daySpans: DaySpan[]): DisplaySegment[] {
  let cumulative = 0;
  return daySpans.map((span) => {
    const minutesToFirstHour = (60 - (span.startMinutes % 60)) % 60;
    const leadInMinutes = Math.max(0, DAY_NAME_ROW_MINUTES - minutesToFirstHour);
    const displayStart = cumulative;
    const contentDisplayStart = displayStart + leadInMinutes;
    const length = span.endMinutes - span.startMinutes;
    const displayEnd = contentDisplayStart + length;
    const displaySegment: DisplaySegment = { ...span, displayStart, contentDisplayStart, displayEnd, leadInMinutes };
    cumulative = displayEnd;
    return displaySegment;
  });
}

// The start of the grid row (at grid-line resolution) containing absoluteMinutes in the given segment: a row of
// the grid range containing it (see GridRange), whose first and last rows may be cut short, or else the gap it
// falls in, which is one row.
export function gridRowStart(segment: DisplaySegment, absoluteMinutes: number, gridLineResolutionMinutes: number): number {
  let rowStart = segment.startMinutes;
  for (const range of segment.gridRanges) {
    if (absoluteMinutes < range.start) break;
    if (absoluteMinutes >= range.end) {
      rowStart = range.end;
      continue;
    }
    return Math.max(nextPatternLine(range, absoluteMinutes, gridLineResolutionMinutes) - gridLineResolutionMinutes, range.start);
  }
  return rowStart;
}

// The first line of the range's grid-row pattern after absoluteMinutes.
function nextPatternLine(range: GridRange, absoluteMinutes: number, gridLineResolutionMinutes: number): number {
  return (
    range.anchor +
    (Math.floor((absoluteMinutes - range.anchor) / gridLineResolutionMinutes) + 1) * gridLineResolutionMinutes
  );
}

// Grid Simplification is possible at all only when a default-duration session fits in one grid row.
export function gridSimplificationAvailable(
  defaultDurationMinutes: number | null,
  gridLineResolutionMinutes: number
): defaultDurationMinutes is number {
  return defaultDurationMinutes !== null && defaultDurationMinutes > 0 && defaultDurationMinutes <= gridLineResolutionMinutes;
}

// Grid Simplification (a page-level option): in a room mostly scheduled in step with the grid, a session is drawn as
// if it lasted until the next grid line, hiding the gap after it. Display only -- dropping still works from real
// times. Returns, by scheduleid, the end time to draw for each of the room's sessions that qualify:
//  - the room and the session's type both allow it (Rooms/Types.allow_grid_simplification);
//  - the session is exactly the default duration (DEFAULT_DURATION), which is no longer than a grid row;
//  - it starts on a grid line inside a grid range (not in a same-day gap -- see DisplaySegment.gridRanges);
//  - it ends before the end of that grid row (the next grid line, or the end of the range if that's sooner);
//  - nothing else in the room overlaps it or the rest of its row.
export function simplifiedEnds(
  roomItems: ScheduleItemData[],
  roomAllows: boolean,
  displaySegments: DisplaySegment[],
  gridLineResolutionMinutes: number,
  defaultDurationMinutes: number | null
): Map<number, number> {
  const ends = new Map<number, number>();
  if (!roomAllows || !gridSimplificationAvailable(defaultDurationMinutes, gridLineResolutionMinutes)) return ends;
  for (const item of roomItems) {
    if (!item.typeGridSimplification || item.durationMinutes !== defaultDurationMinutes) continue;
    const start = item.startMinutes;
    const segment = displaySegments.find((s) => s.startMinutes <= start && start < s.endMinutes);
    const range = segment?.gridRanges.find((r) => r.start <= start && start < r.end);
    if (!range) continue;
    const offset = start - range.anchor;
    const onGridLine =
      start === range.start ||
      ((offset % gridLineResolutionMinutes) + gridLineResolutionMinutes) % gridLineResolutionMinutes === 0;
    if (!onGridLine) continue;
    const rowEnd = Math.min(nextPatternLine(range, start, gridLineResolutionMinutes), range.end);
    if (start + item.durationMinutes >= rowEnd) continue;
    const blocked = roomItems.some(
      (other) =>
        other.scheduleid !== item.scheduleid &&
        other.startMinutes < rowEnd &&
        start < other.startMinutes + Math.max(other.durationMinutes, 1)
    );
    if (!blocked) ends.set(item.scheduleid, rowEnd);
  }
  return ends;
}

export function totalDisplayMinutes(displaySegments: DisplaySegment[]): number {
  if (displaySegments.length === 0) return 0;
  return displaySegments[displaySegments.length - 1].displayEnd;
}

// Maps an absolute minutes-since-con-start value onto the contiguous display timeline. Values outside
// every segment are clamped to the nearest segment boundary rather than dropped, so nothing silently
// disappears from the grid -- though coverScheduledSessions() keeps scheduled sessions from ever needing this.
export function minutesToDisplayOffset(displaySegments: DisplaySegment[], absoluteMinutes: number): number {
  if (displaySegments.length === 0) return 0;
  for (const segment of displaySegments) {
    if (absoluteMinutes >= segment.startMinutes && absoluteMinutes <= segment.endMinutes) {
      return segment.contentDisplayStart + (absoluteMinutes - segment.startMinutes);
    }
    if (absoluteMinutes < segment.startMinutes) {
      // Clamp to where this segment's actual content begins -- never into its day-name lead-in row.
      return segment.contentDisplayStart;
    }
  }
  return displaySegments[displaySegments.length - 1].displayEnd;
}

export function formatTimeOfDay(absoluteMinutes: number): string {
  const minutesInDay = ((absoluteMinutes % (24 * 60)) + 24 * 60) % (24 * 60);
  const hour24 = Math.floor(minutesInDay / 60);
  const minute = minutesInDay % 60;
  const isPM = hour24 >= 12;
  let hour12 = hour24 % 12;
  if (hour12 === 0) hour12 = 12;
  const minuteStr = minute < 10 ? `0${minute}` : `${minute}`;
  return `${hour12}:${minuteStr}${isPM ? 'p' : 'a'}`;
}

export interface AxisTick {
  displayOffset: number;
  label: string;
  isSegmentStart: boolean;
}

// Used for the room columns' empty-grid lines only -- NOT the time axis (see buildHourTicks() below for
// that). These lines represent actual schedulable granularity, so they legitimately follow grid-line
// resolution (GRID_SPACING_MINUTES, else STANDARD_BLOCK_LENGTH), with none inside a same-day gap (see
// DisplaySegment.gridRanges) -- one of three distinct, similarly-named resolutions in the grid scheduler; see the "Time
// resolutions" section of the rewrite plan. Do not conflate this with snap resolution (a page-level
// Snap Mode control -- see computeDropStart() in dropTarget.ts) or display resolution
// (DISPLAY_RESOLUTION_MINUTES in components/Grid.tsx). The bold day-boundary line always sits at the
// segment's displayStart -- the same row as the axis's day-name label (see buildHourTicks()) -- even on
// an hour-aligned segment where that row is a lead-in with no real schedulable time in it, so the bold
// line lines up across the axis and every room column. When there's no lead-in (a half-hour start),
// displayStart and contentDisplayStart are the same row, so this doesn't add a separate tick there --
// the first real tick just carries the isSegmentStart flag itself, as before.
export function buildGridLineTicks(displaySegments: DisplaySegment[], gridLineResolutionMinutes: number): AxisTick[] {
  const ticks: AxisTick[] = [];
  for (const segment of displaySegments) {
    if (segment.leadInMinutes > 0) {
      ticks.push({ displayOffset: segment.displayStart, label: segment.dayName, isSegmentStart: true });
    }
    // Each row ends at the next grid line, the end of its grid range (the start of a gap), or the start of the
    // next grid range (the end of a gap), whichever comes first.
    for (let t = segment.startMinutes; t < segment.endMinutes; ) {
      ticks.push({
        displayOffset: segment.contentDisplayStart + (t - segment.startMinutes),
        label: t === segment.startMinutes ? `${segment.dayName} ${formatTimeOfDay(t)}` : formatTimeOfDay(t),
        isSegmentStart: segment.leadInMinutes === 0 && t === segment.startMinutes,
      });
      const range = segment.gridRanges.find((r) => r.start <= t && t < r.end);
      const nextRange = segment.gridRanges.find((r) => r.start > t);
      const rowEnd = range
        ? Math.min(nextPatternLine(range, t, gridLineResolutionMinutes), range.end)
        : Infinity;
      t = Math.min(rowEnd, nextRange ? nextRange.start : segment.endMinutes);
    }
  }
  // A line closing off the last day. Every other day's end is already marked by the next day's bold line, which
  // sits at the same display offset.
  if (displaySegments.length > 0) {
    const last = displaySegments[displaySegments.length - 1];
    ticks.push({ displayOffset: last.displayEnd, label: formatTimeOfDay(last.endMinutes), isSegmentStart: false });
  }
  return ticks;
}

// Used for the time axis column only. Unlike buildGridLineTicks() above, this deliberately ignores
// grid-line resolution entirely -- the axis always labels/lines on the hour,
// matching the legacy getScheduleTimesArray()'s "timeTop" (hour, labeled) vs "timeBottom" (half-hour,
// unlabeled) distinction, which was independent of STANDARD_BLOCK_LENGTH there too.
//
// Day and time are never combined on one line. Each segment gets a day-name-only marker at
// displayStart, then normal hour ticks starting at the first whole hour at/after the segment's actual
// start (segment.startMinutes):
//  - e.g. a half-hour start (leadInMinutes 0): the day-name marker occupies that first, otherwise-unlabeled,
//    half-hour row; hour ticks pick up normally at the next :00.
//  - e.g. an on-the-hour start (leadInMinutes 30): there's no spare half-hour row to reuse (the segment's own
//    first row already wants an hour label), so buildDisplaySegments() reserved an extra 30-minute row
//    above it for the day name, and the segment's actual start gets a normal (non-bold) hour tick right
//    below that, at contentDisplayStart.
export function buildHourTicks(displaySegments: DisplaySegment[]): AxisTick[] {
  const ticks: AxisTick[] = [];
  for (const segment of displaySegments) {
    ticks.push({
      displayOffset: segment.displayStart,
      label: segment.dayName,
      isSegmentStart: true,
    });
    const firstHour = Math.ceil(segment.startMinutes / 60) * 60;
    for (let t = firstHour; t < segment.endMinutes; t += 60) {
      ticks.push({
        displayOffset: segment.contentDisplayStart + (t - segment.startMinutes),
        label: formatTimeOfDay(t),
        isSegmentStart: false,
      });
    }
  }
  return ticks;
}

export interface LaidOutItem extends ScheduleItemData {
  colIndex: number;
  colCount: number;
}

// Standard calendar-style overlap layout: sweep items in start order, grouping any that transitively
// overlap into a cluster, then greedily assign each item in a cluster to the first column whose
// previous occupant has already ended. Replaces the legacy scheduleGridCompTAB sub-table, which threw
// "Block too complicated to render" past 2 concurrent sessions -- this has no such cap.
export function layoutOverlaps(items: ScheduleItemData[]): LaidOutItem[] {
  const sorted = [...items].sort((a, b) => a.startMinutes - b.startMinutes);
  const result: LaidOutItem[] = [];
  let cluster: ScheduleItemData[] = [];
  let clusterEnd = -Infinity;

  const flushCluster = () => {
    if (cluster.length === 0) return;
    const columnEnds: number[] = [];
    for (const item of cluster) {
      const itemEnd = item.startMinutes + item.durationMinutes;
      let colIndex = columnEnds.findIndex((end) => end <= item.startMinutes);
      if (colIndex === -1) {
        colIndex = columnEnds.length;
        columnEnds.push(itemEnd);
      } else {
        columnEnds[colIndex] = itemEnd;
      }
      result.push({ ...item, colIndex, colCount: -1 }); // colCount filled in below once known
    }
    const colCount = columnEnds.length;
    for (let i = result.length - cluster.length; i < result.length; i++) {
      result[i].colCount = colCount;
    }
    cluster = [];
  };

  for (const item of sorted) {
    if (cluster.length > 0 && item.startMinutes >= clusterEnd) {
      flushCluster();
      clusterEnd = -Infinity;
    }
    cluster.push(item);
    clusterEnd = Math.max(clusterEnd, item.startMinutes + item.durationMinutes);
  }
  flushCluster();

  return result;
}
