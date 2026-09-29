import { useMemo } from 'react';
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

// Tuned so a 60-minute session box (3 text rows: title, id/type, track -- see SessionBox.tsx) just fits
// its own content at the box's current padding/font-size/line-height, measured in Chrome against a real
// rendered box: 59.04px natural content height / 60 minutes.
const PIXELS_PER_MINUTE = 0.984;
// Display resolution: how precisely a scheduled session's position/duration is rendered. This is one of
// three distinct, similarly-named resolutions in the grid scheduler -- see the "Time resolutions"
// section of the rewrite plan:
//  1) grid-line resolution (STANDARD_BLOCK_LENGTH, admin-configurable) -- the empty-grid row lines,
//     gridLineResolutionMinutes below.
//  2) snap resolution -- a page-level Snap Mode control (Grid / 5 / 10 / 15 / 30 min), planned for the
//     drag-and-drop phase; where a drop lands.
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
// Fixed, not flexible -- room columns (and un-overlapped session boxes) stay this width regardless of
// how many rooms are shown. Fewer rooms than fit the viewport leaves whitespace to the right; more
// rooms than fit scrolls horizontally (.grid-scheduler-scroll below has overflow: auto). 260px matches
// the session box width used by the abandoned react_grid_scheduler branch's schedulableSessionStyle
// (ReactApp/zambia-grid-scheduler/src/render_sessions/sessionStyles.ts).
const ROOM_COLUMN_WIDTH_PX = 260;

interface GridProps {
  data: BootstrapData;
  visibleRoomIds: Set<number>;
  onInfoClick: (sessionid: number) => void;
}

export default function Grid({ data, visibleRoomIds, onInfoClick }: GridProps) {
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
    for (const item of data.schedule) {
      if (!map.has(item.roomid)) map.set(item.roomid, []);
      map.get(item.roomid)!.push(item);
    }
    return map;
  }, [data.rooms, data.schedule]);

  return (
    <div className="grid-scheduler-scroll">
      <div
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
            <div className="grid-scheduler-room-column" style={{ height: totalHeightPx }} key={room.roomid}>
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
                />
              ))}
            </div>
          );
        })}
      </div>
    </div>
  );
}
