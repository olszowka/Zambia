import type { DaySegment, ScheduleItemData } from './types';

export interface DisplaySegment extends DaySegment {
  // A contiguous timeline with the gaps between day segments removed, used for vertical pixel
  // positioning. displayStart is the top of the segment's day-name row; contentDisplayStart is where
  // the segment's actual start time (startMinutes) lands, which is displayStart + leadInMinutes.
  // leadInMinutes is 30 when the segment starts exactly on the hour -- see buildHourTicks() -- so there's
  // still a 30-minute-tall row available to show the day name on its own, without a normal hour tick
  // already occupying that same row. It's 0 when the segment starts on a half hour, since that first
  // (real) half-hour row is unlabeled by an hour tick anyway and can show the day name instead.
  displayStart: number;
  contentDisplayStart: number;
  displayEnd: number;
  leadInMinutes: number;
}

export function buildDisplaySegments(daySegments: DaySegment[]): DisplaySegment[] {
  let cumulative = 0;
  return daySegments.map((segment) => {
    const leadInMinutes = segment.startMinutes % 60 === 0 ? 30 : 0;
    const displayStart = cumulative;
    const contentDisplayStart = displayStart + leadInMinutes;
    const length = segment.endMinutes - segment.startMinutes;
    const displayEnd = contentDisplayStart + length;
    const displaySegment: DisplaySegment = { ...segment, displayStart, contentDisplayStart, displayEnd, leadInMinutes };
    cumulative = displayEnd;
    return displaySegment;
  });
}

export function totalDisplayMinutes(displaySegments: DisplaySegment[]): number {
  if (displaySegments.length === 0) return 0;
  return displaySegments[displaySegments.length - 1].displayEnd;
}

// Maps an absolute minutes-since-con-start value onto the contiguous display timeline. Values outside
// every segment (e.g. a session that starts before FIRST_DAY_START_TIME due to the phase-1 static
// day-segment simplification -- see gridScheduler_functions.php) are clamped to the nearest segment
// boundary rather than dropped, so nothing silently disappears from the grid.
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
// resolution (STANDARD_BLOCK_LENGTH / the GRID_TIME_RESOLUTION_MINUTES config planned for a later
// phase) -- one of three distinct, similarly-named resolutions in the grid scheduler; see the "Time
// resolutions" section of the rewrite plan. Do not conflate this with snap resolution (a page-level
// Snap Mode control, planned for the drag-and-drop phase) or display resolution
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
    for (let t = segment.startMinutes; t < segment.endMinutes; t += gridLineResolutionMinutes) {
      ticks.push({
        displayOffset: segment.contentDisplayStart + (t - segment.startMinutes),
        label: t === segment.startMinutes ? `${segment.dayName} ${formatTimeOfDay(t)}` : formatTimeOfDay(t),
        isSegmentStart: segment.leadInMinutes === 0 && t === segment.startMinutes,
      });
    }
  }
  return ticks;
}

// Used for the time axis column only. Unlike buildGridLineTicks() above, this deliberately ignores
// grid-line resolution/STANDARD_BLOCK_LENGTH entirely -- the axis always labels/lines on the hour,
// matching the legacy getScheduleTimesArray()'s "timeTop" (hour, labeled) vs "timeBottom" (half-hour,
// unlabeled) distinction, which was independent of STANDARD_BLOCK_LENGTH there too.
//
// Day and time are never combined on one line. Each segment gets a day-name-only marker at
// displayStart, then normal hour ticks starting at the first whole hour at/after the segment's actual
// start (segment.startMinutes):
//  - half-hour start (leadInMinutes 0): the day-name marker occupies that first, otherwise-unlabeled,
//    half-hour row; hour ticks pick up normally at the next :00.
//  - on-the-hour start (leadInMinutes 30): there's no spare half-hour row to reuse (the segment's own
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
    const firstHour =
      segment.leadInMinutes === 30 ? segment.startMinutes : Math.floor(segment.startMinutes / 60) * 60 + 60;
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
