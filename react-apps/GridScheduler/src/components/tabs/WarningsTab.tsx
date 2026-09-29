// Placeholder until the drag-and-drop phase, which is what actually populates this tab (confirmation
// messages and conflict warnings after each schedule edit -- see webpages/staffMaintainSchedule.js's
// editScheduleCallback()). No edits happen yet in this phase, so there's nothing to show here.
export default function WarningsTab() {
  return <div className="grid-scheduler-warnings-tab-empty">No warnings yet.</div>;
}
