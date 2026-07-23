## This script adds the ConfigurePermissions permission atom, used to gate the upcoming
## "Configure Permissions" admin page and its related server side endpoints, which will
## allow editing of the Permissions, Phases, and PermissionRoles tables. It also adds a
## display_order column to PermissionAtoms, so that page can offer drag-to-reorder of the
## permission matrix rows, same as it already does for PermissionRoles and Phases.
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

ALTER TABLE PermissionAtoms ADD COLUMN display_order INT(11) AFTER notes;

## Populate display_order for all existing atoms (including the one just inserted above),
## sorted by permatomid, starting at 10 and incrementing by 10.
SET @display_order = 0;
UPDATE PermissionAtoms
    SET display_order = (@display_order := @display_order + 10)
    ORDER BY permatomid ASC;

INSERT INTO PatchLog (patchname) VALUES ('72_configure_permissions.sql');
