import type { DragSource, TrackTagUsage } from '../types';
import { PIXELS_PER_MINUTE, ROOM_COLUMN_WIDTH_PX } from '../layoutConstants';

interface DragPreviewProps {
  source: DragSource;
  trackTagUsage: TrackTagUsage;
  // Live drop-time feedback (e.g. "Fri 10:30a", "Swap with 123") -- null when a release here would do nothing.
  label: string | null;
}

// Rendered inside @dnd-kit's <DragOverlay>, following the pointer. Always drawn at grid scale (full room-column
// width, height proportional to duration) -- even when dragged out of the pool, whose list boxes have no
// duration-based height -- so staff can see how much of the grid the session will cover before dropping it.
export default function DragPreview({ source, trackTagUsage, label }: DragPreviewProps) {
  const session = source.kind === 'scheduled' ? source.item : source.session;
  const showTrack = trackTagUsage !== 'TAG_ONLY';
  return (
    <div className="grid-scheduler-drag-preview">
      {label && <div className="grid-scheduler-drop-label">{label}</div>}
      <div
        className="grid-scheduler-session-box grid-scheduler-drag-preview-box"
        style={{ width: ROOM_COLUMN_WIDTH_PX, height: session.durationMinutes * PIXELS_PER_MINUTE }}
      >
        <div className="grid-scheduler-session-title-row">
          <i className="icon-info-sign grid-scheduler-session-info-icon" />
          <span className="grid-scheduler-session-title">{session.title}</span>
        </div>
        <div className="grid-scheduler-session-meta">
          <span className="grid-scheduler-session-id">{session.sessionid}</span>
          <span className="grid-scheduler-session-type">{`Type: ${session.typename}`}</span>
        </div>
        {showTrack && <div className="grid-scheduler-session-track">{`Track: ${session.trackname}`}</div>}
      </div>
    </div>
  );
}
