interface Props {
  onMouseDown: () => void;
  onMouseUp: () => void;
}

// Same visual convention as the Configuration Table Editor's row-move handle
// (webpages/javascript/EditConfigTables.js, Tabulator's `rowHandle: true` column).
export default function DragHandle({ onMouseDown, onMouseUp }: Props) {
  return (
    <span
      className="drag-handle"
      onMouseDown={onMouseDown}
      onMouseUp={onMouseUp}
      title="Drag to reorder"
      aria-label="Drag to reorder"
    >
      <span className="drag-handle-bar" />
      <span className="drag-handle-bar" />
      <span className="drag-handle-bar" />
    </span>
  );
}
