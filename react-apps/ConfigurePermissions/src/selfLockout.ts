import type { Permission, Phase } from './types';

// Mirrors the reachability logic set_permission_set() applies at login time
// (webpages/db_functions.php): a role-based grant applies if phaseid is NULL (all phases),
// or if the referenced phase is both current and implemented.
function isAtomReachable(
  permissions: Permission[],
  phases: Phase[],
  currentUserRoleIds: number[],
  atomId: number | null
): boolean {
  if (atomId === null) {
    return true;
  }
  return permissions.some((p) => {
    if (p.permatomid !== atomId || !currentUserRoleIds.includes(p.permroleid)) {
      return false;
    }
    if (p.phaseid === null) {
      return true;
    }
    const phase = phases.find((ph) => ph.phaseid === p.phaseid);
    return !!phase && phase.current && phase.implemented;
  });
}

// StaffCommonCode.php gates every staff page (including this one) on may_I("Staff") before
// this page's own may_I("ConfigurePermissions") check ever runs -- so "can log in and reach
// this page" requires both atoms to remain reachable via the user's roles, not just the one.
export function canAccessConfigurePermissionsPage(
  permissions: Permission[],
  phases: Phase[],
  currentUserRoleIds: number[],
  staffAtomId: number | null,
  configurePermissionsAtomId: number | null
): boolean {
  return (
    isAtomReachable(permissions, phases, currentUserRoleIds, staffAtomId) &&
    isAtomReachable(permissions, phases, currentUserRoleIds, configurePermissionsAtomId)
  );
}

export const SELF_LOCKOUT_MESSAGE =
  'This change would revoke your own ability to log in and access the Configure Permissions page, so it was blocked.';
