export type TrackTagUsage = 'TAG_ONLY' | 'TAG_OVER_TRACK' | 'TRACK_OVER_TAG' | 'TRACK_ONLY';

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

export interface BootstrapData {
  conStartDateTime: string;
  conNumDays: number;
  resolutionMinutes: number;
  trackTagUsage: TrackTagUsage;
  daySegments: DaySegment[];
  rooms: RoomData[];
  schedule: ScheduleItemData[];
}
