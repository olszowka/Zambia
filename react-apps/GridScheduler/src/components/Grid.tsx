import { useMemo } from 'react';
import type { BootstrapData, ScheduleItemData } from '../types';
import {
  buildAxisTicks,
  buildDisplaySegments,
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

export default function Grid({ data }: { data: BootstrapData }) {
  const displaySegments = useMemo(() => buildDisplaySegments(data.daySegments), [data.daySegments]);
  // The time axis always labels/lines on the hour, independent of the grid's resolution -- see
  // buildHourTicks()'s comment in timeGrid.ts. The room columns' empty-grid lines are a separate
  // concern and do follow the resolution (STANDARD_BLOCK_LENGTH today).
  const hourTicks = useMemo(() => buildHourTicks(displaySegments), [displaySegments]);
  const rowLineTicks = useMemo(
    () => buildAxisTicks(displaySegments, data.resolutionMinutes),
    [displaySegments, data.resolutionMinutes]
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
          gridTemplateColumns: `${TIME_COLUMN_WIDTH_PX}px repeat(${data.rooms.length}, ${ROOM_COLUMN_WIDTH_PX}px)`,
        }}
      >
        <div className="grid-scheduler-corner">Time</div>
        {data.rooms.map((room) => (
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
        {data.rooms.map((room) => {
          const items = layoutOverlaps(itemsByRoom.get(room.roomid) ?? []);
          return (
            <div className="grid-scheduler-room-column" style={{ height: totalHeightPx }} key={room.roomid}>
              {rowLineTicks.map((tick) => (
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
                  top={minutesToDisplayOffset(displaySegments, item.startMinutes) * PIXELS_PER_MINUTE}
                  height={item.durationMinutes * PIXELS_PER_MINUTE}
                />
              ))}
            </div>
          );
        })}
      </div>
    </div>
  );
}
