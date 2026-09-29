import type { SessionSearchResult } from '../types';

interface UnscheduledSessionBoxProps {
  session: SessionSearchResult;
  onInfoClick: (sessionid: number) => void;
}

// List-item variant of the same box styling used on the grid (.grid-scheduler-session-box) -- flows in
// normal document order instead of being pixel-positioned, via the
// .grid-scheduler-unscheduled-session-box modifier class (see App.css).
export default function UnscheduledSessionBox({ session, onInfoClick }: UnscheduledSessionBoxProps) {
  return (
    <div
      className="grid-scheduler-session-box grid-scheduler-unscheduled-session-box"
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
