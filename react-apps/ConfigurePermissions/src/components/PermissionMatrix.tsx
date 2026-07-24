import { useState } from 'react';
import type { PermissionAtom, PermissionRole, Phase, Permission } from '../types';
import { reorderAtoms, togglePermission } from '../api';
import { canAccessConfigurePermissionsPage, SELF_LOCKOUT_MESSAGE } from '../selfLockout';
import { useDragReorder } from '../useDragReorder';
import DragHandle from './DragHandle';

const ALL_PHASES = 'all';

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

export default function PermissionMatrix({
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
  const [selectedPhase, setSelectedPhase] = useState<string>(ALL_PHASES);
  const [pendingCells, setPendingCells] = useState<Set<string>>(new Set());

  const viewingPhaseId = selectedPhase === ALL_PHASES ? null : Number(selectedPhase);

  // The header and body are two separate <table> elements (see below) so the body's table can
  // scroll on its own while the header table simply sits above it, unscrolled -- position:
  // sticky on table cells doesn't reliably paint above sibling rows in this browser, so this
  // avoids sticky entirely rather than trying to work around that. table-layout: fixed plus
  // these shared explicit widths is what keeps the two tables' columns aligned with each other.
  const handleColWidth = '4%';
  const nameColWidth = '40%';
  const roleColWidth = `${(100 - 4 - 40) / Math.max(roles.length, 1)}%`;

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
        <table className="table table-bordered table-sm permission-matrix-header table-clear border-dark">
          <thead>
            <tr>
              <th style={{ width: handleColWidth }}></th>
              <th style={{ width: nameColWidth }}>Permission Atom</th>
              {roles.map((role) => (
                <th key={role.permroleid} className="text-center" style={{ width: roleColWidth }}>
                  {role.permrolename}
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
                {roles.map((role) => {
                  const grantedAtCurrentScope = isGranted(atom.permatomid, role.permroleid, viewingPhaseId);
                  const grantedAllPhases = viewingPhaseId !== null && isGranted(atom.permatomid, role.permroleid, null);
                  const key = cellKey(atom.permatomid, role.permroleid);
                  return (
                    <td key={role.permroleid} className="text-center" style={{ width: roleColWidth }}>
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
