export interface PermissionAtom {
  permatomid: number;
  permatomtag: string;
  // Human-friendly display name -- used in the UI instead of permatomtag, which remains the
  // internal identifier (matched against by tag in App.tsx/selfLockout.ts).
  permatomname: string;
  elementid: number | null;
  page: string | null;
  notes: string | null;
  display_order: number | null;
}

export interface PermissionRole {
  permroleid: number;
  permrolename: string;
  notes: string | null;
  display_order: number | null;
}

export interface Phase {
  phaseid: number;
  phasename: string;
  notes: string | null;
  current: boolean;
  implemented: boolean;
  display_order: number | null;
}

// Role-based grants only -- badgeid (per-participant override) grants are out of scope
// for this page; see design notes in webpages/ConfigurePermissions.php.
export interface Permission {
  permissionid: number;
  permatomid: number;
  phaseid: number | null;
  permroleid: number;
}

export interface BootstrapData {
  atoms: PermissionAtom[];
  roles: PermissionRole[];
  phases: Phase[];
  permissions: Permission[];
  // permroleids the logged-in user holds, so the client can block edits that would revoke
  // their own access to this page -- see selfLockout.ts.
  currentUserRoleIds: number[];
}
