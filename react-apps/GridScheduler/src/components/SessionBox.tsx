import type { CSSProperties } from 'react';
import { useDraggable } from '@dnd-kit/core';
import type { LaidOutItem } from '../timeGrid';
import type { DragSource, SessionBoxField } from '../types';
import SessionBoxContent from './SessionBoxContent';

interface SessionBoxProps {
  item: LaidOutItem;
  fields: SessionBoxField[][];
  top: number;
  height: number;
  // Set when Grid Simplification draws the box taller than the session really is (see simplifiedEnds() in
  // timeGrid.ts): the height of the real session, below which the box is shaded.
  realHeight: number | null;
  onInfoClick: (sessionid: number) => void;
  dragDisabled: boolean;
  highlighted: boolean;
  animationHidden: boolean;
}

export default function SessionBox({
  item,
  fields,
  top,
  height,
  realHeight,
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
  const style: CSSProperties & { '--grid-scheduler-real-height'?: string } = {
    top,
    height,
    left: `${leftPct}%`,
    width: `${widthPct}%`,
  };
  if (realHeight !== null) style['--grid-scheduler-real-height'] = `${realHeight}px`;

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
        (realHeight !== null ? ' grid-scheduler-session-box-simplified' : '') +
        (animationHidden ? ' grid-scheduler-animation-hidden' : '')
      }
      style={style}
      title={item.title}
    >
      <SessionBoxContent session={scheduleItem} fields={fields} onInfoClick={onInfoClick} />
    </div>
  );
}
