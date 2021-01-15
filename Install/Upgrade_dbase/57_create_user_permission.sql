## This script modifies the db to support editing participant registration (contact) data and permission roles.
##
##	Created by Syd Weinstein on September 3, 2020
## 	Copyright (c) 2020-2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
CREATE TABLE CongoDumpHistory (
    `badgeid` varchar(15) NOT NULL DEFAULT '',
    `firstname` varchar(30) DEFAULT NULL,
    `lastname` varchar(40) DEFAULT NULL,
    `badgename` varchar(51) DEFAULT NULL,
    `phone` varchar(100) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `postaddress1` varchar(100) DEFAULT NULL,
    `postaddress2` varchar(100) DEFAULT NULL,
    `postcity` varchar(50) DEFAULT NULL,
    `poststate` varchar(25) DEFAULT NULL,
    `postzip` varchar(10) DEFAULT NULL,
    `postcountry` varchar(25) DEFAULT NULL,
    `regtype` varchar(40) DEFAULT NULL,
    `createdts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `createdbybadgeid` varchar(15) NOT NULL DEFAULT '',
    `inactivatedts` timestamp NULL DEFAULT NULL,
    `inactivatedbybadgeid` varchar(15) DEFAULT NULL,
    PRIMARY KEY (`badgeid`,`createdts`),
    KEY `badgeid` (`badgeid`),
    KEY `createdbybadgeid` (`createdbybadgeid`),
    KEY `inactivatedbybadgeid` (`inactivatedbybadgeid`),
    CONSTRAINT `CongoDumpHistory_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `CongoDumpHistory_ibfk_2` FOREIGN KEY (`createdbybadgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `CongoDumpHistory_ibfk_3` FOREIGN KEY (`inactivatedbybadgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contains changes to user information from registration system.';

INSERT INTO PermissionAtoms (permatomid, permatomtag, elementid, page, notes) VALUES
    (20,'CreateUser',NULL,'CreateUser','Manually create user. Must have edit for one or more roles.'),
    (21,'EditUserPermRoles',NULL,'Admin Participants','Control all user permission roles. Also used on create user page.'),
    (22,'EditUserPermRoles',3,'Admin Participants','Control participant user permission role only. Also used on create user page.'),
    (23,'EditUserPermRoles',2,'Admin Participants','Control staff user permission role only. Also used on create user page.');

ALTER TABLE PermissionRoles
    ADD COLUMN display_order int(11) AFTER notes;

UPDATE PermissionRoles
    SET display_order = 10 * permroleid;

/**
  Admin role gets to administer all roles
  Senior staff role gets to administer staff and participant roles
 */
INSERT INTO Permissions (permatomid, phaseid, permroleid, badgeid) VALUES
    (21, NULL, 1, NULL), (22, NULL, 12, NULL), (23, NULL, 12, NULL);

/**
  Copy all staff permissions to senior staff it doesn't already have
 */
INSERT INTO Permissions (permatomid, phaseid, permroleid, badgeid)
    SELECT DISTINCT
            P.permatomid, P.phaseid, 12 /* Senior Staff */, NULL
        FROM
            Permissions P
        WHERE
                P.permroleid = 2 /* Staff */
            AND NOT EXISTS (
                SELECT *
                    FROM
                        Permissions P2
                    WHERE
                            P2.permatomid = P.permatomid
                        AND P2.phaseid <=> P.phaseid
                        AND P2.permroleid = 12 /* Senior Staff */
            );

/**
  Copy all senior staff permissions to admin it doesn't already have
 */
INSERT INTO Permissions (permatomid, phaseid, permroleid, badgeid)
    SELECT DISTINCT
            P.permatomid, P.phaseid, 1 /* Administrator */, NULL
        FROM
            Permissions P
        WHERE
                P.permroleid = 12 /* Senior Staff */
            AND NOT EXISTS (
                SELECT *
                    FROM
                        Permissions P2
                    WHERE
                            P2.permatomid = P.permatomid
                        AND P2.phaseid <=> P.phaseid
                        AND P2.permroleid = 1 /* Administrator */
            );

INSERT INTO PatchLog (patchname) VALUES ('57_create_user_permission.sql');
