import { formatTimeOfDay } from './timeGrid';
import type {
  DragSource,
  DropResolution,
  RoomData,
  ScheduleEdit,
  ScheduleItemData,
  SessionSearchResult,
} from './types';

// Everything needed to carry out one drop: the edits to send to the server, plus the client-side state changes
// to show immediately (optimistically) and to undo if the server doesn't apply them.
export interface EditPlan {
  edits: ScheduleEdit[];
  optimisticSchedule: ScheduleItemData[];
  // Sessions to add to the front of the "sessions to be scheduled" pool / sessionids to remove from it.
  poolAdd: SessionSearchResult[];
  poolRemoveIds: number[];
  // Human-readable confirmation of each change, shown in the Warnings tab once applied.
  messages: string[];
  // Swap Mode only: the occupant displaced by the drop, and where it ends up -- animated there by
  // swapAnimation.ts, since unlike the dragged session it moves without the user watching it move.
  counterpart?: { sessionid: number; destination: 'grid' | 'pool' };
  // Where the dragged session lands on the grid, when it lands on the grid at all -- used to keep the drop preview
  // outline on screen while a conflicting edit awaits Save Anyway / Cancel.
  landing?: { roomid: number; startMinutes: number; durationMinutes: number };
}

export interface PlanContext {
  schedule: ScheduleItemData[];
  rooms: RoomData[];
  conStartDateTime: string;
}

// Optimistically-inserted rows need a scheduleid before the server assigns one; negative so they can never
// collide with a real one. They're replaced wholesale by the server's fresh schedule once the edit is saved.
let nextTemporaryScheduleId = -1;

export function formatDayTime(conStartDateTime: string, absoluteMinutes: number): string {
  // conStartDateTime is local time ("YYYY-MM-DDTHH:MM:SS", no zone), which Date parses as local -- only the
  // calendar day is used here, so DST shifts don't matter.
  const day = new Date(conStartDateTime);
  day.setDate(day.getDate() + Math.floor(absoluteMinutes / (24 * 60)));
  const weekday = day.toLocaleDateString('en-US', { weekday: 'short' });
  return `${weekday} ${formatTimeOfDay(absoluteMinutes)}`;
}

function describeSlot(ctx: PlanContext, roomid: number, startMinutes: number, durationMinutes: number): string {
  const roomname = ctx.rooms.find((room) => room.roomid === roomid)?.roomname ?? `room ${roomid}`;
  const endTime = formatTimeOfDay(startMinutes + durationMinutes);
  return `${roomname}, ${formatDayTime(ctx.conStartDateTime, startMinutes)}–${endTime}`;
}

function describeSession(session: { sessionid: number; title: string }): string {
  return `Session ${session.sessionid} (${session.title})`;
}

function toPoolEntry(item: ScheduleItemData): SessionSearchResult {
  return {
    sessionid: item.sessionid,
    title: item.title,
    trackname: item.trackname,
    typename: item.typename,
    divisionname: item.divisionname,
    durationMinutes: item.durationMinutes,
  };
}

function toScheduleItem(session: SessionSearchResult, roomid: number, startMinutes: number): ScheduleItemData {
  return { ...session, scheduleid: nextTemporaryScheduleId--, roomid, startMinutes };
}

function moveItem(schedule: ScheduleItemData[], scheduleid: number, roomid: number, startMinutes: number) {
  return schedule.map((item) => (item.scheduleid === scheduleid ? { ...item, roomid, startMinutes } : item));
}

// The short label shown next to the dragged box while it's over a valid target.
export function describeDrop(ctx: PlanContext, source: DragSource, drop: DropResolution): string {
  switch (drop.kind) {
    case 'place':
      return formatDayTime(ctx.conStartDateTime, drop.startMinutes);
    case 'swap':
      return `Swap with ${drop.target.sessionid}`;
    case 'cabinet':
      return source.kind === 'scheduled' ? 'Remove from schedule' : 'Remove from list';
    case 'pool':
      return 'Return to list';
  }
}

