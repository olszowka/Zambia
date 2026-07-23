import { useState } from 'react';
import type { Permission, PermissionAtom, PermissionRole, Phase } from '../types';
import { addRole, deleteRole, reorderRoles, updateRole } from '../api';
import { buildDeleteConfirmMessage, describeRoleDeleteImpact } from '../deleteImpact';
import { useDragReorder } from '../useDragReorder';
import { useSuppressHoverAfterClick } from '../useSuppressHoverAfterClick';
import DragHandle from './DragHandle';

interface Props {
  roles: PermissionRole[];
  atoms: PermissionAtom[];
  phases: Phase[];
  permissions: Permission[];
  onRolesChange: (roles: PermissionRole[]) => void;
  onPermissionsChange: (permissions: Permission[]) => void;
  onError: (message: string) => void;
}

const emptyForm = { permrolename: '', notes: '' };

export default function RolesTab({
  roles,
  atoms,
  phases,
  permissions,
  onRolesChange,
  onPermissionsChange,
  onError,
}: Props) {
  const [editingId, setEditingId] = useState<number | null>(null);
  const [form, setForm] = useState(emptyForm);
  const [showAddForm, setShowAddForm] = useState(false);
  const { ref: hoverSuppressRef, onClickCapture: suppressHoverOnClick } = useSuppressHoverAfterClick();

  const { getRowProps, getHandleProps } = useDragReorder(
    roles,
    (role) => role.permroleid,
    async (orderedIds) => {
      const previousRoles = roles;
      const byId = new Map(roles.map((r) => [r.permroleid, r]));
      onRolesChange(
        orderedIds.map((id, index) => ({ ...byId.get(id)!, display_order: (index + 1) * 10 }))
      );
      try {
        await reorderRoles(orderedIds);
      } catch (e) {
        onRolesChange(previousRoles);
        onError(e instanceof Error ? e.message : 'Failed to reorder roles');
      }
    }
  );

  function startEdit(role: PermissionRole) {
    setEditingId(role.permroleid);
    setForm({ permrolename: role.permrolename, notes: role.notes ?? '' });
  }

  async function saveEdit(permroleid: number) {
    const role = roles.find((r) => r.permroleid === permroleid)!;
    try {
      await updateRole(permroleid, form.permrolename, form.notes, role.display_order ?? 0);
      onRolesChange(
        roles.map((r) => (r.permroleid === permroleid ? { ...r, permrolename: form.permrolename, notes: form.notes } : r))
      );
      setEditingId(null);
    } catch (e) {
      onError(e instanceof Error ? e.message : 'Failed to update role');
    }
  }

  async function handleAdd() {
    const nextDisplayOrder = Math.max(0, ...roles.map((r) => r.display_order ?? 0)) + 10;
    try {
      const result = await addRole(form.permrolename, form.notes, nextDisplayOrder);
      onRolesChange([...roles, result.role]);
      setForm(emptyForm);
      setShowAddForm(false);
    } catch (e) {
      onError(e instanceof Error ? e.message : 'Failed to add role');
    }
  }

  async function handleDelete(role: PermissionRole) {
    const impactLines = describeRoleDeleteImpact(permissions, atoms, phases, role.permroleid);
    const hasImpact = impactLines.length > 0;
    const message = hasImpact
      ? buildDeleteConfirmMessage(`the role "${role.permrolename}"`, impactLines)
      : `Delete role "${role.permrolename}"?`;
    if (!confirm(message)) {
      return;
    }
    try {
      await deleteRole(role.permroleid, hasImpact);
      onRolesChange(roles.filter((r) => r.permroleid !== role.permroleid));
      if (hasImpact) {
        onPermissionsChange(permissions.filter((p) => p.permroleid !== role.permroleid));
      }
    } catch (e) {
      onError(e instanceof Error ? e.message : 'Failed to delete role');
    }
  }

  return (
    <div ref={hoverSuppressRef} onClickCapture={suppressHoverOnClick}>
      <table className="table table-bordered table-sm align-middle table-clear border-dark">
        <thead>
          <tr>
            <th style={{ width: '2rem' }}></th>
            <th>Name</th>
            <th>Notes</th>
            <th style={{ width: '10rem' }}></th>
          </tr>
        </thead>
        <tbody>
          {roles.map((role, index) => (
            <tr key={role.permroleid} {...getRowProps(index)}>
              <td className="text-center">
                <DragHandle {...getHandleProps()} />
              </td>
              {editingId === role.permroleid ? (
                <>
                  <td>
                    <input
                      className="form-control form-control-sm"
                      value={form.permrolename}
                      onChange={(e) => setForm({ ...form, permrolename: e.target.value })}
                    />
                  </td>
                  <td>
                    <input
                      className="form-control form-control-sm"
                      value={form.notes}
                      onChange={(e) => setForm({ ...form, notes: e.target.value })}
                    />
                  </td>
                  <td>
                    <button
                      className="btn btn-sm btn-primary me-1"
                      onClick={(e) => {
                        e.currentTarget.blur();
                        saveEdit(role.permroleid);
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
                  <td>{role.permrolename}</td>
                  <td>{role.notes}</td>
                  <td>
                    <button
                      className="btn btn-sm btn-outline-secondary me-1"
                      onClick={(e) => {
                        e.currentTarget.blur();
                        startEdit(role);
                      }}
                    >
                      Edit
                    </button>
                    <button
                      className="btn btn-sm btn-outline-danger"
                      onClick={(e) => {
                        e.currentTarget.blur();
                        handleDelete(role);
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
              value={form.permrolename}
              onChange={(e) => setForm({ ...form, permrolename: e.target.value })}
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
          <div className="col-auto">
            <button className="btn btn-sm btn-primary me-1" onClick={handleAdd}>
              Add Role
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
          Add Role
        </button>
      )}
    </div>
  );
}
