import { useState } from 'react';
import type { PermissionAtom, PermissionRole, Phase, Permission } from '../types';
import { togglePermission } from '../api';
import { canAccessConfigurePermissionsPage, SELF_LOCKOUT_MESSAGE } from '../selfLockout';

const ALL_PHASES = 'all';

interface Props {
  atoms: PermissionAtom[];
  roles: PermissionRole[];
  phases: Phase[];
  permissions: Permission[];
  onPermissionsChange: (permissions: Permission[]) => void;
  onError: (message: string) => void;
  currentUserRoleIds: number[];
  staffAtomId: number | null;
  configurePermissionsAtomId: number | null;
}

function atomRowLabel(atom: PermissionAtom, roles: PermissionRole[]): string {
  if (atom.elementid === null) {
    return atom.permatomtag;
  }
  const scopedRole = roles.find((role) => role.permroleid === atom.elementid);
  return `${atom.permatomtag} (${scopedRole ? `${scopedRole.permrolename} only` : `elementid ${atom.elementid}`})`;
}

export default function PermissionMatrix({
  atoms,
  roles,
  phases,
  permissions,
  onPermissionsChange,
  onError,
  currentUserRoleIds,
  staffAtomId,
  configurePermissionsAtomId,
}: Props) {
  const [selectedPhase, setSelectedPhase] = useState<string>(ALL_PHASES);
  const [pendingCells, setPendingCells] = useState<Set<string>>(new Set());

  const viewingPhaseId = selectedPhase === ALL_PHASES ? null : Number(selectedPhase);

  function cellKey(permatomid: number, permroleid: number): string {
    return `${permatomid}:${permroleid}`;
  }

  function isGranted(permatomid: number, permroleid: number, phaseid: number | null): boolean {
    return permissions.some(
      (p) => p.permatomid === permatomid && p.permroleid === permroleid && p.phaseid === phaseid
    );
  }

  async function handleToggle(atom: PermissionAtom, role: PermissionRole, checked: boolean) {
    const key = cellKey(atom.permatomid, role.permroleid);
    if (pendingCells.has(key)) {
      return;
    }

    const previousPermissions = permissions;
    const nextPermissions = checked
      ? [
          ...permissions,
          { permissionid: -1, permatomid: atom.permatomid, permroleid: role.permroleid, phaseid: viewingPhaseId },
        ]
      : permissions.filter(
          (p) => !(p.permatomid === atom.permatomid && p.permroleid === role.permroleid && p.phaseid === viewingPhaseId)
        );

    const isSelfProtectedAtom = atom.permatomid === configurePermissionsAtomId || atom.permatomid === staffAtomId;
    if (!checked && isSelfProtectedAtom) {
      const wasReachable = canAccessConfigurePermissionsPage(
        previousPermissions,
        phases,
        currentUserRoleIds,
        staffAtomId,
        configurePermissionsAtomId
      );
      const willBeReachable = canAccessConfigurePermissionsPage(
        nextPermissions,
        phases,
        currentUserRoleIds,
        staffAtomId,
        configurePermissionsAtomId
      );
      if (wasReachable && !willBeReachable) {
        onError(SELF_LOCKOUT_MESSAGE);
        return;
      }
    }

    setPendingCells((prev) => new Set(prev).add(key));
    onPermissionsChange(nextPermissions);

    try {
      await togglePermission(atom.permatomid, role.permroleid, viewingPhaseId, checked);
    } catch (e) {
      onPermissionsChange(previousPermissions);
      onError(e instanceof Error ? e.message : 'Failed to update permission');
    } finally {
      setPendingCells((prev) => {
        const next = new Set(prev);
        next.delete(key);
        return next;
      });
    }
  }

  return (
    <div>
      <div className="row mb-3">
        <div className="col-auto">
          <label htmlFor="phase-select" className="form-label">
            Phase scope
          </label>
          <select
            id="phase-select"
            className="form-select"
            value={selectedPhase}
            onChange={(e) => setSelectedPhase(e.target.value)}
          >
            <option value={ALL_PHASES}>All Phases</option>
            {phases.map((phase) => (
              <option key={phase.phaseid} value={phase.phaseid}>
                {phase.phasename}
              </option>
            ))}
          </select>
        </div>
      </div>
      <div className="table-responsive">
        <table className="table table-bordered table-sm permission-matrix table-clear border-dark">
          <thead>
            <tr>
              <th>Permission Atom</th>
              {roles.map((role) => (
                <th key={role.permroleid} className="text-center">
                  {role.permrolename}
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {atoms.map((atom) => (
              <tr key={`${atom.permatomid}`}>
                <td>
                  {atomRowLabel(atom, roles)}
                  {atom.notes && <div className="form-text">{atom.notes}</div>}
                </td>
                {roles.map((role) => {
                  const grantedAtCurrentScope = isGranted(atom.permatomid, role.permroleid, viewingPhaseId);
                  const grantedAllPhases = viewingPhaseId !== null && isGranted(atom.permatomid, role.permroleid, null);
                  const key = cellKey(atom.permatomid, role.permroleid);
                  return (
                    <td key={role.permroleid} className="text-center">
                      <input
                        type="checkbox"
                        className="form-check-input"
                        checked={grantedAtCurrentScope}
                        disabled={pendingCells.has(key)}
                        onChange={(e) => handleToggle(atom, role, e.target.checked)}
                        title={grantedAllPhases ? 'Already granted for All Phases' : undefined}
                      />
                      {grantedAllPhases && <div className="form-text">(all phases)</div>}
                    </td>
                  );
                })}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
