import type { SessionInfoData } from '../../types';

interface InfoTabProps {
  info: SessionInfoData | null;
  loading: boolean;
}

export default function InfoTab({ info, loading }: InfoTabProps) {
  if (loading) {
    return <div className="grid-scheduler-info-tab-empty">Loading…</div>;
  }
  if (!info) {
    return (
      <div className="grid-scheduler-info-tab-empty">Click the info icon on a session to see its details.</div>
    );
  }

  const moderator = info.participants.find((p) => p.moderator);
  const otherParticipants = info.participants.filter((p) => !p.moderator);

  return (
    <div className="grid-scheduler-info-tab">
      <div className="infolabel">Title:</div>
      <div className="infofield">{info.title}</div>
      <div className="infolabel">Description:</div>
      <div className="infofield">{info.progguiddesc || ''}</div>
      <div>
        <span className="infolabel">Session ID: </span>
        <span className="infofield">{info.sessionid}</span>
      </div>
      <div>
        <span className="infolabel">Track: </span>
        <span className="infofield">{info.trackname}</span>
      </div>
      <div>
        <span className="infolabel">Tags: </span>
        <span className="infofield">{info.tagNames.join(', ')}</span>
      </div>
      <div>
        <span className="infolabel">Type: </span>
        <span className="infofield">{info.typename}</span>
      </div>
      <div>
        <span className="infolabel">Division: </span>
        <span className="infofield">{info.divisionname}</span>
      </div>
      <div>
        <span className="infolabel">Duration: </span>
        <span className="infofield">{info.duration}</span>
      </div>
      {info.notesforprog ? (
        <>
          <div className="infolabel">Notes for programming:</div>
          <div className="infofield">{info.notesforprog}</div>
        </>
      ) : (
        <div className="infolabel">No notes for programming</div>
      )}
      {info.scheduled ? (
        <>
          <div className="infolabel">Scheduled:</div>
          <div className="inforow">
            {info.starttime} - {info.endtime}
          </div>
          <div className="inforow">in {info.roomname}</div>
        </>
      ) : (
        <div className="infolabel">Not scheduled</div>
      )}
      {info.participants.length > 0 ? (
        <>
          {moderator ? (
            <>
              <div className="infolabel">Moderator</div>
              <div className="inforow">
                {moderator.badgename} ({moderator.badgeid}) {moderator.participantname}
              </div>
            </>
          ) : (
            <div className="infolabel">No moderator assigned</div>
          )}
          <div className="infolabel">Participants</div>
          {otherParticipants.map((p) => (
            <div className="inforow" key={p.badgeid}>
              {p.badgename} ({p.badgeid}) {p.participantname}
            </div>
          ))}
        </>
      ) : (
        <div className="infolabel">No participants assigned</div>
      )}
    </div>
  );
}
