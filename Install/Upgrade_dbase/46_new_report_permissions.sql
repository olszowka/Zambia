## This script create news permissions related to manipulating reports
##
##	Created by Peter Olszowka on 2019-06-16;
## 	Copyright (c) 2019 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
LOCK TABLES PermissionAtoms WRITE;
SET FOREIGN_KEY_CHECKS = 0;
REPLACE INTO `PermissionAtoms` (permatomid, permatomtag, elementid, page, notes) VALUES
  (17,'ConfigureReports',NULL,'ConfigureReports','Enable menu option');
SET FOREIGN_KEY_CHECKS = 1;
UNLOCK TABLES;

LOCK TABLES PermissionRoles WRITE, Permissions WRITE;
SET FOREIGN_KEY_CHECKS = 0;
REPLACE INTO `PermissionRoles` (permroleid, permrolename, notes) VALUES
  (1,'Administrator','Reconfigure reports + ???'),
  (12,'Senior Staff','Can send email from Zambia');
SET FOREIGN_KEY_CHECKS = 1;
UNLOCK TABLES;

LOCK TABLES Permissions WRITE;
SET FOREIGN_KEY_CHECKS = 0;
UPDATE `Permissions`
    SET
        permroleid = 12 /* Senior Staff */
    WHERE
        permroleid = 1; /* Administrator */
REPLACE INTO `Permissions` (permissionid, permatomid, phaseid, permroleid, badgeid) VALUES
  (NULL,17,NULL,1,NULL);
SET FOREIGN_KEY_CHECKS = 1;
UNLOCK TABLES;

INSERT INTO PatchLog (patchname) VALUES ('46_new_report_permissions.sql');
