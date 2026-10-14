import type { ScheduleItemData, SessionBoxField, SessionSearchResult } from '../types';

interface SessionBoxContentProps {
  session: ScheduleItemData | SessionSearchResult;
  // From BootstrapData.sessionBoxFields (GRID_SESSION_BOX_FIELDS, or a TRACK_TAG_USAGE-based default).
  fields: SessionBoxField[][];
  // Omitted for the drag overlay, whose icon is just for looks.
  onInfoClick?: (sessionid: number) => void;
}

function formatDuration(minutes: number): string {
  const remainder = minutes % 60;
  return `${Math.floor(minutes / 60)}:${remainder < 10 ? '0' : ''}${remainder}`;
}

// [class-name suffix, text] for one field.
function fieldContent(field: SessionBoxField, session: ScheduleItemData | SessionSearchResult): [string, string] {
  switch (field) {
    case 'title':
      return ['title', session.title];
    case 'sessionid':
      return ['id', String(session.sessionid)];
    case 'type':
      return ['type', `Type: ${session.typename}`];
    case 'track':
      return ['track', `Track: ${session.trackname}`];
    case 'tags':
      return ['tags', `Tags: ${session.tagnames.join(', ')}`];
    case 'division':
      return ['division', `Division: ${session.divisionname}`];
    case 'duration':
      return ['duration', `Duration: ${formatDuration(session.durationMinutes)}`];
  }
}

// The inside of a session box, shared by the grid (SessionBox), the "sessions to be scheduled" list
// (UnscheduledSessionBox), and the drag overlay (DragPreview), so all three always show the same fields. Each line is
// a single line of text, clipped with an ellipsis; the box itself clips lines that don't fit its height.
export default function SessionBoxContent({ session, fields, onInfoClick }: SessionBoxContentProps) {
  return (
    <>
      {fields.map((line, lineIndex) => (
        <div className="grid-scheduler-session-line" key={lineIndex}>
          {lineIndex === 0 && (
            <i
              className="icon-info-sign grid-scheduler-session-info-icon"
              onClick={onInfoClick && (() => onInfoClick(session.sessionid))}
            />
          )}
          {line.map((field) => {
            const [classSuffix, text] = fieldContent(field, session);
            return (
              <span className={`grid-scheduler-session-field grid-scheduler-session-${classSuffix}`} key={field}>
                {text}
              </span>
            );
          })}
        </div>
      ))}
    </>
  );
}
