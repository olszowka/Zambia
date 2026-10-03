// The outcome of the most recent schedule edit -- replaced on every edit, like the legacy page's
// editScheduleCallback() (webpages/javascript/staffMaintainSchedule.js).
export interface EditOutcome {
  kind: 'applied' | 'error';
  messages: string[];
  // Participant conflicts that were overridden (kind 'applied'), as rendered by check_room_sched_conflicts().
  conflictsHtml: string;
  // Overrides the default error heading ("The change was not saved.") -- e.g. for a failed refresh, which isn't
  // a change at all.
  heading?: string;
}

// An edit that was held back because it would create participant conflicts, awaiting staff confirmation.
export interface PendingConflict {
  messages: string[];
  conflictsHtml: string;
}

export interface WarningsTabProps {
  lastOutcome: EditOutcome | null;
  pendingConflict: PendingConflict | null;
  saving: boolean;
  onOverrideConflict: () => void;
  onCancelConflict: () => void;
}

function MessageList({ messages }: { messages: string[] }) {
  if (messages.length === 0) return null;
  return (
    <ul className="grid-scheduler-warnings-messages">
      {messages.map((message, i) => (
        <li key={i}>{message}</li>
      ))}
    </ul>
  );
}

export default function WarningsTab({
  lastOutcome,
  pendingConflict,
  saving,
  onOverrideConflict,
  onCancelConflict,
}: WarningsTabProps) {
  if (pendingConflict) {
    return (
      <div className="grid-scheduler-warnings-tab">
        <div className="grid-scheduler-warnings-heading">
          This change would cause the conflicts below. It has not been saved.
        </div>
        <MessageList messages={pendingConflict.messages} />
        {/* Server-rendered by check_room_sched_conflicts(), which HTML-escapes all user-entered text. */}
        <div dangerouslySetInnerHTML={{ __html: pendingConflict.conflictsHtml }} />
        <div className="grid-scheduler-warnings-buttons">
          <button type="button" className="btn btn-warning btn-sm" onClick={onOverrideConflict} disabled={saving}>
            Save Anyway
          </button>
          <button type="button" className="btn btn-secondary btn-sm" onClick={onCancelConflict} disabled={saving}>
            Cancel
          </button>
        </div>
      </div>
    );
  }
  if (saving) {
    return <div className="grid-scheduler-warnings-tab-empty">Saving…</div>;
  }
  if (!lastOutcome) {
    return <div className="grid-scheduler-warnings-tab-empty">No changes made yet.</div>;
  }
  if (lastOutcome.kind === 'error') {
    return (
      <div className="grid-scheduler-warnings-tab">
        <div className="grid-scheduler-warnings-error">{lastOutcome.heading ?? 'The change was not saved.'}</div>
        <MessageList messages={lastOutcome.messages} />
      </div>
    );
  }
  return (
    <div className="grid-scheduler-warnings-tab">
      <div className="grid-scheduler-warnings-heading">Saved.</div>
      <MessageList messages={lastOutcome.messages} />
      {lastOutcome.conflictsHtml && (
        <>
          <div className="grid-scheduler-warnings-heading">Saved despite these conflicts:</div>
          <div dangerouslySetInnerHTML={{ __html: lastOutcome.conflictsHtml }} />
        </>
      )}
    </div>
  );
}
