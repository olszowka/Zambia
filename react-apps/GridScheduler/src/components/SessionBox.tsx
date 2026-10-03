import { useDraggable } from '@dnd-kit/core';
import type { LaidOutItem } from '../timeGrid';
import type { DragSource, TrackTagUsage } from '../types';

interface SessionBoxProps {
  item: LaidOutItem;
  trackTagUsage: TrackTagUsage;
  top: number;
  height: number;
  onInfoClick: (sessionid: number) => void;
  dragDisabled: boolean;
  highlighted: boolean;
  animationHidden: boolean;
}

export default function SessionBox({
  item,
  trackTagUsage,
  top,
  height,
  onInfoClick,
  dragDisabled,
  highlighted,
  animationHidden,
}: SessionBoxProps) {
  // colIndex/colCount are layout-only; the drag source carries the plain schedule row.
  const { colIndex, colCount, ...scheduleItem } = item;
  const dragSource: DragSource = { kind: 'scheduled', item: scheduleItem };
  const { setNodeRef, listeners, attributes, isDragging } = useDraggable({
    id: `scheduled-${item.scheduleid}`,
    data: dragSource,
    disabled: dragDisabled,
  });
  const widthPct = 100 / colCount;
  const leftPct = colIndex * widthPct;
  // Tags aren't in the read-only bootstrap payload yet (planned for the config-driven session-box
  // phase, along with respecting TAG_OVER_TRACK/TRACK_OVER_TAG choice of primary field) -- for now,
  // just honor TAG_ONLY/TRACK_ONLY's "hide the other field" instruction where we can.
  const showTrack = trackTagUsage !== 'TAG_ONLY';

  return (
    <div
      ref={setNodeRef}
      {...listeners}
      {...attributes}
      // data-drop-kind/-scheduleid: lets dropTarget.ts's hit-testing recognize this box as a Swap Mode target.
      data-drop-kind="session"
      data-scheduleid={item.scheduleid}
      data-sessionid={item.sessionid}
      className={
        'grid-scheduler-session-box' +
        (isDragging ? ' grid-scheduler-drag-source' : '') +
        (highlighted ? ' grid-scheduler-session-box-moved' : '') +
        (animationHidden ? ' grid-scheduler-animation-hidden' : '')
      }
      style={{ top, height, left: `${leftPct}%`, width: `${widthPct}%` }}
      title={item.title}
    >
      <div className="grid-scheduler-session-title-row">
        <i
          className="icon-info-sign grid-scheduler-session-info-icon"
          onClick={() => onInfoClick(item.sessionid)}
        />
        <span className="grid-scheduler-session-title">{item.title}</span>
      </div>
      <div className="grid-scheduler-session-meta">
        <span className="grid-scheduler-session-id">{item.sessionid}</span>
        <span className="grid-scheduler-session-type">{`Type: ${item.typename}`}</span>
      </div>
      {showTrack && <div className="grid-scheduler-session-track">{`Track: ${item.trackname}`}</div>}
    </div>
  );
}