// Turns a drop into an EditPlan, following the drag-and-drop matrix in the rewrite plan. Returns null when the
// drop changes nothing (e.g. a scheduled session released exactly where it already was).
export function planDrop(ctx: PlanContext, source: DragSource, drop: DropResolution): EditPlan | null {
  const { schedule } = ctx;

  if (source.kind === 'unscheduled') {
    const session = source.session;
    switch (drop.kind) {
      case 'place':
        return {
          edits: [{ action: 'insert', sessionid: session.sessionid, roomid: drop.roomid, startMinutes: drop.startMinutes }],
          optimisticSchedule: [...schedule, toScheduleItem(session, drop.roomid, drop.startMinutes)],
          landing: { roomid: drop.roomid, startMinutes: drop.startMinutes, durationMinutes: session.durationMinutes },
          poolAdd: [],
          poolRemoveIds: [session.sessionid],
          messages: [
            `${describeSession(session)} scheduled in ${describeSlot(ctx, drop.roomid, drop.startMinutes, session.durationMinutes)}.`,
          ],
        };
      case 'swap': {
        // The occupant goes back to the pool; the dragged session takes its room and start time.
        const target = drop.target;
        return {
          edits: [
            { action: 'delete', sessionid: target.sessionid, scheduleid: target.scheduleid },
            { action: 'insert', sessionid: session.sessionid, roomid: target.roomid, startMinutes: target.startMinutes },
          ],
          landing: { roomid: target.roomid, startMinutes: target.startMinutes, durationMinutes: session.durationMinutes },
          optimisticSchedule: [
            ...schedule.filter((item) => item.scheduleid !== target.scheduleid),
            toScheduleItem(session, target.roomid, target.startMinutes),
          ],
          poolAdd: [toPoolEntry(target)],
          poolRemoveIds: [session.sessionid],
          counterpart: { sessionid: target.sessionid, destination: 'pool' },
          messages: [
            `${describeSession(target)} removed from the schedule and returned to the list.`,
            `${describeSession(session)} scheduled in ${describeSlot(ctx, target.roomid, target.startMinutes, session.durationMinutes)}.`,
          ],
        };
      }
      case 'cabinet':
        // Never scheduled, so there's nothing to change in the database -- it just leaves the pool.
        return { edits: [], optimisticSchedule: schedule, poolAdd: [], poolRemoveIds: [session.sessionid], messages: [] };
      case 'pool':
        return null;
    }
  }

  const item = source.item;
  const from = describeSlot(ctx, item.roomid, item.startMinutes, item.durationMinutes);
  switch (drop.kind) {
    case 'place':
      if (drop.roomid === item.roomid && drop.startMinutes === item.startMinutes) {
        return null;
      }
      return {
        edits: [
          {
            action: 'reschedule',
            sessionid: item.sessionid,
            scheduleid: item.scheduleid,
            roomid: drop.roomid,
            startMinutes: drop.startMinutes,
          },
        ],
        optimisticSchedule: moveItem(schedule, item.scheduleid, drop.roomid, drop.startMinutes),
        landing: { roomid: drop.roomid, startMinutes: drop.startMinutes, durationMinutes: item.durationMinutes },
        poolAdd: [],
        poolRemoveIds: [],
        messages: [
          `${describeSession(item)} moved from ${from} to ${describeSlot(ctx, drop.roomid, drop.startMinutes, item.durationMinutes)}.`,
        ],
      };
    case 'swap': {
      // Each session takes the other's room and start time (keeping its own duration).
      const target = drop.target;
      return {
        edits: [
          {
            action: 'reschedule',
            sessionid: item.sessionid,
            scheduleid: item.scheduleid,
            roomid: target.roomid,
            startMinutes: target.startMinutes,
          },
          {
            action: 'reschedule',
            sessionid: target.sessionid,
            scheduleid: target.scheduleid,
            roomid: item.roomid,
            startMinutes: item.startMinutes,
          },
        ],
        landing: { roomid: target.roomid, startMinutes: target.startMinutes, durationMinutes: item.durationMinutes },
        optimisticSchedule: moveItem(
          moveItem(schedule, item.scheduleid, target.roomid, target.startMinutes),
          target.scheduleid,
          item.roomid,
          item.startMinutes
        ),
        poolAdd: [],
        poolRemoveIds: [],
        counterpart: { sessionid: target.sessionid, destination: 'grid' },
        messages: [
          `${describeSession(item)} moved from ${from} to ${describeSlot(ctx, target.roomid, target.startMinutes, item.durationMinutes)}.`,
          `${describeSession(target)} moved from ${describeSlot(ctx, target.roomid, target.startMinutes, target.durationMinutes)} to ${describeSlot(ctx, item.roomid, item.startMinutes, target.durationMinutes)}.`,
        ],
      };
    }
    case 'cabinet':
    case 'pool': {
      // Both remove it from the schedule; only a drop on the pool puts it back in the list.
      const toPool = drop.kind === 'pool';
      return {
        edits: [{ action: 'delete', sessionid: item.sessionid, scheduleid: item.scheduleid }],
        optimisticSchedule: schedule.filter((other) => other.scheduleid !== item.scheduleid),
        poolAdd: toPool ? [toPoolEntry(item)] : [],
        poolRemoveIds: [],
        messages: [
          toPool
            ? `${describeSession(item)} removed from ${from} and returned to the list.`
            : `${describeSession(item)} removed from ${from}.`,
        ],
      };
    }
  }
}

