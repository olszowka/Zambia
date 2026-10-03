export type TrackTagUsage = 'TAG_ONLY' | 'TAG_OVER_TRACK' | 'TRACK_OVER_TAG' | 'TRACK_ONLY';

export type TabKey = 'rooms' | 'sessions' | 'warnings' | 'info';

export interface RoomData {
  roomid: number;
  roomname: string;
  display_order: number | null;
}

export interface ScheduleItemData {
  scheduleid: number;
  roomid: number;
  sessionid: number;
  title: string;
  // Minutes since conStartDateTime -- matches the Schedule table's starttime column, which is stored
  // as elapsed time since the con started (so it can exceed 24 hours for overnight sessions), not a
  // wall-clock time of day. See webpages/gridScheduler_functions.php.
  startMinutes: number;
  durationMinutes: number;
  trackname: string;
  typename: string;
  divisionname: string;
}

export interface DaySegment {
  day: number;
  dayName: string;
  // Both in minutes since conStartDateTime, same units as ScheduleItemData.startMinutes.
  startMinutes: number;
  endMinutes: number;
}

// A named (id/name) lookup entry -- Tracks, Tags, Types, and Divisions all shape the same way for the
// Sessions tab's filter dropdowns.
export interface LookupEntry {
  id: number;
  name: string;
}

export interface BootstrapData {
  conStartDateTime: string;
  conNumDays: number;
  // Grid-line resolution (STANDARD_BLOCK_LENGTH today) -- one of three distinct, similarly-named
  // resolutions in the grid scheduler; see the "Time resolutions" section of the rewrite plan. Do not
  // conflate with snap resolution (the page-level Snap Mode control -- see computeDropStart() in dropTarget.ts)
  // or display resolution (DISPLAY_RESOLUTION_MINUTES in components/Grid.tsx).
  gridLineResolutionMinutes: number;
  trackTagUsage: TrackTagUsage;
  daySegments: DaySegment[];
  rooms: RoomData[];
  schedule: ScheduleItemData[];
  tracks: LookupEntry[];
  tags: LookupEntry[];
  types: LookupEntry[];
  divisions: LookupEntry[];
}

// One unscheduled session, as returned by the "searchSessions" action -- shown in the "sessions to be
// scheduled" list. Distinct from ScheduleItemData (which additionally carries where/when it's scheduled)
// even though the fields overlap, since an unscheduled session has no scheduleid/roomid/startMinutes.
export interface SessionSearchResult {
  sessionid: number;
  title: string;
  trackname: string;
  typename: string;
  divisionname: string;
  durationMinutes: number;
}

export interface SessionInfoParticipant {
  moderator: boolean;
  badgename: string;
  badgeid: string;
  participantname: string;
}

// Full detail for one session, as returned by the "getSessionInfo" action -- shown in the Info tab.
export interface SessionInfoData {
  sessionid: number;
  title: string;
  progguiddesc: string | null;
  notesforprog: string | null;
  trackname: string;
  typename: string;
  divisionname: string;
  duration: string;
  tagNames: string[];
  scheduled: boolean;
  roomname: string | null;
  starttime: string | null;
  endtime: string | null;
  participants: SessionInfoParticipant[];
}

// Snap resolution -- one of three distinct, similarly-named resolutions in the grid scheduler; see the "Time
// resolutions" section of the rewrite plan. A page-level UI choice (the Snap selector in the Options menu), not admin
// config. 'grid' snaps to empty grid rows/sub-slots (grid-line resolution plus adjacent sessions' edges -- see
// computeDropStart() in dropTarget.ts); a number snaps to that fixed N-minute boundary.
export type SnapMode = 'grid' | 5 | 10 | 15 | 30;

// What's being dragged: either a session already on the grid, or one from the "sessions to be scheduled" pool.
export type DragSource =
  | { kind: 'scheduled'; item: ScheduleItemData }
  | { kind: 'unscheduled'; session: SessionSearchResult };

// Where a drag would land if released right now -- resolved continuously while dragging (for the live drop-time
// label and drop preview) and once more on release. See resolveDrop() in dropTarget.ts.
export type DropResolution =
  | { kind: 'place'; roomid: number; startMinutes: number }
  | { kind: 'swap'; target: ScheduleItemData }
  | { kind: 'cabinet' }
  | { kind: 'pool' };

// One edit sent to the editSchedule action -- see gridScheduler_normalizeEdits() in
// webpages/gridScheduler_functions.php for the server-side counterpart.
export type ScheduleEdit =
  | { action: 'insert'; sessionid: number; roomid: number; startMinutes: number }
  | { action: 'reschedule'; sessionid: number; scheduleid: number; roomid: number; startMinutes: number }
  | { action: 'delete'; sessionid: number; scheduleid: number };

export interface EditScheduleResponse {
  // 'conflicts': nothing was written; conflictsHtml lists them, and the edit can be resent with ignoreConflicts.
  // 'stale': nothing was written, since another user changed something this edit depended on; see message.
  status: 'applied' | 'conflicts' | 'stale';
  // Server-rendered by check_room_sched_conflicts() (webpages/SubmitMaintainRoom.php), with all user-entered
  // text HTML-escaped there. Also populated on 'applied' when conflicts were overridden.
  conflictsHtml: string;
  message: string;
  schedule: ScheduleItemData[];
}
