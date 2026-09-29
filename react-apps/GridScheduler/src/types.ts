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
  // conflate with snap resolution (a page-level Snap Mode control, planned for the drag-and-drop phase)
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
