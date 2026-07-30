import type { Phase, PermissionRole } from './types';

// The PHP backend (webpages/SubmitConfigurePermissions.php) reads parameters via getString()/getInt(),
// which only look at $_GET/$_POST -- so requests must be form-encoded, not a raw JSON body.
async function postActionRaw<T>(action: string, body: URLSearchParams): Promise<T> {
  body.set('ajax_request_action', action);
  const response = await fetch('SubmitConfigurePermissions.php', {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body,
  });
  const json = await response.json();
  if (json.error) {
    throw new Error(json.error);
  }
  return json as T;
}

function postAction<T>(action: string, params: Record<string, string | number | boolean>): Promise<T> {
  const body = new URLSearchParams();
  for (const [key, value] of Object.entries(params)) {
    body.set(key, String(value));
  }
  return postActionRaw(action, body);
}

// getArrayOfInts() (webpages/data_functions.php) expects a repeated "name[]=..." form field,
// which PHP collects into $_POST['name'] as an array.
function postActionWithIdList<T>(action: string, fieldName: string, ids: number[]): Promise<T> {
  const body = new URLSearchParams();
  ids.forEach((id) => body.append(`${fieldName}[]`, String(id)));
  return postActionRaw(action, body);
}

export function togglePermission(
  permatomid: number,
  permroleid: number,
  phaseid: number | null,
  grant: boolean
): Promise<Record<string, never>> {
  return postAction('toggle_permission', {
    permatomid,
    permroleid,
    phaseid: phaseid === null ? '' : phaseid,
    grant: grant ? '1' : '0',
  });
}

export function addRole(permrolename: string, notes: string, display_order: number): Promise<{ role: PermissionRole }> {
  return postAction('add_role', { permrolename, notes, display_order });
}

export function updateRole(
  permroleid: number,
  permrolename: string,
  notes: string,
  display_order: number
): Promise<Record<string, never>> {
  return postAction('update_role', { permroleid, permrolename, notes, display_order });
}

// cascade: when true, dependent Permissions rows for this role are deleted first (the caller
// is expected to have already confirmed this with the user -- see RolesTab.tsx).
export function deleteRole(permroleid: number, cascade = false): Promise<Record<string, never>> {
  return postAction('delete_role', { permroleid, cascade: cascade ? '1' : '0' });
}

export function addPhase(
  phasename: string,
  notes: string,
  current: boolean,
  implemented: boolean,
  display_order: number
): Promise<{ phase: Phase }> {
  return postAction('add_phase', {
    phasename,
    notes,
    current: current ? '1' : '0',
    implemented: implemented ? '1' : '0',
    display_order,
  });
}

export function updatePhase(
  phaseid: number,
  phasename: string,
  notes: string,
  current: boolean,
  implemented: boolean,
  display_order: number
): Promise<Record<string, never>> {
  return postAction('update_phase', {
    phaseid,
    phasename,
    notes,
    current: current ? '1' : '0',
    implemented: implemented ? '1' : '0',
    display_order,
  });
}

// cascade: when true, dependent Permissions rows for this phase are deleted first (the caller
// is expected to have already confirmed this with the user -- see PhasesTab.tsx).
export function deletePhase(phaseid: number, cascade = false): Promise<Record<string, never>> {
  return postAction('delete_phase', { phaseid, cascade: cascade ? '1' : '0' });
}

// Reassigns display_order to 10, 20, 30, ... in the given order (same convention as the
// Configuration Table Editor's drag-to-reorder behavior).
export function reorderRoles(orderedRoleIds: number[]): Promise<Record<string, never>> {
  return postActionWithIdList('reorder_roles', 'ordered_ids', orderedRoleIds);
}

export function reorderPhases(orderedPhaseIds: number[]): Promise<Record<string, never>> {
  return postActionWithIdList('reorder_phases', 'ordered_ids', orderedPhaseIds);
}

export function reorderAtoms(orderedAtomIds: number[]): Promise<Record<string, never>> {
  return postActionWithIdList('reorder_atoms', 'ordered_ids', orderedAtomIds);
}
