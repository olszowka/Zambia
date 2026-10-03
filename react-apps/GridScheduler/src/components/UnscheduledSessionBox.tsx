import { useDraggable } from '@dnd-kit/core';
import type { DragSource, SessionSearchResult } from '../types';

interface UnscheduledSessionBoxProps {
  session: SessionSearchResult;
  onInfoClick: (sessionid: number) => void;
  dragDisabled: boolean;
  animationHidden: boolean;
}

// List-item variant of the same box styling used on the grid (.grid-scheduler-session-box) -- flows in
// normal document order instead of being pixel-positioned, via the
// .grid-scheduler-unscheduled-session-box modifier class (see App.css).
export default function UnscheduledSessionBox({
  session,
  onInfoClick,
  dragDisabled,
  animationHidden,
}: UnscheduledSessionBoxProps) {
  const dragSource: DragSource = { kind: 'unscheduled', session };
  const { setNodeRef, listeners, attributes, isDragging } = useDraggable({
    id: `unscheduled-${session.sessionid}`,
    data: dragSource,
    disabled: dragDisabled,
  });

  return (
    <div
      ref={setNodeRef}
      {...listeners}
      {...attributes}
      data-sessionid={session.sessionid}
      className={
        'grid-scheduler-session-box grid-scheduler-unscheduled-session-box' +
        (isDragging ? ' grid-scheduler-drag-source' : '') +
        (animationHidden ? ' grid-scheduler-animation-hidden' : '')
      }
      title={session.title}
    >
      <div className="grid-scheduler-session-title-row">
        <i
          className="icon-info-sign grid-scheduler-session-info-icon"
          onClick={() => onInfoClick(session.sessionid)}
        />
        <span className="grid-scheduler-session-title">{session.title}</span>
      </div>
      <div className="grid-scheduler-session-meta">
        <span className="grid-scheduler-session-id">{session.sessionid}</span>
        <span className="grid-scheduler-session-type">{`Type: ${session.typename}`}</span>
      </div>
      <div className="grid-scheduler-session-track">{`Track: ${session.trackname}`}</div>
    </div>
  );
}
