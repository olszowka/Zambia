import { useState } from 'react';
import type { Permission, PermissionAtom, PermissionRole, Phase } from '../types';
import { addPhase, deletePhase, reorderPhases, updatePhase } from '../api';
import { buildDeleteConfirmMessage, describePhaseDeleteImpact } from '../deleteImpact';
import { canAccessConfigurePermissionsPage, SELF_LOCKOUT_MESSAGE } from '../selfLockout';
import { useDragReorder } from '../useDragReorder';
import { useSuppressHoverAfterClick } from '../useSuppressHoverAfterClick';
import DragHandle from './DragHandle';

interface Props {
  phases: Phase[];
  atoms: PermissionAtom[];
  roles: PermissionRole[];
  permissions: Permission[];
  onPhasesChange: (phases: Phase[]) => void;
  onPermissionsChange: (permissions: Permission[]) => void;
  onError: (message: string) => void;
  currentUserRoleIds: number[];
  staffAtomId: number | null;
  configurePermissionsAtomId: number | null;
}

const emptyForm = { phasename: '', notes: '', current: false, implemented: false };

export default function PhasesTab({
  phases,
  atoms,
  roles,
  permissions,
  onPhasesChange,
  onPermissionsChange,
  onError,
  currentUserRoleIds,
  staffAtomId,
  configurePermissionsAtomId,
}: Props) {
  const [editingId, setEditingId] = useState<number | null>(null);
  const [form, setForm] = useState(emptyForm);
  const [showAddForm, setShowAddForm] = useState(false);
  const { ref: hoverSuppressRef, onClickCapture: suppressHoverOnClick } = useSuppressHoverAfterClick();

  const { getRowProps, getHandleProps } = useDragReorder(
    phases,
    (phase) => phase.phaseid,
    async (orderedIds) => {
      const previousPhases = phases;
      const byId = new Map(phases.map((p) => [p.phaseid, p]));
      onPhasesChange(orderedIds.map((id, index) => ({ ...byId.get(id)!, display_order: (index + 1) * 10 })));
      try {
        await reorderPhases(orderedIds);
      } catch (e) {
        onPhasesChange(previousPhases);
        onError(e instanceof Error ? e.message : 'Failed to reorder phases');
      }
    }
  );

  function startEdit(phase: Phase) {
    setEditingId(phase.phaseid);
    setForm({
      phasename: phase.phasename,
      notes: phase.notes ?? '',
      current: phase.current,
      implemented: phase.implemented,
    });
  }

  async function saveEdit(phaseid: number) {
    const nextPhases = phases.map((p) => (p.phaseid === phaseid ? { ...p, ...form } : p));
    const wasReachable = canAccessConfigurePermissionsPage(
      permissions,
      phases,
      currentUserRoleIds,
      staffAtomId,
      configurePermissionsAtomId
    );
    const willBeReachable = canAccessConfigurePermissionsPage(
      permissions,
      nextPhases,
      currentUserRoleIds,
      staffAtomId,
      configurePermissionsAtomId
    );
    if (wasReachable && !willBeReachable) {
      onError(SELF_LOCKOUT_MESSAGE);
      return;
    }

    const phase = phases.find((p) => p.phaseid === phaseid)!;
    try {
      await updatePhase(phaseid, form.phasename, form.notes, form.current, form.implemented, phase.display_order ?? 0);
      onPhasesChange(nextPhases);
      setEditingId(null);
    } catch (e) {
      onError(e instanceof Error ? e.message : 'Failed to update phase');
    }
  }

  async function handleAdd() {
    const nextDisplayOrder = Math.max(0, ...phases.map((p) => p.display_order ?? 0)) + 10;
    try {
      const result = await addPhase(form.phasename, form.notes, form.current, form.implemented, nextDisplayOrder);
      onPhasesChange([...phases, result.phase]);
      setForm(emptyForm);
      setShowAddForm(false);
    } catch (e) {
      onError(e instanceof Error ? e.message : 'Failed to add phase');
    }
  }

  async function handleDelete(phase: Phase) {
    const impactLines = describePhaseDeleteImpact(permissions, atoms, roles, phase.phaseid);
    const hasImpact = impactLines.length > 0;

    if (hasImpact) {
      const nextPermissions = permissions.filter((p) => p.phaseid !== phase.phaseid);
      const nextPhases = phases.filter((p) => p.phaseid !== phase.phaseid);
      const wasReachable = canAccessConfigurePermissionsPage(
        permissions,
        phases,
        currentUserRoleIds,
        staffAtomId,
        configurePermissionsAtomId
      );
      const willBeReachable = canAccessConfigurePermissionsPage(
        nextPermissions,
        nextPhases,
        currentUserRoleIds,
        staffAtomId,
        configurePermissionsAtomId
      );
      if (wasReachable && !willBeReachable) {
        onError(SELF_LOCKOUT_MESSAGE);
        return;
      }
    }

    const message = hasImpact
      ? buildDeleteConfirmMessage(`the phase "${phase.phasename}"`, impactLines)
      : `Delete phase "${phase.phasename}"?`;
    if (!confirm(message)) {
      return;
    }
    try {
      await deletePhase(phase.phaseid, hasImpact);
      onPhasesChange(phases.filter((p) => p.phaseid !== phase.phaseid));
      if (hasImpact) {
        onPermissionsChange(permissions.filter((p) => p.phaseid !== phase.phaseid));
      }
    } catch (e) {
      onError(e instanceof Error ? e.message : 'Failed to delete phase');
    }
  }

  return (
    <div ref={hoverSuppressRef} onClickCapture={suppressHoverOnClick} className="container-xl">
      <table className="table table-bordered table-sm align-middle table-clear border-dark">
        <thead>
          <tr>
            <th style={{ width: '2rem' }}></th>
            <th>Name</th>
            <th>Notes</th>
            <th style={{ width: '6rem' }}>Current</th>
            <th style={{ width: '7rem' }}>Implemented</th>
            <th style={{ width: '10rem' }}></th>
          </tr>
        </thead>
        <tbody>
          {phases.map((phase, index) => (
            <tr key={phase.phaseid} {...getRowProps(index)}>
              <td className="text-center">
                <DragHandle {...getHandleProps()} />
              </td>
              {editingId === phase.phaseid ? (
                <>
                  <td>
                    <input
                      className="form-control form-control-sm"
                      value={form.phasename}
                      onChange={(e) => setForm({ ...form, phasename: e.target.value })}
                    />
                  </td>
                  <td>
                    <input
                      className="form-control form-control-sm"
                      value={form.notes}
                      onChange={(e) => setForm({ ...form, notes: e.target.value })}
                    />
                  </td>
                  <td className="text-center">
                    <input
                      type="checkbox"
                      className="form-check-input"
                      checked={form.current}
                      onChange={(e) => setForm({ ...form, current: e.target.checked })}
                    />
                  </td>
                  <td className="text-center">
                    <input
                      type="checkbox"
                      className="form-check-input"
                      checked={form.implemented}
                      onChange={(e) => setForm({ ...form, implemented: e.target.checked })}
                    />
                  </td>
                  <td>
                    <button
                      className="btn btn-sm btn-primary me-1"
                      onClick={(e) => {
                        e.currentTarget.blur();
                        saveEdit(phase.phaseid);
                      }}
                    >
                      Save
                    </button>
                    <button
                      className="btn btn-sm btn-secondary"
                      onClick={(e) => {
                        e.currentTarget.blur();
                        setEditingId(null);
                      }}
                    >
                      Cancel
                    </button>
                  </td>
                </>
              ) : (
                <>
                  <td>{phase.phasename}</td>
                  <td>{phase.notes}</td>
                  <td className="text-center">{phase.current ? 'Yes' : 'No'}</td>
                  <td className="text-center">{phase.implemented ? 'Yes' : 'No'}</td>
                  <td>
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={(e) => {
                        e.currentTarget.blur();
                        startEdit(phase);
                      }}
                    >
                      Edit
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={(e) => {
                        e.currentTarget.blur();
                        handleDelete(phase);
                      }}
                    >
                      Delete
                    </button>
                  </td>
                </>
              )}
            </tr>
          ))}
        </tbody>
      </table>
      {showAddForm ? (
        <div className="row g-2 align-items-end">
          <div className="col-auto">
            <label className="form-label">Name</label>
            <input
              className="form-control form-control-sm"
              value={form.phasename}
              onChange={(e) => setForm({ ...form, phasename: e.target.value })}
            />
          </div>
          <div className="col-auto">
            <label className="form-label">Notes</label>
            <input
              className="form-control form-control-sm"
              value={form.notes}
              onChange={(e) => setForm({ ...form, notes: e.target.value })}
            />
          </div>
          <div className="col-auto form-check">
            <input
              type="checkbox"
              className="form-check-input"
              id="new-phase-current"
              checked={form.current}
              onChange={(e) => setForm({ ...form, current: e.target.checked })}
            />
            <label className="form-check-label" htmlFor="new-phase-current">
              Current
            </label>
          </div>
          <div className="col-auto form-check">
            <input
              type="checkbox"
              className="form-check-input"
              id="new-phase-implemented"
              checked={form.implemented}
              onChange={(e) => setForm({ ...form, implemented: e.target.checked })}
            />
            <label className="form-check-label" htmlFor="new-phase-implemented">
              Implemented
            </label>
          </div>
          <div className="col-auto">
            <button className="btn btn-sm btn-primary me-1" onClick={handleAdd}>
              Add Phase
            </button>
            <button
              className="btn btn-sm btn-secondary"
              onClick={() => {
                setShowAddForm(false);
                setForm(emptyForm);
              }}
            >
              Cancel
            </button>
          </div>
        </div>
      ) : (
        <button className="btn btn-sm btn-primary" onClick={() => setShowAddForm(true)}>
          Add Phase
        </button>
      )}
    </div>
  );
}
