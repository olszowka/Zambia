import { useRef, useState } from 'react';

// Drag is only initiated from a handle (via onMouseDown/onMouseUp on the handle element),
// not by grabbing anywhere in the row, even though the whole <tr> carries `draggable`.
export function useDragReorder<T>(items: T[], getId: (item: T) => number, onReorder: (orderedIds: number[]) => void) {
  const dragIndex = useRef<number | null>(null);
  const handleGrabbed = useRef(false);
  const [overIndex, setOverIndex] = useState<number | null>(null);

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
      onDragOver: (e: React.DragEvent) => {
        if (dragIndex.current === null) {
          return;
        }
        e.preventDefault();
        setOverIndex(index);
      },
      onDragLeave: () => {
        setOverIndex((prev) => (prev === index ? null : prev));
      },
      onDrop: (e: React.DragEvent) => {
        e.preventDefault();
        const from = dragIndex.current;
        dragIndex.current = null;
        setOverIndex(null);
        if (from === null || from === index) {
          return;
        }
        const reordered = [...items];
        const [moved] = reordered.splice(from, 1);
        reordered.splice(index, 0, moved);
        onReorder(reordered.map(getId));
      },
      onDragEnd: () => {
        handleGrabbed.current = false;
        dragIndex.current = null;
        setOverIndex(null);
      },
      className: overIndex === index ? 'drag-over-row' : undefined,
    };
  }

  return { getRowProps, getHandleProps };
}
