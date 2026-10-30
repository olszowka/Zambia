import type { EditScheduleResponse, ScheduleEdit, ScheduleItemData, SessionInfoData, SessionSearchResult } from './types';

// The PHP backend (webpages/GridSchedulerAjax.php) reads parameters via getString()/getInt()/etc., which
// only look at $_GET/$_POST -- so requests must be form-encoded, not a raw JSON body. Mirrors
// react-apps/ConfigurePermissions/src/api.ts's postAction() pattern.
async function postForm<T>(body: URLSearchParams): Promise<T> {
  const response = await fetch('GridSchedulerAjax.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body,
  });
  // GridSchedulerAjax.php sets $return500errors, so RenderErrorAjax() responds with a 500 and a plain-text
  // (not JSON) message body.
  if (!response.ok) {
    const text = (await response.text()).replace(/<br>\s*/g, ' ').trim();
    throw new Error(text || `Request failed (${response.status}).`);
  }
  const json = await response.json();
  if (json.error) {
    throw new Error(json.error);
  }
  return json as T;
}

async function postAction<T>(action: string, params: Record<string, string | number | boolean> = {}): Promise<T> {
  const body = new URLSearchParams();
  body.set('ajax_request_action', action);
  for (const [key, value] of Object.entries(params)) {
    body.set(key, String(value));
  }
  return postForm<T>(body);
}

// getArrayOfInts() (webpages/data_functions.php) expects repeated "name[]=..." fields, which PHP collects
// into $_POST['name'] as an array -- URLSearchParams.append() (not .set()) produces that shape.
function appendIntArray(body: URLSearchParams, fieldName: string, ids: number[]) {
  ids.forEach((id) => body.append(`${fieldName}[]`, String(id)));
}

export interface SearchSessionsParams {
  currSessionIds: number[];
  trackId: number;
  tagIds: number[];
  tagmatch: 'any' | 'all';
  typeId: number;
  divisionId: number;
  sessionId: number | null;
  title: string;
  personsAssigned: boolean;
}

export async function searchSessions(params: SearchSessionsParams): Promise<SessionSearchResult[]> {
  const body = new URLSearchParams();
  body.set('ajax_request_action', 'searchSessions');
  appendIntArray(body, 'currSessionIdArray', params.currSessionIds);
  body.set('trackId', String(params.trackId));
  appendIntArray(body, 'tagIds', params.tagIds);
  body.set('tagmatch', params.tagmatch);
  body.set('typeId', String(params.typeId));
  body.set('divisionId', String(params.divisionId));
  if (params.sessionId !== null) {
    body.set('sessionId', String(params.sessionId));
  }
  body.set('title', params.title);
  body.set('personsAssigned', params.personsAssigned ? '1' : '0');
  return (await postForm<{ sessions: SessionSearchResult[] }>(body)).sessions;
}

export async function getSchedule(): Promise<ScheduleItemData[]> {
  return (await postAction<{ schedule: ScheduleItemData[] }>('getSchedule')).schedule;
}

export function getSessionInfo(sessionid: number): Promise<SessionInfoData> {
  return postAction<SessionInfoData>('getSessionInfo', { sessionid });
}

// Sends one drag-and-drop gesture's edits. The edits go as a single JSON-encoded field since they vary in shape
// (see gridScheduler_normalizeEdits() in webpages/gridScheduler_functions.php).
export function editSchedule(edits: ScheduleEdit[], ignoreConflicts: boolean): Promise<EditScheduleResponse> {
  return postAction<EditScheduleResponse>('editSchedule', {
    edits: JSON.stringify(edits),
    ignoreConflicts: ignoreConflicts ? 1 : 0,
  });
}
