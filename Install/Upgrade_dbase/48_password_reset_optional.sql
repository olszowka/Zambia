## This script adds the 'ResetUserPassword' permission atom and grants it to senior staff.
##
##	Created by Peter Olszowka on 2020-04-19;
## 	Copyright (c) 2020

LOCK TABLES PermissionAtoms WRITE;
SET FOREIGN_KEY_CHECKS = 0;
REPLACE INTO `PermissionAtoms` (permatomid, permatomtag, elementid, page, notes) VALUES
    (18,'ResetUserPassword',NULL,'AdminParticipants','No one needs this permission once password overhaul is implemented and system has email integration.');
SET FOREIGN_KEY_CHECKS = 1;
UNLOCK TABLES;

LOCK TABLES Permissions WRITE;
INSERT INTO `Permissions` (permatomid, phaseid, permroleid, badgeid) VALUES
    (18, NULL, 12, NULL);
UNLOCK TABLES;

INSERT INTO PatchLog (patchname) VALUES ('48_password_reset_optional.sql');