export function applyPlanToPool(pool: SessionSearchResult[], plan: EditPlan): SessionSearchResult[] {
  const addIds = new Set(plan.poolAdd.map((session) => session.sessionid));
  const kept = pool.filter((session) => !plan.poolRemoveIds.includes(session.sessionid) && !addIds.has(session.sessionid));
  return [...plan.poolAdd, ...kept];
}

// Undoes applyPlanToPool() when the server didn't apply the plan. Removed sessions come back at the front of the
// list rather than their original position -- close enough, and it keeps them easy to find.
export function revertPlanFromPool(
  pool: SessionSearchResult[],
  plan: EditPlan,
  source: DragSource
): SessionSearchResult[] {
  const addIds = new Set(plan.poolAdd.map((session) => session.sessionid));
  const restored = source.kind === 'unscheduled' && plan.poolRemoveIds.includes(source.session.sessionid) ? [source.session] : [];
  return [...restored, ...pool.filter((session) => !addIds.has(session.sessionid))];
}

// Sessions whose room or start time differs between two server-confirmed schedules for reasons the user's own
// gesture didn't cause -- i.e. another staff member's edits, revealed when a save returns the fresh schedule. A
// session newly present in `after` counts too (it appeared on the grid without the user putting it there);
// sessions no longer present don't (there's nothing left on the grid to mark). Keyed by sessionid, since
// optimistic inserts carry temporary scheduleids. Pure, and indifferent to where `after` came from, so a future
// source of server schedules (e.g. polling) can reuse it with an empty touched set.
export function findUntouchedMoves(
  before: ScheduleItemData[],
  after: ScheduleItemData[],
  touchedSessionIds: Set<number>
): Set<number> {
  const beforeBySession = new Map(before.map((item) => [item.sessionid, item]));
  const moved = new Set<number>();
  for (const item of after) {
    if (touchedSessionIds.has(item.sessionid)) continue;
    const previous = beforeBySession.get(item.sessionid);
    if (!previous || previous.roomid !== item.roomid || previous.startMinutes !== item.startMinutes) {
      moved.add(item.sessionid);
    }
  }
  return moved;
}
