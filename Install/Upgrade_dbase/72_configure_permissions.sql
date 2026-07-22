## This script adds the ConfigurePermissions permission atom, used to gate the upcoming
## "Configure Permissions" admin page and its related server side endpoints, which will
## allow editing of the Permissions, Phases, and PermissionRoles tables.
##
##	Created by Peter Olszowka on 2026-07-22;
## 	Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

INSERT INTO PermissionAtoms(permatomid, permatomtag, page, notes)
VALUES (33, 'ConfigurePermissions', 'ConfigurePermissions', 'Allows editing the Permissions, Phases, and PermissionRoles tables');

INSERT INTO Permissions(permatomid, phaseid, permroleid)
SELECT a.permatomid, null, r.permroleid
FROM PermissionAtoms a
JOIN PermissionRoles r ON (r.permrolename = 'Administrator')
WHERE permatomtag = 'ConfigurePermissions';

INSERT INTO PatchLog (patchname) VALUES ('72_configure_permissions.sql');
