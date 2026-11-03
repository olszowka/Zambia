import { useDraggable } from '@dnd-kit/core';
import type { DragSource, SessionBoxField, SessionSearchResult } from '../types';
import SessionBoxContent from './SessionBoxContent';

interface UnscheduledSessionBoxProps {
  session: SessionSearchResult;
  fields: SessionBoxField[][];
  onInfoClick: (sessionid: number) => void;
  dragDisabled: boolean;
  animationHidden: boolean;
}

// List-item variant of the same box styling used on the grid (.grid-scheduler-session-box) -- flows in
// normal document order instead of being pixel-positioned, via the
// .grid-scheduler-unscheduled-session-box modifier class (see App.css). Unlike a grid box, whose height follows the
// session's duration, it's always 3 lines tall, clipping any further lines.
export default function UnscheduledSessionBox({
  session,
  fields,
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
      <SessionBoxContent session={session} fields={fields} onInfoClick={onInfoClick} />
    </div>
  );
}
