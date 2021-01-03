## This script adds the data retention consent field to the participants table
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

INSERT INTO `PermissionAtoms` (permatomid, permatomtag, elementid, page, notes) VALUES
    (20,'CreateUser',NULL,'CreateUser','Manually create user.');

INSERT INTO PatchLog (patchname) VALUES ('57_create_user_permission.sql');
