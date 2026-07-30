##  Created by Peter Olszowka on 2026-07-29;
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
## 1)
## As this script will alter an index, it first removes any rows which would violate the revised index.
## 2)
## The index to be revised is inadvertantly being used to support a foreign key, so an index is created
## to explicitly support that foreign key.  Then the index can be dropped and replaced.
## 3)
## This script replaces the unique1 index on the Permissions table so that duplicate rows with the
## same values for permatomid, phaseid, and permroleid containing a NULL badgeid will be prevented.
## The previous index did not prevent duplicates as long as badgeid was NULL.
## MariaDB does not support MySQL's functional index syntax (CREATE INDEX ... ON t ((expr))), so this
## is done by adding a generated virtual column for IFNULL(badgeid, '') and indexing that column
## instead. This form works on both MySQL and MariaDB.
## 4)
## This script add a row to the Permissions table (if it doesn't already exist) to allow participants
## to log in even if the only phase which is active is the "Post con" phase.
## 5)
## Altering the unique1 index above seems to have the side effect of renaming the table `Permissions` to
## `permissions` on some servers (reproducible on lower_case_table_names=2 setups; ALGORITHM=COPY did not
## prevent it, and MariaDB's DROP INDEX doesn't accept an ALGORITHM clause at all, so it's only specified
## on the CREATE INDEX below). This step detects whether that happened and, only then, restores the correct
## case. Since lower_case_table_names=2 makes a same-name case-only rename a no-op, the restore goes through
## a temporary table name in two steps.

DELETE P1 FROM Permissions P1
    JOIN Permissions P2 ON
            P1.permatomid = P2.permatomid
        AND P1.phaseid = P2.phaseid
        AND P1.permroleid = P2.permroleid
        AND IFNULL(P1.badgeid, '') <=> IFNULL(P2.badgeid, '')
    WHERE P1.permissionid > P2.permissionid;

CREATE INDEX `FK_PermAtoms` ON Permissions (permatomid);

DROP INDEX unique1 ON Permissions;

ALTER TABLE Permissions
    ADD COLUMN `badgeid_norm` VARCHAR(15) GENERATED ALWAYS AS (IFNULL(badgeid, '')) VIRTUAL;

CREATE UNIQUE INDEX unique1 ON Permissions (permatomid, phaseid, permroleid, badgeid_norm) ALGORITHM=COPY;

INSERT IGNORE INTO Permissions
    (permatomid, phaseid, permroleid, badgeid)
    VALUES (
    (SELECT permatomid FROM PermissionAtoms WHERE LOWER(permatomtag) = 'participant'),
    (SELECT phaseid FROM Phases WHERE LOWER(phasename) = 'post con'),
    (SELECT permroleid FROM PermissionRoles WHERE LOWER(permrolename) = 'participant'),
    NULL);

## lower_case_table_names=2 makes a same-name rename that only changes case (`permissions` -> `Permissions`)
## a no-op, so the fix must go through an intermediate name in two steps, as before -- just gated so it
## only runs if the renaming bug actually happened on this server.
SET @needs_case_rename := (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND BINARY TABLE_NAME = 'permissions'
);

SET @rename_sql_1 := IF(@needs_case_rename > 0,
    'RENAME TABLE `permissions` TO `permissions_temp`',
    'DO 0');
PREPARE stmt FROM @rename_sql_1;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @rename_sql_2 := IF(@needs_case_rename > 0,
    'RENAME TABLE `permissions_temp` TO `Permissions`',
    'DO 0');
PREPARE stmt FROM @rename_sql_2;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

INSERT INTO `PatchLog` (patchname) VALUES ('73_another_permissions_cleanup.sql');
