// Pixel geometry shared by the grid (components/Grid.tsx) and the drag-and-drop code that has to map a pointer
// position back onto the grid's timeline (dropTarget.ts, components/DragPreview.tsx).

// Tuned so a 60-minute session box (3 text rows: title, id/type, track -- see SessionBox.tsx) just fits
// its own content at the box's current padding/font-size/line-height, measured in Chrome against a real
// rendered box: 59.04px natural content height / 60 minutes.
export const PIXELS_PER_MINUTE = 0.984;

// Fixed, not flexible -- room columns (and un-overlapped session boxes) stay this width regardless of
// how many rooms are shown. Fewer rooms than fit the viewport leaves whitespace to the right; more
// rooms than fit scrolls horizontally (.grid-scheduler-scroll has overflow: auto). 260px matches
// the session box width used by the abandoned react_grid_scheduler branch's schedulableSessionStyle
// (ReactApp/zambia-grid-scheduler/src/render_sessions/sessionStyles.ts).
export const ROOM_COLUMN_WIDTH_PX = 260;
