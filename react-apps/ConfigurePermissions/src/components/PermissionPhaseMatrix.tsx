import { useState } from 'react';
import type { PermissionAtom, PermissionRole, Phase, Permission } from '../types';
import { reorderAtoms, togglePermission } from '../api';
import { canAccessConfigurePermissionsPage, SELF_LOCKOUT_MESSAGE } from '../selfLockout';
import { useDragReorder } from '../useDragReorder';
import DragHandle from './DragHandle';

const ALL_PHASES_COLUMN = 'all';

interface Props {
  atoms: PermissionAtom[];
  onAtomsChange: (atoms: PermissionAtom[]) => void;
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
    return atom.permatomname;
  }
  const scopedRole = roles.find((role) => role.permroleid === atom.elementid);
  return `${atom.permatomname} (${scopedRole ? `${scopedRole.permrolename} only` : `elementid ${atom.elementid}`})`;
}

// Same grid as PermissionMatrix, transposed: one role is picked via a select box, and the
// columns are phases (plus an "All Phases" column) instead of roles.
export default function PermissionPhaseMatrix({
  atoms,
  onAtomsChange,
  roles,
  phases,
  permissions,
  onPermissionsChange,
  onError,
  currentUserRoleIds,
  staffAtomId,
  configurePermissionsAtomId,
}: Props) {
  const [selectedRole, setSelectedRole] = useState<string>('');
  const [pendingCells, setPendingCells] = useState<Set<string>>(new Set());

  const selectedRoleId = roles.some((r) => String(r.permroleid) === selectedRole)
    ? Number(selectedRole)
    : (roles[0]?.permroleid ?? null);

  const handleColWidth = '4%';
  const nameColWidth = '40%';
  const phaseColWidth = `${(100 - 4 - 40) / Math.max(phases.length + 1, 1)}%`;

  const { getRowProps, getHandleProps } = useDragReorder(
    atoms,
    (atom) => atom.permatomid,
    async (orderedIds) => {
      const previousAtoms = atoms;
      const byId = new Map(atoms.map((a) => [a.permatomid, a]));
      onAtomsChange(orderedIds.map((id, index) => ({ ...byId.get(id)!, display_order: (index + 1) * 10 })));
      try {
        await reorderAtoms(orderedIds);
      } catch (e) {
        onAtomsChange(previousAtoms);
        onError(e instanceof Error ? e.message : 'Failed to reorder permission atoms');
      }
    }
  );

  function cellKey(permatomid: number, phaseid: number | null): string {
    return `${permatomid}:${phaseid === null ? ALL_PHASES_COLUMN : phaseid}`;
  }

  function isGranted(permatomid: number, permroleid: number, phaseid: number | null): boolean {
    return permissions.some(
      (p) => p.permatomid === permatomid && p.permroleid === permroleid && p.phaseid === phaseid
    );
  }

  async function handleToggle(atom: PermissionAtom, phaseid: number | null, checked: boolean) {
    if (selectedRoleId === null) {
      return;
    }
    const key = cellKey(atom.permatomid, phaseid);
    if (pendingCells.has(key)) {
      return;
    }

    const previousPermissions = permissions;
    const nextPermissions = checked
      ? [
          ...permissions,
          { permissionid: -1, permatomid: atom.permatomid, permroleid: selectedRoleId, phaseid },
        ]
      : permissions.filter(
          (p) => !(p.permatomid === atom.permatomid && p.permroleid === selectedRoleId && p.phaseid === phaseid)
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
      await togglePermission(atom.permatomid, selectedRoleId, phaseid, checked);
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
          <label htmlFor="role-select" className="form-label">
            Role
          </label>
          <select
            id="role-select"
            className="form-select"
            value={selectedRoleId ?? ''}
            onChange={(e) => setSelectedRole(e.target.value)}
          >
            {roles.map((role) => (
              <option key={role.permroleid} value={role.permroleid}>
                {role.permrolename}
              </option>
            ))}
          </select>
        </div>
      </div>
      <div className="table-responsive">
        <table className="table table-bordered table-sm permission-matrix-header table-clear border-dark">
          <thead>
            <tr>
              <th style={{ width: handleColWidth }}></th>
              <th style={{ width: nameColWidth }}>Permission Atom</th>
              <th className="text-center" style={{ width: phaseColWidth }}>
                All
              </th>
              {phases.map((phase) => (
                <th key={phase.phaseid} className="text-center" style={{ width: phaseColWidth }}>
                  {phase.phasename}
                </th>
              ))}
            </tr>
          </thead>
        </table>
      </div>
      <div className="table-responsive permission-matrix-body-scroll">
        <table className="table table-bordered table-sm permission-matrix-body table-clear border-dark">
          <tbody>
            {atoms.map((atom, index) => (
              <tr key={`${atom.permatomid}`} {...getRowProps(index)}>
                <td className="text-center" style={{ width: handleColWidth }}>
                  <DragHandle {...getHandleProps()} />
                </td>
                <td style={{ width: nameColWidth }}>
                  {atomRowLabel(atom, roles)}
                  {atom.notes && <div className="form-text">{atom.notes}</div>}
                </td>
                {[null, ...phases.map((phase) => phase.phaseid)].map((phaseid) => {
                  const granted = selectedRoleId !== null && isGranted(atom.permatomid, selectedRoleId, phaseid);
                  const grantedAllPhases =
                    phaseid !== null &&
                    selectedRoleId !== null &&
                    isGranted(atom.permatomid, selectedRoleId, null);
                  const key = cellKey(atom.permatomid, phaseid);
                  return (
                    <td key={phaseid ?? ALL_PHASES_COLUMN} className="text-center" style={{ width: phaseColWidth }}>
                      <input
                        type="checkbox"
                        className="form-check-input"
                        checked={granted}
                        disabled={pendingCells.has(key) || selectedRoleId === null}
                        onChange={(e) => handleToggle(atom, phaseid, e.target.checked)}
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
