import type { Permission, PermissionAtom, PermissionRole, Phase } from './types';

function phaseLabel(phaseid: number | null, phases: Phase[]): string {
  if (phaseid === null) {
    return 'All Phases';
  }
  return phases.find((p) => p.phaseid === phaseid)?.phasename ?? `phase ${phaseid}`;
}

// Permission grants that would be removed if this role were deleted, one line per grant.
export function describeRoleDeleteImpact(
  permissions: Permission[],
  atoms: PermissionAtom[],
  phases: Phase[],
  permroleid: number
): string[] {
  return permissions
    .filter((p) => p.permroleid === permroleid)
    .map((p) => {
      const atomName = atoms.find((a) => a.permatomid === p.permatomid)?.permatomname ?? `atom ${p.permatomid}`;
      return `${atomName} (${phaseLabel(p.phaseid, phases)})`;
    });
}

// Permission grants that would be removed if this phase were deleted, one line per grant.
export function describePhaseDeleteImpact(
  permissions: Permission[],
  atoms: PermissionAtom[],
  roles: PermissionRole[],
  phaseid: number
): string[] {
  return permissions
    .filter((p) => p.phaseid === phaseid)
    .map((p) => {
      const atomName = atoms.find((a) => a.permatomid === p.permatomid)?.permatomname ?? `atom ${p.permatomid}`;
      const roleName = roles.find((r) => r.permroleid === p.permroleid)?.permrolename ?? `role ${p.permroleid}`;
      return `${atomName} granted to ${roleName}`;
    });
}

export function buildDeleteConfirmMessage(entityDescription: string, impactLines: string[]): string {
  return (
    `Deleting ${entityDescription} will also remove these permission grants:\n\n` +
    impactLines.map((line) => `- ${line}`).join('\n') +
    '\n\nContinue?'
  );
}
