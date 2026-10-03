import { useMemo } from 'react';
import { useDroppable } from '@dnd-kit/core';
import type { BootstrapData, ScheduleItemData } from '../types';
import {
  buildDisplaySegments,
  buildGridLineTicks,
  buildHourTicks,
  layoutOverlaps,
  minutesToDisplayOffset,
  totalDisplayMinutes,
} from '../timeGrid';
import SessionBox from './SessionBox';
import { PIXELS_PER_MINUTE, ROOM_COLUMN_WIDTH_PX } from '../layoutConstants';

// Display resolution: how precisely a scheduled session's position/duration is rendered. This is one of
// three distinct, similarly-named resolutions in the grid scheduler -- see the "Time resolutions"
// section of the rewrite plan:
//  1) grid-line resolution (STANDARD_BLOCK_LENGTH, admin-configurable) -- the empty-grid row lines,
//     gridLineResolutionMinutes below.
//  2) snap resolution -- the page-level Snap Mode control (Grid / 5 / 10 / 15 / 30 min); where a drop lands.
//     See computeDropStart() in dropTarget.ts.
//  3) display resolution (this constant) -- how finely an already-scheduled session renders. Fixed and
//     NOT configurable, unlike the other two. The legacy implementation could only render to the nearest
//     30 minutes; this rewrite renders to the nearest minute.
const DISPLAY_RESOLUTION_MINUTES = 1;

function roundToDisplayResolution(minutes: number): number {
  return Math.round(minutes / DISPLAY_RESOLUTION_MINUTES) * DISPLAY_RESOLUTION_MINUTES;
}
// Wide enough for the longest day name alone ("Wednesday", ~80px at the axis's font-size -- day and
// time are never on the same line, see buildHourTicks()) to fit on one line -- too narrow and it wraps
// to two lines and collides with the tick below it.
const TIME_COLUMN_WIDTH_PX = 90;

// Where the session being dragged would land, drawn as an outline in its target room column.
export interface DropPreview {
  roomid: number;
  startMinutes: number;
  durationMinutes: number;
}

interface GridProps {
  data: BootstrapData;
  // Kept separately from data.schedule (the bootstrap snapshot), since drag-and-drop edits change it.
  schedule: ScheduleItemData[];
  visibleRoomIds: Set<number>;
  onInfoClick: (sessionid: number) => void;
  dropPreview: DropPreview | null;
  dragDisabled: boolean;
  // Sessions to draw with the "moved without being touched" highlight -- see findUntouchedMoves().
  movedSessionIds: Set<number>;
  // A swap counterpart mid-animation -- its real box is hidden until the flying clone arrives (swapAnimation.ts).
  animatingSessionId: number | null;
}

