import type { DisplaySegment } from './timeGrid';
import type { DragSource, DropResolution, ScheduleItemData, SnapMode } from './types';
import { PIXELS_PER_MINUTE } from './layoutConstants';

export const SNAP_MODES: { value: SnapMode; label: string }[] = [
  { value: 'grid', label: 'Grid' },
  { value: 5, label: '5 min' },
  { value: 10, label: '10 min' },
  { value: 15, label: '15 min' },
  { value: 30, label: '30 min' },
];

// Inverse of minutesToDisplayOffset() (timeGrid.ts): maps a vertical position on the contiguous display
// timeline back to an absolute minutes-since-con-start value, along with the day segment it falls in. A
// position in a segment's day-name lead-in row, or above the first segment, clamps to that segment's first
// real minute; one past the last segment clamps to its end.
export function displayOffsetToAbsolute(
  displaySegments: DisplaySegment[],
  displayOffset: number
): { minutes: number; segment: DisplaySegment } | null {
  for (const segment of displaySegments) {
    if (displayOffset < segment.contentDisplayStart) {
      return { minutes: segment.startMinutes, segment };
    }
    if (displayOffset < segment.displayEnd) {
      return { minutes: segment.startMinutes + (displayOffset - segment.contentDisplayStart), segment };
    }
  }
  if (displaySegments.length === 0) return null;
  const last = displaySegments[displaySegments.length - 1];
  return { minutes: last.endMinutes, segment: last };
}

function clamp(value: number, min: number, max: number): number {
  return Math.min(Math.max(value, min), max);
}

interface ComputeDropStartArgs {
  segment: DisplaySegment;
  // Absolute time under the top edge of the dragged box (fractional minutes).
  topMinutes: number;
  snapMode: SnapMode;
  // Grid-line resolution (concept #1 in the plan's "Time resolutions" section) -- only consulted by the 'grid'
  // snap mode.
  gridLineResolutionMinutes: number;
  // Everything already scheduled in the target room, excluding the session being dragged.
  roomItems: ScheduleItemData[];
}

// Snap resolution (concept #2 in the plan's "Time resolutions" section): turns the raw time under the dragged
// box's top edge into the start time the session would actually be scheduled at.
//  - Fixed N-minute modes round to the nearest N-minute boundary, regardless of grid rows or other sessions.
//  - 'grid' mode reproduces the legacy page's slot-based dropping (doABlock()'s empty and compound-empty
//    slots): it probes half a grid row below the top edge -- so the box snaps to whichever row its top edge is
//    nearest -- and lands at the start of the empty slot containing that probe point. An empty slot is a grid
//    row, narrowed by any adjacent session ending partway through it (so a session ending at 10:20 makes the
//    10:00 row's empty slot start at 10:20). If the probe point is inside an existing session instead, the
//    dragged session lands alongside it, at its start time (the legacy "drop on an occupied slot" behavior).
// Either way, the result always starts within the segment it was dropped in.
export function computeDropStart({
  segment,
  topMinutes,
  snapMode,
  gridLineResolutionMinutes,
  roomItems,
}: ComputeDropStartArgs): number {
  if (snapMode !== 'grid') {
    const minStart = Math.ceil(segment.startMinutes / snapMode) * snapMode;
    const maxStart = Math.max(minStart, Math.floor((segment.endMinutes - 1) / snapMode) * snapMode);
    return clamp(Math.round(topMinutes / snapMode) * snapMode, minStart, maxStart);
  }

  const resolution = gridLineResolutionMinutes;
  const probe = clamp(topMinutes + resolution / 2, segment.startMinutes, segment.endMinutes - 1);
  const occupant = roomItems.find(
    (item) => item.startMinutes <= probe && probe < item.startMinutes + item.durationMinutes
  );
  if (occupant) {
    return occupant.startMinutes;
  }
  let start = segment.startMinutes + Math.floor((probe - segment.startMinutes) / resolution) * resolution;
  for (const item of roomItems) {
    const itemEnd = item.startMinutes + item.durationMinutes;
    if (itemEnd > start && itemEnd <= probe) {
      start = itemEnd;
    }
  }
  return start;
}

