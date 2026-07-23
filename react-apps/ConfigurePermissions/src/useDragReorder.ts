import { useRef, useState } from 'react';

type DropPosition = 'before' | 'after';

interface DropTarget {
  index: number;
  position: DropPosition;
}

function dropPositionForEvent(e: React.DragEvent<HTMLTableRowElement>): DropPosition {
  const rect = e.currentTarget.getBoundingClientRect();
  return e.clientY - rect.top < rect.height / 2 ? 'before' : 'after';
}

// Drag is only initiated from a handle (via onMouseDown/onMouseUp on the handle element),
// not by grabbing anywhere in the row, even though the whole <tr> carries `draggable`.
//
// The drop indicator tracks which half of the hovered row the cursor is over, not just which
// row: hovering the top half means "insert before this row" (indicator on its top edge),
// hovering the bottom half means "insert after this row" (indicator on its bottom edge). This
// is what lets the very end of the list -- past the last row's midpoint -- be a valid drop
// target, shown as a line under the last row.
export function useDragReorder<T>(items: T[], getId: (item: T) => number, onReorder: (orderedIds: number[]) => void) {
  const dragIndex = useRef<number | null>(null);
  const handleGrabbed = useRef(false);
  const [dropTarget, setDropTarget] = useState<DropTarget | null>(null);

  function getHandleProps() {
    return {
      onMouseDown: () => {
        handleGrabbed.current = true;
      },
      onMouseUp: () => {
        handleGrabbed.current = false;
      },
    };
  }

  function getRowProps(index: number) {
    return {
      draggable: true,
      onDragStart: (e: React.DragEvent) => {
        if (!handleGrabbed.current) {
          e.preventDefault();
          return;
        }
        dragIndex.current = index;
        e.dataTransfer.effectAllowed = 'move';
      },
      onDragOver: (e: React.DragEvent<HTMLTableRowElement>) => {
        if (dragIndex.current === null) {
          return;
        }
        e.preventDefault();
        const position = dropPositionForEvent(e);
        setDropTarget((prev) => (prev && prev.index === index && prev.position === position ? prev : { index, position }));
      },
      onDragLeave: () => {
        setDropTarget((prev) => (prev && prev.index === index ? null : prev));
      },
      onDrop: (e: React.DragEvent<HTMLTableRowElement>) => {
        e.preventDefault();
        const from = dragIndex.current;
        dragIndex.current = null;
        setDropTarget(null);
        if (from === null) {
          return;
        }
        const position = dropPositionForEvent(e);
        // dropIndex is the position (in the original, pre-removal array) before which the
        // dragged item should land -- ranging from 0 (very start) to items.length (very end).
        const dropIndex = position === 'before' ? index : index + 1;
        // Removing `from` shifts everything after it down by one, so the insertion index into
        // the post-removal array needs adjusting whenever the drop point was after the source.
        const insertionIndex = dropIndex > from ? dropIndex - 1 : dropIndex;
        if (insertionIndex === from) {
          return;
        }
        const reordered = [...items];
        const [moved] = reordered.splice(from, 1);
        reordered.splice(insertionIndex, 0, moved);
        onReorder(reordered.map(getId));
      },
      onDragEnd: () => {
        handleGrabbed.current = false;
        dragIndex.current = null;
        setDropTarget(null);
      },
      className:
        dropTarget && dropTarget.index === index
          ? dropTarget.position === 'before'
            ? 'drag-over-top'
            : 'drag-over-bottom'
          : undefined,
    };
  }

  return { getRowProps, getHandleProps };
}