export default function Grid({
  data,
  schedule,
  visibleRoomIds,
  onInfoClick,
  dropPreview,
  dragDisabled,
  movedSessionIds,
  animatingSessionId,
}: GridProps) {
  // The whole grid is registered as a single @dnd-kit droppable purely so its auto-scroll kicks in: @dnd-kit
  // auto-scrolls the scrollable ancestors of whatever droppable the pointer is over (falling back to the
  // dragged node's own ancestors), so without this, a session dragged in from the pool -- whose ancestors are
  // the side panel's -- would never scroll the grid. The ancestor search starts above the droppable node
  // itself, which is why this is the inner grid rather than .grid-scheduler-scroll. Actual drop targeting is
  // done by dropTarget.ts's own hit-testing, not @dnd-kit's collision detection.
  const { setNodeRef: setGridNodeRef } = useDroppable({ id: 'grid-scheduler-grid' });
  const visibleRooms = useMemo(
    () => data.rooms.filter((room) => visibleRoomIds.has(room.roomid)),
    [data.rooms, visibleRoomIds]
  );
  const displaySegments = useMemo(() => buildDisplaySegments(data.daySegments), [data.daySegments]);
  // The time axis always labels/lines on the hour, independent of grid-line resolution -- see
  // buildHourTicks()'s comment in timeGrid.ts. The room columns' empty-grid lines are a separate
  // concern and do follow grid-line resolution (STANDARD_BLOCK_LENGTH today).
  const hourTicks = useMemo(() => buildHourTicks(displaySegments), [displaySegments]);
  const gridLineTicks = useMemo(
    () => buildGridLineTicks(displaySegments, data.gridLineResolutionMinutes),
    [displaySegments, data.gridLineResolutionMinutes]
  );
  const totalHeightPx = totalDisplayMinutes(displaySegments) * PIXELS_PER_MINUTE;

  const itemsByRoom = useMemo(() => {
    const map = new Map<number, ScheduleItemData[]>();
    for (const room of data.rooms) map.set(room.roomid, []);
    for (const item of schedule) {
      if (!map.has(item.roomid)) map.set(item.roomid, []);
      map.get(item.roomid)!.push(item);
    }
    return map;
  }, [data.rooms, schedule]);

  return (
    <div className="grid-scheduler-scroll">
      <div
        ref={setGridNodeRef}
        className="grid-scheduler-grid"
        style={{
          // repeat(0, ...) is invalid CSS (repeat's count must be >= 1) -- with zero visible rooms it
          // would invalidate the whole gridTemplateColumns declaration, falling back to auto-sized
          // implicit columns (collapsing the axis column instead of holding it at TIME_COLUMN_WIDTH_PX,
          // and not reliably recovering back to the explicit template when a room is toggled back off
          // afterward). Omit the repeat() entirely when there are no rooms to show instead.
          gridTemplateColumns:
            visibleRooms.length === 0
              ? `${TIME_COLUMN_WIDTH_PX}px`
              : `${TIME_COLUMN_WIDTH_PX}px repeat(${visibleRooms.length}, ${ROOM_COLUMN_WIDTH_PX}px)`,
        }}
      >
        <div className="grid-scheduler-corner">Time</div>
        {visibleRooms.map((room) => (
          <div className="grid-scheduler-room-header" key={room.roomid}>
            {room.roomname}
          </div>
        ))}

        <div className="grid-scheduler-axis" style={{ height: totalHeightPx }}>
          {hourTicks.map((tick) => (
            <div
              key={tick.displayOffset}
              className={
                'grid-scheduler-axis-tick' + (tick.isSegmentStart ? ' grid-scheduler-axis-tick-day-start' : '')
              }
              style={{ top: tick.displayOffset * PIXELS_PER_MINUTE }}
            >
              {tick.label}
            </div>
          ))}
        </div>
        {visibleRooms.map((room) => {
          const items = layoutOverlaps(itemsByRoom.get(room.roomid) ?? []);
          return (
            <div
              className="grid-scheduler-room-column"
              style={{ height: totalHeightPx }}
              key={room.roomid}
              data-drop-kind="room"
              data-roomid={room.roomid}
            >
              {gridLineTicks.map((tick) => (
                <div
                  key={tick.displayOffset}
                  className={
                    'grid-scheduler-row-line' + (tick.isSegmentStart ? ' grid-scheduler-row-line-day-start' : '')
                  }
                  style={{ top: tick.displayOffset * PIXELS_PER_MINUTE }}
                />
              ))}
              {items.map((item) => (
                <SessionBox
                  key={item.scheduleid}
                  item={item}
                  trackTagUsage={data.trackTagUsage}
                  top={
                    minutesToDisplayOffset(displaySegments, roundToDisplayResolution(item.startMinutes)) *
                    PIXELS_PER_MINUTE
                  }
                  height={roundToDisplayResolution(item.durationMinutes) * PIXELS_PER_MINUTE}
                  onInfoClick={onInfoClick}
                  dragDisabled={dragDisabled}
                  highlighted={movedSessionIds.has(item.sessionid)}
                  animationHidden={animatingSessionId === item.sessionid}
                />
              ))}
              {dropPreview && dropPreview.roomid === room.roomid && (
                <div
                  className="grid-scheduler-drop-preview"
                  style={{
                    top: minutesToDisplayOffset(displaySegments, dropPreview.startMinutes) * PIXELS_PER_MINUTE,
                    height: dropPreview.durationMinutes * PIXELS_PER_MINUTE,
                  }}
                />
              )}
            </div>
          );
        })}
      </div>
    </div>
  );
}
