import { useState } from 'react';
import type { BootstrapData, PermissionRole, Phase, Permission } from './types';
import PermissionMatrix from './components/PermissionMatrix';
import RolesTab from './components/RolesTab';
import PhasesTab from './components/PhasesTab';

type TabKey = 'matrix' | 'roles' | 'phases';

export default function App({ initialData }: { initialData: BootstrapData }) {
  const [activeTab, setActiveTab] = useState<TabKey>('matrix');
  const [roles, setRoles] = useState<PermissionRole[]>(initialData.roles);
  const [phases, setPhases] = useState<Phase[]>(initialData.phases);
  const [permissions, setPermissions] = useState<Permission[]>(initialData.permissions);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const configurePermissionsAtom = initialData.atoms.find((a) => a.permatomtag === 'ConfigurePermissions');
  const configurePermissionsAtomId = configurePermissionsAtom ? configurePermissionsAtom.permatomid : null;
  // Reaching this page also requires the "Staff" atom -- StaffCommonCode.php gates every
  // staff page on it before this page's own permission check ever runs.
  const staffAtom = initialData.atoms.find((a) => a.permatomtag === 'Staff');
  const staffAtomId = staffAtom ? staffAtom.permatomid : null;
  const currentUserRoleIds = initialData.currentUserRoleIds;

  function tabButton(key: TabKey, label: string) {
    return (
      <li className="nav-item" key={key}>
        <button
          type="button"
          className={`nav-link${activeTab === key ? ' active' : ''}`}
          onClick={() => setActiveTab(key)}
        >
          {label}
        </button>
      </li>
    );
  }

  return (
    <div>
      {errorMessage && (
        <div className="alert alert-danger alert-dismissible" role="alert">
          {errorMessage}
          <button type="button" className="btn-close" aria-label="Close" onClick={() => setErrorMessage(null)} />
        </div>
      )}
      <ul className="nav nav-tabs mb-3">
        {tabButton('matrix', 'Permission Matrix')}
        {tabButton('roles', 'Roles')}
        {tabButton('phases', 'Phases')}
      </ul>
      {activeTab === 'matrix' && (
        <PermissionMatrix
          atoms={initialData.atoms}
          roles={roles}
          phases={phases}
          permissions={permissions}
          onPermissionsChange={setPermissions}
          onError={setErrorMessage}
          currentUserRoleIds={currentUserRoleIds}
          staffAtomId={staffAtomId}
          configurePermissionsAtomId={configurePermissionsAtomId}
        />
      )}
      {activeTab === 'roles' && (
        <RolesTab
          roles={roles}
          atoms={initialData.atoms}
          phases={phases}
          permissions={permissions}
          onRolesChange={setRoles}
          onPermissionsChange={setPermissions}
          onError={setErrorMessage}
        />
      )}
      {activeTab === 'phases' && (
        <PhasesTab
          phases={phases}
          atoms={initialData.atoms}
          roles={roles}
          permissions={permissions}
          onPhasesChange={setPhases}
          onPermissionsChange={setPermissions}
          onError={setErrorMessage}
          currentUserRoleIds={currentUserRoleIds}
          staffAtomId={staffAtomId}
          configurePermissionsAtomId={configurePermissionsAtomId}
        />
      )}
    </div>
  );
}
