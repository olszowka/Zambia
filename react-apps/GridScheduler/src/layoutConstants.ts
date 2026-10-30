// Pixel geometry shared by the grid (components/Grid.tsx) and the drag-and-drop code that has to map a pointer
// position back onto the grid's timeline (dropTarget.ts, components/DragPreview.tsx).

// Tuned so a 60-minute session box with 3 text rows (as in the default GRID_SESSION_BOX_FIELDS -- see
// SessionBoxContent.tsx) just fits its own content at the box's current padding/font-size/line-height, measured in
// Chrome against a real rendered box: 59.04px natural content height / 60 minutes.
export const PIXELS_PER_MINUTE = 0.984;

// Fixed, not flexible -- room columns (and un-overlapped session boxes) stay this width regardless of
// how many rooms are shown. Fewer rooms than fit the viewport leaves whitespace to the right; more
// rooms than fit scrolls horizontally (.grid-scheduler-scroll has overflow: auto). 260px matches
// the session box width used by the abandoned react_grid_scheduler branch's schedulableSessionStyle
// (ReactApp/zambia-grid-scheduler/src/render_sessions/sessionStyles.ts).
export const ROOM_COLUMN_WIDTH_PX = 260;

// The sticky time axis column at the grid's left edge. Wide enough for the longest day name alone ("Wednesday",
// ~80px at the axis's font-size -- day and time are never on the same line, see buildHourTicks()) to fit on one
// line -- too narrow and it wraps to two lines and collides with the tick below it. Also offsets the left edge
// auto-scroll band (App.tsx's autoScrollOptionsFor()), since the axis covers the grid's leftmost pixels.
export const TIME_COLUMN_WIDTH_PX = 90;