// What's under the pointer, as far as dropping is concerned. Hit-tested against the real DOM
// (elementFromPoint), not against @dnd-kit's own droppable registry: the drop semantics depend on details
// @dnd-kit doesn't model (which room column a session box sits in, sticky headers/axis covering columns when
// scrolled). The drag overlay and the drag source both have pointer-events: none while dragging, so they're
// never what's hit. Droppable elements are tagged with data-drop-kind (+ data-roomid / data-scheduleid).
type PointerTarget =
  | { kind: 'room'; roomid: number; columnEl: HTMLElement }
  | { kind: 'session'; scheduleid: number; roomid: number; columnEl: HTMLElement }
  | { kind: 'cabinet' }
  | { kind: 'pool' };

function hitTest(x: number, y: number): PointerTarget | null {
  const hit = document.elementFromPoint(x, y);
  const dropEl = hit?.closest<HTMLElement>('[data-drop-kind]');
  if (!dropEl) return null;
  switch (dropEl.dataset.dropKind) {
    case 'cabinet':
      return { kind: 'cabinet' };
    case 'pool':
      return { kind: 'pool' };
    case 'room':
      return { kind: 'room', roomid: Number(dropEl.dataset.roomid), columnEl: dropEl };
    case 'session': {
      const columnEl = dropEl.closest<HTMLElement>('[data-drop-kind="room"]');
      if (!columnEl) return null;
      return {
        kind: 'session',
        scheduleid: Number(dropEl.dataset.scheduleid),
        roomid: Number(columnEl.dataset.roomid),
        columnEl,
      };
    }
  }
  return null;
}

export interface ResolveDropArgs {
  source: DragSource;
  pointerX: number;
  pointerY: number;
  // Viewport y of the dragged box's (drag overlay's) top edge.
  boxTop: number;
  swapMode: boolean;
  snapMode: SnapMode;
  gridLineResolutionMinutes: number;
  displaySegments: DisplaySegment[];
  schedule: ScheduleItemData[];
}

// Resolves the drop-target matrix from the rewrite plan's drag-and-drop section into a single outcome. Returns
// null when a release here would do nothing (e.g. over the axis, or an unscheduled session over the pool it
// came from).
export function resolveDrop(args: ResolveDropArgs): DropResolution | null {
  const { source, swapMode, schedule } = args;
  const target = hitTest(args.pointerX, args.pointerY);
  if (!target) return null;
  if (target.kind === 'cabinet') return { kind: 'cabinet' };
  if (target.kind === 'pool') return source.kind === 'scheduled' ? { kind: 'pool' } : null;

  const draggedScheduleId = source.kind === 'scheduled' ? source.item.scheduleid : null;
  if (target.kind === 'session' && swapMode && target.scheduleid !== draggedScheduleId) {
    const swapTarget = schedule.find((item) => item.scheduleid === target.scheduleid);
    if (swapTarget) return { kind: 'swap', target: swapTarget };
  }

  // Swap Mode off (or no swap target): place by time, whether the pointer is over empty grid or another
  // session -- computeDropStart() handles landing alongside an existing session.
  const columnTop = target.columnEl.getBoundingClientRect().top;
  const located = displayOffsetToAbsolute(args.displaySegments, (args.boxTop - columnTop) / PIXELS_PER_MINUTE);
  if (!located) return null;
  const startMinutes = computeDropStart({
    segment: located.segment,
    topMinutes: located.minutes,
    snapMode: args.snapMode,
    gridLineResolutionMinutes: args.gridLineResolutionMinutes,
    roomItems: schedule.filter((item) => item.roomid === target.roomid && item.scheduleid !== draggedScheduleId),
  });
  return { kind: 'place', roomid: target.roomid, startMinutes };
}
