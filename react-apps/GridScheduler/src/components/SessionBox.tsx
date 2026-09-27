import type { LaidOutItem } from '../timeGrid';
import type { TrackTagUsage } from '../types';

interface SessionBoxProps {
  item: LaidOutItem;
  trackTagUsage: TrackTagUsage;
  top: number;
  height: number;
}

export default function SessionBox({ item, trackTagUsage, top, height }: SessionBoxProps) {
  const widthPct = 100 / item.colCount;
  const leftPct = item.colIndex * widthPct;
  // Tags aren't in the read-only bootstrap payload yet (planned for the config-driven session-box
  // phase, along with respecting TAG_OVER_TRACK/TRACK_OVER_TAG choice of primary field) -- for now,
  // just honor TAG_ONLY/TRACK_ONLY's "hide the other field" instruction where we can.
  const showTrack = trackTagUsage !== 'TAG_ONLY';

  return (
    <div
      className="grid-scheduler-session-box"
      style={{ top, height, left: `${leftPct}%`, width: `${widthPct}%` }}
      title={item.title}
    >
      <div className="grid-scheduler-session-title-row">
        {/* Not wired up yet (no Info tab to populate) -- see webpages/staffMaintainScheduleSubmit.php's
            "icon-info-sign getSessionInfoP" for the eventual click target this stands in for. */}
        <i className="icon-info-sign grid-scheduler-session-info-icon" />
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
