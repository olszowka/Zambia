-- Copyright (c) 2011-2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
-- This script contains base schema tables, indexes, and trigger, plus data necessary for Zambia to run at all.
--
-- Table structure for table `BioEditStatuses`
--

DROP TABLE IF EXISTS `BioEditStatuses`;
CREATE TABLE `BioEditStatuses` (
    `bioeditstatusid` INT(11) NOT NULL AUTO_INCREMENT,
    `bioeditstatusname` VARCHAR(60) DEFAULT NULL,
    `display_order` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY  (`bioeditstatusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `CongoDump`
--

DROP TABLE IF EXISTS `CongoDump`;
CREATE TABLE `CongoDump` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `firstname` VARCHAR(30) DEFAULT NULL,
    `lastname` VARCHAR(40) DEFAULT NULL,
    `badgename` VARCHAR(51) DEFAULT NULL,
    `phone` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `postaddress1` VARCHAR(100) DEFAULT NULL,
    `postaddress2` VARCHAR(100) DEFAULT NULL,
    `postcity` VARCHAR(50) DEFAULT NULL,
    `poststate` VARCHAR(25) DEFAULT NULL,
    `postzip` VARCHAR(10) DEFAULT NULL,
    `postcountry` VARCHAR(25) DEFAULT NULL,
    `regtype` VARCHAR(40) DEFAULT NULL,
    PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Credentials`
--

DROP TABLE IF EXISTS `Credentials`;
CREATE TABLE `Credentials` (
    `credentialid` INT(11) NOT NULL AUTO_INCREMENT,
    `credentialname` VARCHAR(100) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`credentialid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `CustomText`
--

DROP TABLE IF EXISTS `CustomText`;
CREATE TABLE `CustomText` (
    `customtextid` INT(11) NOT NULL AUTO_INCREMENT,
    `page` VARCHAR(100) DEFAULT NULL,
    `tag` VARCHAR(25) DEFAULT NULL,
    `textcontents` TEXT,
    `active` TINYINT(1) NOT NULL DEFAULT 1,
    `html_block_level` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`customtextid`),
    UNIQUE KEY `page` (`page`,`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Divisions`
--

DROP TABLE IF EXISTS `Divisions`;
CREATE TABLE `Divisions` (
    `divisionid` INT(11) NOT NULL AUTO_INCREMENT,
    `divisionname` VARCHAR(30) DEFAULT NULL,
    `display_order` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`divisionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `EmailCC`
--

DROP TABLE IF EXISTS `EmailCC`;
CREATE TABLE `EmailCC` (
    `emailccid` INT(11) NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) NOT NULL DEFAULT '0',
    `emailaddress` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`emailccid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `EmailFrom`
--

DROP TABLE IF EXISTS `EmailFrom`;
CREATE TABLE `EmailFrom` (
    `emailfromid` INT(11) NOT NULL AUTO_INCREMENT,
    `emailfromdescription` VARCHAR(30) DEFAULT NULL,
    `display_order` INT(11) NOT NULL DEFAULT '0',
    `emailfromaddress` VARCHAR(255) DEFAULT NULL,
    PRIMARY KEY (`emailfromid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `EmailHistory`
--

DROP TABLE IF EXISTS `EmailHistory`;
CREATE TABLE `EmailHistory` (
    `emailqueueid` INT(11) NOT NULL AUTO_INCREMENT,
    `emailto` VARCHAR(255) DEFAULT NULL,
    `emailfrom` VARCHAR(255) DEFAULT NULL,
    `emailcc` VARCHAR(255) DEFAULT NULL,
    `emailsubject` VARCHAR(255) DEFAULT NULL,
    `status` INT(11) NOT NULL,
    `emailtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`emailqueueid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `EmailQueue`
--

DROP TABLE IF EXISTS `EmailQueue`;
CREATE TABLE `EmailQueue` (
    `emailqueueid` INT(11) NOT NULL AUTO_INCREMENT,
    `emailto` VARCHAR(255) DEFAULT NULL,
    `emailfrom` VARCHAR(255) DEFAULT NULL,
    `emailcc` VARCHAR(255) DEFAULT NULL,
    `emailsubject` VARCHAR(255) DEFAULT NULL,
    `body` TEXT,
    `status` INT(11) NOT NULL,
    `emailtimestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`emailqueueid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `EmailTo`
--

DROP TABLE IF EXISTS `EmailTo`;
CREATE TABLE `EmailTo` (
    `emailtoid` INT(11) NOT NULL AUTO_INCREMENT,
    `emailtodescription` VARCHAR(75) DEFAULT NULL,
    `display_order` INT(11) NOT NULL DEFAULT '0',
    `emailtoquery` TEXT,
    PRIMARY KEY (`emailtoid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Features`
--

DROP TABLE IF EXISTS `Features`;
CREATE TABLE `Features` (
    `featureid` INT(11) NOT NULL AUTO_INCREMENT,
    `featurename` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`featureid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `KidsCategories`
--

DROP TABLE IF EXISTS `KidsCategories`;
CREATE TABLE `KidsCategories` (
    `kidscatid` INT(11) NOT NULL AUTO_INCREMENT,
    `kidscatname` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`kidscatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `LanguageStatuses`
--

DROP TABLE IF EXISTS `LanguageStatuses`;
CREATE TABLE `LanguageStatuses` (
    `languagestatusid` INT(11) NOT NULL AUTO_INCREMENT,
    `languagestatusname` VARCHAR(30) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`languagestatusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table ParticipantPasswordResetRequests
--

DROP TABLE IF EXISTS ParticipantPasswordResetRequests;

CREATE TABLE `ParticipantPasswordResetRequests` (
    `badgeidentered` VARCHAR(15) NOT NULL DEFAULT '' COMMENT 'Not necessarily a valid badgeid, so no Foreign Key',
    `email` VARCHAR(255) NOT NULL DEFAULT '',
    `ipaddress` VARCHAR(225) NOT NULL DEFAULT '',
    `creationdatetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `expirationdatetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `selector` CHAR(16) NOT NULL DEFAULT '',
    `token` CHAR(64) NOT NULL DEFAULT '',
    `cancelled` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (badgeidentered, creationdatetime),
    UNIQUE KEY PPRR_selector (selector)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PhotoDenialReasons`
--

DROP TABLE IF EXISTS `PhotoDenialReasons`;
CREATE TABLE `PhotoDenialReasons` (
    `photodenialreasonid` INT(11) NOT NULL AUTO_INCREMENT,
    `reasontext` VARCHAR(512) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`photodenialreasonid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PhotoUploadStatus`
--

DROP TABLE IF EXISTS `PhotoUploadStatus`;
CREATE TABLE `PhotoUploadStatus` (
    `photouploadstatus` INT(11) NOT NULL,
    `statustext` VARCHAR(64) NOT NULL,
    PRIMARY KEY (`photouploadstatus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Participants`
--

DROP TABLE IF EXISTS `Participants`;
CREATE TABLE `Participants` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `password` VARCHAR(255) DEFAULT NULL,
    `bestway` VARCHAR(12) DEFAULT NULL,
    `interested` TINYINT(1) DEFAULT NULL,
    `bio` TEXT,
    `htmlbio` TEXT,
    `pubsname` VARCHAR(50) DEFAULT NULL,
    `name_for_sorting` VARCHAR(50) DEFAULT NULL,
    `uploadedphotofilename` VARCHAR(64) DEFAULT NULL,
    `approvedphotofilename` VARCHAR(64) DEFAULT NULL,
    `photodenialreasonothertext` VARCHAR(512) DEFAULT NULL,
    `photodenialreasonid` INT(11) DEFAULT NULL,
    `photouploadstatus` INT(11) DEFAULT NULL,
    `share_email` TINYINT(1) DEFAULT '1',
    `staff_notes` TEXT,
    `use_photo` TINYINT(1) DEFAULT NULL,
    `data_retention` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`badgeid`),
    KEY `photodenialreasonidinx` (`photodenialreasonid`),
    KEY `photouploadstatusinx` (`photouploadstatus`),
    CONSTRAINT `participantsphotouploadstatus_fk` FOREIGN KEY (`photouploadstatus`) REFERENCES `PhotoUploadStatus` (`photouploadstatus`),
    CONSTRAINT `participantsphotodenialreasonid_fk` FOREIGN KEY (`photodenialreasonid`) REFERENCES `PhotoDenialReasons` (`photodenialreasonid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantTags`
--

DROP TABLE IF EXISTS `ParticipantTags`;
CREATE TABLE `ParticipantTags` (
    `participanttagid` INT(11) NOT NULL AUTO_INCREMENT,
    `participanttagname` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`participanttagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PatchLog`
--

DROP TABLE IF EXISTS `PatchLog`;
CREATE TABLE `PatchLog` (
    `patchname` VARCHAR(40) DEFAULT NULL,
    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PermissionAtoms`
--

DROP TABLE IF EXISTS `PermissionAtoms`;
CREATE TABLE `PermissionAtoms` (
    `permatomid` INT(11) NOT NULL AUTO_INCREMENT,
    `permatomtag` VARCHAR(32) NOT NULL DEFAULT '',
    `elementid` INT(11) DEFAULT NULL,
    `page` VARCHAR(30) DEFAULT NULL,
    `notes` TEXT,
    `display_order` INT(11),
    PRIMARY KEY (`permatomid`),
    UNIQUE KEY `taginx` (`permatomtag`,`elementid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PermissionRoles`
--

DROP TABLE IF EXISTS `PermissionRoles`;
CREATE TABLE `PermissionRoles` (
    `permroleid` INT(11) NOT NULL AUTO_INCREMENT,
    `permrolename` VARCHAR(100) DEFAULT NULL,
    `notes` TEXT,
    `display_order` INT(11),
    PRIMARY KEY (`permroleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Phases`
--

DROP TABLE IF EXISTS `Phases`;
CREATE TABLE `Phases` (
    `phaseid` INT(11) NOT NULL AUTO_INCREMENT,
    `phasename` VARCHAR(100) DEFAULT NULL,
    `current` TINYINT(1) DEFAULT '0',
    `notes` TEXT,
    `implemented` TINYINT(1) DEFAULT '0',
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`phaseid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Configuration info.';

--
-- Table structure for table `PreviousCons`
--

DROP TABLE IF EXISTS `PreviousCons`;
CREATE TABLE `PreviousCons` (
    `previousconid` INT(11) NOT NULL AUTO_INCREMENT,
    `previousconname` VARCHAR(128) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`previousconid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PreviousParticipants`
--

DROP TABLE IF EXISTS `PreviousParticipants`;
CREATE TABLE `PreviousParticipants` (
    `badgeid` VARCHAR(15) NOT NULL,
    `bio` TEXT,
    `staff_notes` TEXT,
    PRIMARY KEY (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PubStatuses`
--

DROP TABLE IF EXISTS `PubStatuses`;
CREATE TABLE `PubStatuses` (
    `pubstatusid` INT(11) NOT NULL AUTO_INCREMENT,
    `pubstatusname` VARCHAR(12) DEFAULT NULL,
    `display_order` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`pubstatusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `RegTypes`
--

DROP TABLE IF EXISTS `RegTypes`;
CREATE TABLE `RegTypes` (
    `regtypeid` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `display_order` INT(11) DEFAULT '0',
    `regtype` VARCHAR(40) NOT NULL DEFAULT '',
    `message` VARCHAR(100) DEFAULT NULL,
    UNIQUE KEY `RegTypes_Regtype` (`regtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Roles`
--

DROP TABLE IF EXISTS `Roles`;
CREATE TABLE `Roles` (
    `roleid` INT(11) NOT NULL AUTO_INCREMENT,
    `rolename` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `RoomSets`
--

DROP TABLE IF EXISTS `RoomSets`;
CREATE TABLE `RoomSets` (
    `roomsetid` INT(11) NOT NULL AUTO_INCREMENT,
    `roomsetname` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`roomsetid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Rooms`
--

DROP TABLE IF EXISTS `Rooms`;
CREATE TABLE `Rooms` (
    `roomid` INT(11) NOT NULL AUTO_INCREMENT,
    `roomname` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    `height` VARCHAR(100) DEFAULT NULL,
    `dimensions` VARCHAR(100) DEFAULT NULL,
    `area` VARCHAR(100) DEFAULT NULL,
    `function` VARCHAR(100) DEFAULT NULL,
    `floor` VARCHAR(50) DEFAULT NULL,
    `notes` TEXT,
    `is_scheduled` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`roomid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Services`
--

DROP TABLE IF EXISTS `Services`;
CREATE TABLE `Services` (
    `serviceid` INT(11) NOT NULL AUTO_INCREMENT,
    `servicename` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`serviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SessionEditCodes`
--

DROP TABLE IF EXISTS `SessionEditCodes`;
CREATE TABLE `SessionEditCodes` (
    `sessioneditcode` INT(11) NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(40) DEFAULT NULL,
    `display_order` INT(11) NOT NULL DEFAULT '1',
    PRIMARY KEY (`sessioneditcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SessionStatuses`
--

DROP TABLE IF EXISTS `SessionStatuses`;
CREATE TABLE `SessionStatuses` (
    `statusid` INT(11) NOT NULL AUTO_INCREMENT,
    `statusname` VARCHAR(50) DEFAULT NULL,
    `validate` TINYINT(1) NOT NULL DEFAULT '0',
    `may_be_scheduled` TINYINT(1) NOT NULL DEFAULT '0',
    `display_order` INT(11) DEFAULT NULL,
    PRIMARY KEY (`statusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SurveyQuestionTypes`
--

DROP TABLE IF EXISTS `SurveyQuestionTypes`;
CREATE TABLE `SurveyQuestionTypes` (
    `typeid` INT(11) NOT NULL AUTO_INCREMENT,
    `shortname` VARCHAR(100) NOT NULL,
    `description` VARCHAR(1024) DEFAULT NULL,
    `current` tinyint DEFAULT '0',
    `display_order` INT(11) NOT NULL,
    PRIMARY KEY (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Possible types of questions for the survey';

--
-- Table structure for table `Tags`
--

DROP TABLE IF EXISTS `Tags`;
CREATE TABLE `Tags` (
    `tagid` INT(11) NOT NULL AUTO_INCREMENT,
    `tagname` VARCHAR(30) CHARACTER SET utf8 collate utf8_general_ci default NULL,
    `display_order` INT(11) NOT NULL default '0',
    PRIMARY KEY  (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Times`
--

DROP TABLE IF EXISTS `Times`;
CREATE TABLE `Times` (
    `timeid` INT(11) NOT NULL,
    `timedisplay` CHAR(14) DEFAULT NULL,
    `timevalue` TIME DEFAULT NULL,
    `next_day` TINYINT(1) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    `avail_start` TINYINT(1) DEFAULT NULL,
    `avail_end` TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (`timeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Tracks`
--

DROP TABLE IF EXISTS `Tracks`;
CREATE TABLE `Tracks` (
    `trackid` INT(11) NOT NULL AUTO_INCREMENT,
    `trackname` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    `selfselect` TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (`trackid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Types`
--

DROP TABLE IF EXISTS `Types`;
CREATE TABLE `Types` (
    `typeid` INT(11) NOT NULL AUTO_INCREMENT,
    `typename` VARCHAR(50) DEFAULT NULL,
    `display_order` INT(11) DEFAULT NULL,
    `selfselect` TINYINT(1) DEFAULT NULL,
    PRIMARY KEY (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `CongoDumpHistory`
--

DROP TABLE IF EXISTS `CongoDumpHistory`;
CREATE TABLE `CongoDumpHistory` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `firstname` VARCHAR(30) DEFAULT NULL,
    `lastname` VARCHAR(40) DEFAULT NULL,
    `badgename` VARCHAR(51) DEFAULT NULL,
    `phone` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `postaddress1` VARCHAR(100) DEFAULT NULL,
    `postaddress2` VARCHAR(100) DEFAULT NULL,
    `postcity` VARCHAR(50) DEFAULT NULL,
    `poststate` VARCHAR(25) DEFAULT NULL,
    `postzip` VARCHAR(10) DEFAULT NULL,
    `postcountry` VARCHAR(25) DEFAULT NULL,
    `regtype` VARCHAR(40) DEFAULT NULL,
    `createdts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `createdbybadgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `inactivatedts` TIMESTAMP NULL DEFAULT NULL,
    `inactivatedbybadgeid` VARCHAR(15) DEFAULT NULL,
    PRIMARY KEY (`badgeid`,`createdts`),
    KEY `badgeid` (`badgeid`),
    KEY `createdbybadgeid` (`createdbybadgeid`),
    KEY `inactivatedbybadgeid` (`inactivatedbybadgeid`),
    CONSTRAINT `CongoDumpHistory_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `CongoDumpHistory_ibfk_2` FOREIGN KEY (`createdbybadgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `CongoDumpHistory_ibfk_3` FOREIGN KEY (`inactivatedbybadgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Contains changes to user information from registration system.';

--
-- Table structure for table `Sessions`
--

DROP TABLE IF EXISTS `Sessions`;
CREATE TABLE `Sessions` (
    `sessionid` INT(11) NOT NULL AUTO_INCREMENT,
    `trackid` INT(11) NOT NULL DEFAULT '0',
    `typeid` INT(11) NOT NULL DEFAULT '0',
    `divisionid` INT(11) NOT NULL DEFAULT '0',
    `pubstatusid` INT(11) DEFAULT '0',
    `languagestatusid` INT(11) DEFAULT '1',
    `pubsno` VARCHAR(50) DEFAULT NULL,
    `title` VARCHAR(100) DEFAULT NULL,
    `secondtitle` VARCHAR(100) DEFAULT NULL,
    `pocketprogtext` TEXT,
    `progguiddesc` TEXT,
    `progguidhtml` TEXT,
    `persppartinfo` TEXT,
    `meetinglink` VARCHAR(512) DEFAULT NULL,
    `panelistlink` VARCHAR(512) DEFAULT NULL,
    `captionlink` VARCHAR(512) DEFAULT NULL,
    `recordinglink` VARCHAR(512) DEFAULT NULL,
    `duration` TIME DEFAULT NULL,
    `estatten` INT(11) DEFAULT NULL,
    `kidscatid` INT(11) NOT NULL DEFAULT '0',
    `signupreq` TINYINT(1) DEFAULT NULL,
    `roomsetid` INT(11) NOT NULL DEFAULT '0',
    `notesforpart` TEXT,
    `servicenotes` TEXT,
    `statusid` INT(11) NOT NULL DEFAULT '0',
    `notesforprog` TEXT,
    `warnings` TINYINT(1) DEFAULT NULL,
    `invitedguest` TINYINT(1) DEFAULT '0',
    `ts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`sessionid`),
    KEY `trackid` (`trackid`),
    KEY `typeid` (`typeid`),
    KEY `kidscatid` (`kidscatid`),
    KEY `roomsetid` (`roomsetid`),
    KEY `statusid` (`statusid`),
    KEY `divisionid` (`divisionid`),
    KEY `pubstatusid` (`pubstatusid`),
    KEY `languagestatusid` (`languagestatusid`),
    CONSTRAINT `Sessions_ibfk_1` FOREIGN KEY (`trackid`) REFERENCES `Tracks` (`trackid`),
    CONSTRAINT `Sessions_ibfk_2` FOREIGN KEY (`typeid`) REFERENCES `Types` (`typeid`),
    CONSTRAINT `Sessions_ibfk_3` FOREIGN KEY (`kidscatid`) REFERENCES `KidsCategories` (`kidscatid`),
    CONSTRAINT `Sessions_ibfk_4` FOREIGN KEY (`roomsetid`) REFERENCES `RoomSets` (`roomsetid`),
    CONSTRAINT `Sessions_ibfk_5` FOREIGN KEY (`statusid`) REFERENCES `SessionStatuses` (`statusid`),
    CONSTRAINT `Sessions_ibfk_6` FOREIGN KEY (`pubstatusid`) REFERENCES `PubStatuses` (`pubstatusid`),
    CONSTRAINT `Sessions_ibfk_7` FOREIGN KEY (`divisionid`) REFERENCES `Divisions` (`divisionid`),
    CONSTRAINT `Sessions_ibfk_8` FOREIGN KEY (`languagestatusid`) REFERENCES `LanguageStatuses` (`languagestatusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantAvailability`
--

DROP TABLE IF EXISTS `ParticipantAvailability`;
CREATE TABLE `ParticipantAvailability` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `maxprog` INT(11) DEFAULT NULL,
    `preventconflict` VARCHAR(255) DEFAULT NULL,
    `otherconstraints` VARCHAR(255) DEFAULT NULL,
    `numkidsfasttrack` INT(11) DEFAULT NULL,
    PRIMARY KEY (`badgeid`),
    CONSTRAINT `ParticipantAvailability_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantAvailabilityDays`
--

DROP TABLE IF EXISTS `ParticipantAvailabilityDays`;
CREATE TABLE `ParticipantAvailabilityDays` (
    `badgeid` VARCHAR(15) NOT NULL,
    `day` smallINT(6) NOT NULL,
    `maxprog` INT(11) DEFAULT NULL,
    PRIMARY KEY (`badgeid`,`day`),
    CONSTRAINT `ParticipantAvailabilityDays_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantAvailabilityTimes`
--

DROP TABLE IF EXISTS `ParticipantAvailabilityTimes`;
CREATE TABLE `ParticipantAvailabilityTimes` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `availabilitynum` INT(11) NOT NULL DEFAULT '0',
    `starttime` TIME DEFAULT NULL,
    `endtime` TIME DEFAULT NULL,
    PRIMARY KEY (`badgeid`,`availabilitynum`),
    CONSTRAINT `ParticipantAvailabilityTimes_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantHasCredential`
--

DROP TABLE IF EXISTS `ParticipantHasCredential`;
CREATE TABLE `ParticipantHasCredential` (
    `badgeid` VARCHAR(15) NOT NULL,
    `credentialid` INT(11) NOT NULL,
    PRIMARY KEY (`badgeid`,`credentialid`),
    KEY `phcfk2` (`credentialid`),
    CONSTRAINT `phcfk1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `phcfk2` FOREIGN KEY (`credentialid`) REFERENCES `Credentials` (`credentialid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantHasRole`
--

DROP TABLE IF EXISTS `ParticipantHasRole`;
CREATE TABLE `ParticipantHasRole` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `roleid` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`badgeid`,`roleid`),
    KEY `roleid` (`roleid`),
    CONSTRAINT `ParticipantHasRole_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `ParticipantHasRole_ibfk_2` FOREIGN KEY (`roleid`) REFERENCES `Roles` (`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantHasTag`
--

DROP TABLE IF EXISTS `ParticipantHasTag`;
CREATE TABLE `ParticipantHasTag` (
    `badgeid` VARCHAR(15) DEFAULT 'NULL',
    `participanttagid` INT(11) DEFAULT NULL,
    KEY `ParticipantHasTag_badgeid_participanttagid_idx` (`badgeid`,`participanttagid`) USING BTREE,
    KEY `participanttagid` (`participanttagid`),
    CONSTRAINT `participanthastag_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `participanthastag_ibfk_2` FOREIGN KEY (`participanttagid`) REFERENCES `ParticipantTags` (`participanttagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantInterests`
--

DROP TABLE IF EXISTS `ParticipantInterests`;
CREATE TABLE `ParticipantInterests` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `yespanels` TEXT,
    `nopanels` TEXT,
    `yespeople` TEXT,
    `nopeople` TEXT,
    `otherroles` TEXT,
    PRIMARY KEY (`badgeid`),
    CONSTRAINT `ParticipantInterests_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantOnSession`
--

DROP TABLE IF EXISTS `ParticipantOnSession`;
CREATE TABLE `ParticipantOnSession` (
    `participantonsessionid` INT(11) NOT NULL AUTO_INCREMENT,
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `moderator` TINYINT(1) DEFAULT '0',
    PRIMARY KEY (`participantonsessionid`),
    UNIQUE KEY `uniqueness` (`badgeid`,`sessionid`),
    KEY `sessionid` (`sessionid`),
    CONSTRAINT `ParticipantOnSession_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `ParticipantOnSession_ibfk_2` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Participant info captured by Zambia.  Set by staff.';

DELIMITER ;;
CREATE TRIGGER POS_insert_trig AFTER INSERT ON ParticipantOnSession FOR EACH ROW
    IF (
        SELECT count(*) FROM ParticipantOnSession
            WHERE sessionid = NEW.sessionid AND moderator = 1)
        > 1 THEN
        SIGNAL sqlstate '45000' SET MESSAGE_TEXT = 'Attempted to insert more than one record with moderator = 1 for a single session into ParticipantOnSession.';
    END IF;
;;
DELIMITER ;

--
-- Table structure for table ParticipantOnSessionHistory
--

DROP TABLE IF EXISTS `ParticipantOnSessionHistory`;

CREATE TABLE `ParticipantOnSessionHistory` (
    `participantonsessionhistoryid` INT(11) NOT NULL AUTO_INCREMENT,
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `moderator` TINYINT(1) NOT NULL DEFAULT '0',
    `participantonsessionid` INT(11) DEFAULT NULL,
    `createdts` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `createdbybadgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `inactivatedts` TIMESTAMP NULL DEFAULT NULL,
    `inactivatedbybadgeid` VARCHAR(15) DEFAULT NULL,
    PRIMARY KEY (`participantonsessionhistoryid`),
    KEY `badgeid` (`badgeid`),
    KEY `sessionid` (`sessionid`),
    KEY `ParticipantOnSessionHistory_ibfk_3` (`createdbybadgeid`),
    KEY `ParticipantOnSessionHistory_ibfk_4` (`inactivatedbybadgeid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_2` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_3` FOREIGN KEY (`createdbybadgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `ParticipantOnSessionHistory_ibfk_4` FOREIGN KEY (`inactivatedbybadgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DELIMITER ;;
CREATE TRIGGER `POSH_insert_trig` BEFORE INSERT ON `ParticipantOnSessionHistory` FOR EACH ROW BEGIN
        IF NEW.inactivatedts IS NOT NULL OR NEW.inactivatedbybadgeid IS NOT NULL THEN
            SIGNAL sqlstate '45000' SET MESSAGE_TEXT = 'Insert table ParticipantOnSessionHistory with non null inactivated... field.';
        END IF;
        INSERT INTO ParticipantOnSession (badgeid, sessionid, moderator)
            VALUES (NEW.badgeid, NEW.sessionid, NEW.moderator);
    END ;;
CREATE TRIGGER `POSH_update_trig` BEFORE UPDATE ON `ParticipantOnSessionHistory` FOR EACH ROW BEGIN
        IF NEW.inactivatedts IS NULL OR NEW.inactivatedbybadgeid IS NULL THEN
            SIGNAL sqlstate '45000' SET message_text = 'Update table ParticipantOnSessionHistory with null inactivated... field.';
        END IF;
        IF OLD.inactivatedts IS NOT NULL OR OLD.inactivatedbybadgeid IS NOT NULL THEN
            SIGNAL sqlstate '45000' SET message_text = 'Update table ParticipantOnSessionHistory when record previously inactivated.';
        END IF;
        IF NOT(NEW.participantonsessionhistoryid <=> OLD.participantonsessionhistoryid) OR
            NOT(NEW.badgeid <=> OLD.badgeid) OR
            NOT(NEW.sessionid <=> OLD.sessionid) OR
            NOT(NEW.moderator <=> OLD.moderator) OR
            NOT(NEW.createdts <=> OLD.createdts) OR
            NOT(NEW.createdbybadgeid <=> OLD.createdbybadgeid) THEN
            SIGNAL sqlstate '45000' SET message_text = 'Update field other than inactivated... in table ParticipantOnSessionHistory.';
        END IF;
        DELETE FROM ParticipantOnSession
            WHERE
                    badgeid = OLD.badgeid
                AND sessionid = OLD.sessionid
                AND moderator = OLD.moderator;
    END ;;
CREATE TRIGGER `POSH_delete_trig` BEFORE DELETE ON `ParticipantOnSessionHistory` FOR EACH ROW SIGNAL sqlstate '45000' SET MESSAGE_TEXT = 'May only insert and update table ParticipantOnSessionHistory.' ;;
DELIMITER ;

--
-- Table structure for table `ParticipantSessionInterest`
--

DROP TABLE IF EXISTS `ParticipantSessionInterest`;
CREATE TABLE `ParticipantSessionInterest` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `rank` INT(11) DEFAULT NULL,
    `willmoderate` TINYINT(1) DEFAULT NULL,
    `comments` TEXT,
    PRIMARY KEY (`badgeid`,`sessionid`),
    KEY `sessionid` (`sessionid`),
    CONSTRAINT `ParticipantSessionInterest_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `ParticipantSessionInterest_ibfk_2` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `ParticipantSuggestions`
--

DROP TABLE IF EXISTS `ParticipantSuggestions`;
CREATE TABLE `ParticipantSuggestions` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `paneltopics` TEXT,
    `otherideas` TEXT,
    `suggestedguests` TEXT,
    PRIMARY KEY (`badgeid`),
    CONSTRAINT `ParticipantSuggestions_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Permissions`
--

DROP TABLE IF EXISTS `Permissions`;
CREATE TABLE `Permissions` (
    `permissionid` INT(11) NOT NULL AUTO_INCREMENT,
    `permatomid` INT(11) NOT NULL DEFAULT '0',
    `phaseid` INT(11) COMMENT 'null indicates all phases',
    `permroleid` INT(11) COMMENT 'one and only one of permroleid or badgeid should be non-null',
    `badgeid` VARCHAR(15) COMMENT 'one and only one of permroleid or badgeid should be non-null',
    PRIMARY KEY (`permissionid`),
    UNIQUE KEY `unique1` (`permatomid`,`phaseid`,`permroleid`,`badgeid`),
    KEY `FK_Permissions` (`phaseid`),
    KEY `FK_PRoles` (`permroleid`),
    KEY `FK_Parts` (`badgeid`),
    CONSTRAINT `Permissions_ibfk_1` FOREIGN KEY (`permatomid`) REFERENCES `PermissionAtoms` (`permatomid`),
    CONSTRAINT `Permissions_ibfk_2` FOREIGN KEY (`phaseid`) REFERENCES `Phases` (`phaseid`),
    CONSTRAINT `Permissions_ibfk_3` FOREIGN KEY (`permroleid`) REFERENCES `PermissionRoles` (`permroleid`),
    CONSTRAINT `Permissions_ibfk_4` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PreviousConTracks`
--

DROP TABLE IF EXISTS `PreviousConTracks`;
CREATE TABLE `PreviousConTracks` (
    `previousconid` INT(11) NOT NULL,
    `previoustrackid` INT(11) NOT NULL,
    `trackname` VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (`previousconid`,`previoustrackid`),
    KEY `previousconid` (`previousconid`),
    CONSTRAINT `PreviousCons_ibfk_1` FOREIGN KEY (`previousconid`) REFERENCES `PreviousCons` (`previousconid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `PreviousSessions`
--

DROP TABLE IF EXISTS `PreviousSessions`;
CREATE TABLE `PreviousSessions` (
    `previousconid` INT(11) NOT NULL,
    `previoussessionid` INT(11) NOT NULL,
    `previoustrackid` INT(11) NOT NULL,
    `previousstatusid` INT(11) NOT NULL,
    `typeid` INT(11) NOT NULL,
    `divisionid` INT(11) NOT NULL,
    `languagestatusid` INT(11) DEFAULT NULL,
    `title` VARCHAR(100) DEFAULT NULL,
    `secondtitle` VARCHAR(100) DEFAULT NULL,
    `pocketprogtext` TEXT,
    `progguiddesc` TEXT,
    `progguidhtml` TEXT,
    `persppartinfo` TEXT,
    `duration` TIME DEFAULT NULL,
    `estatten` INT(11) DEFAULT NULL,
    `kidscatid` INT(11) NOT NULL,
    `signupreq` TINYINT(1) DEFAULT NULL,
    `notesforpart` TEXT,
    `notesforprog` TEXT,
    `invitedguest` TINYINT(1) DEFAULT NULL,
    `importedsessionid` INT(11) DEFAULT NULL,
    PRIMARY KEY (`previousconid`,`previoussessionid`),
    KEY `previousconid` (`previousconid`),
    KEY `previoustrackid` (`previousconid`,`previoustrackid`),
    KEY `previousstatusid` (`previousstatusid`),
    KEY `typeid` (`typeid`),
    KEY `divisionid` (`divisionid`),
    KEY `languagestatusid` (`languagestatusid`),
    KEY `kidscatid` (`kidscatid`),
    CONSTRAINT `PreviousSessions_ibfk_1` FOREIGN KEY (`previousconid`) REFERENCES `PreviousCons` (`previousconid`),
    CONSTRAINT `PreviousSessions_ibfk_2` FOREIGN KEY (`previousconid`, `previoustrackid`) REFERENCES `PreviousConTracks` (`previousconid`, `previoustrackid`),
    CONSTRAINT `PreviousSessions_ibfk_3` FOREIGN KEY (`previousstatusid`) REFERENCES `SessionStatuses` (`statusid`),
    CONSTRAINT `PreviousSessions_ibfk_4` FOREIGN KEY (`typeid`) REFERENCES `Types` (`typeid`),
    CONSTRAINT `PreviousSessions_ibfk_5` FOREIGN KEY (`divisionid`) REFERENCES `Divisions` (`divisionid`),
    CONSTRAINT `PreviousSessions_ibfk_6` FOREIGN KEY (`languagestatusid`) REFERENCES `LanguageStatuses` (`languagestatusid`),
    CONSTRAINT `PreviousSessions_ibfk_7` FOREIGN KEY (`kidscatid`) REFERENCES `KidsCategories` (`kidscatid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `RoomHasSet`
--

DROP TABLE IF EXISTS `RoomHasSet`;
CREATE TABLE `RoomHasSet` (
    `roomhassetid` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `roomid` INT(11) NOT NULL DEFAULT '0',
    `roomsetid` INT(11) NOT NULL DEFAULT '0',
    `capacity` INT(11) DEFAULT NULL,
    UNIQUE KEY (`roomid`,`roomsetid`),
    KEY `roomsetid` (`roomsetid`),
    CONSTRAINT `RoomHasSet_ibfk_1` FOREIGN KEY (`roomid`) REFERENCES `Rooms` (`roomid`),
    CONSTRAINT `RoomHasSet_ibfk_2` FOREIGN KEY (`roomsetid`) REFERENCES `RoomSets` (`roomsetid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `Schedule`
--

DROP TABLE IF EXISTS `Schedule`;
CREATE TABLE `Schedule` (
    `scheduleid` INT(11) NOT NULL AUTO_INCREMENT,
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `roomid` INT(11) NOT NULL DEFAULT '0',
    `starttime` TIME NOT NULL DEFAULT '00:00:00',
    PRIMARY KEY (`scheduleid`),
    KEY `sessionid` (`sessionid`),
    KEY `roomid` (`roomid`),
    CONSTRAINT `Schedule_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `Schedule_ibfk_2` FOREIGN KEY (`roomid`) REFERENCES `Rooms` (`roomid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SessionEditHistory`
--

DROP TABLE IF EXISTS `SessionEditHistory`;
CREATE TABLE `SessionEditHistory` (
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `badgeid` VARCHAR(15) DEFAULT NULL,
    `name` VARCHAR(40) DEFAULT NULL,
    `email_address` VARCHAR(75) DEFAULT NULL,
    `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `sessioneditcode` INT(11) NOT NULL DEFAULT '0',
    `statusid` INT(11) NOT NULL DEFAULT '0',
    `editdescription` TEXT,
    PRIMARY KEY (`sessionid`,`timestamp`),
    KEY `FK_SessionEditHistory` (`badgeid`),
    KEY `FK_SessionEditCodes` (`sessioneditcode`),
    KEY `FK_SessionEditHistory4` (`statusid`),
    CONSTRAINT `SessionEditHistory_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `SessionEditHistory_ibfk_2` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `SessionEditHistory_ibfk_3` FOREIGN KEY (`sessioneditcode`) REFERENCES `SessionEditCodes` (`sessioneditcode`),
    CONSTRAINT `SessionEditHistory_ibfk_4` FOREIGN KEY (`statusid`) REFERENCES `SessionStatuses` (`statusid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SessionHasFeature`
--

DROP TABLE IF EXISTS `SessionHasFeature`;
CREATE TABLE `SessionHasFeature` (
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `featureid` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`sessionid`,`featureid`),
    KEY `featureid` (`featureid`),
    CONSTRAINT `SessionHasFeature_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `SessionHasFeature_ibfk_2` FOREIGN KEY (`featureid`) REFERENCES `Features` (`featureid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SessionHasService`
--

DROP TABLE IF EXISTS `SessionHasService`;
CREATE TABLE `SessionHasService` (
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `serviceid` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`sessionid`,`serviceid`),
    KEY `serviceid` (`serviceid`),
    CONSTRAINT `SessionHasService_ibfk_1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `SessionHasService_ibfk_2` FOREIGN KEY (`serviceid`) REFERENCES `Services` (`serviceid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SessionHasTag`
--

DROP TABLE IF EXISTS `SessionHasTag`;
CREATE TABLE `SessionHasTag` (
    `sessionid` INT(11) NOT NULL DEFAULT '0',
    `tagid` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY  (`sessionid`,`tagid`),
    KEY `Fkey2` (`tagid`),
    CONSTRAINT `Fkey1` FOREIGN KEY (`sessionid`) REFERENCES `Sessions` (`sessionid`),
    CONSTRAINT `Fkey2` FOREIGN KEY (`tagid`) REFERENCES `Tags` (`tagid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `SurveyQuestionConfig`
--

DROP TABLE IF EXISTS `SurveyQuestionConfig`;
CREATE TABLE `SurveyQuestionConfig` (
    `questionid` INT(11) NOT NULL AUTO_INCREMENT,
    `shortname` VARCHAR(100) NOT NULL,
    `description` VARCHAR(1024) DEFAULT NULL,
    `prompt` VARCHAR(512) NOT NULL,
    `hover` VARCHAR(8192) DEFAULT NULL,
    `display_order` INT(11) NOT NULL,
    `typeid` INT(11) NOT NULL,
    `required` TINYINT(1) NOT NULL DEFAULT '0',
    `publish` TINYINT(1) NOT NULL DEFAULT '0',
    `privacy_user` TINYINT(1) NOT NULL DEFAULT '0',
    `searchable` TINYINT(1) NOT NULL DEFAULT '0',
    `ascending` TINYINT(1) NOT NULL DEFAULT '1',
    `display_only` TINYINT(1) NOT NULL DEFAULT '0',
    `min_value` INT(11) DEFAULT NULL,
    `max_value` INT(11) DEFAULT NULL,
    PRIMARY KEY (`questionid`),
    UNIQUE KEY `SurveyQuestionConfig_shortname` (`shortname`),
    KEY `typeid` (`typeid`),
    CONSTRAINT `surveyquestionconfig_ibfk_1` FOREIGN KEY (`typeid`) REFERENCES `SurveyQuestionTypes` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Each question in the configured survey has a row.';

--
-- Table structure for table `SurveyQuestionOptionConfig`
--

DROP TABLE IF EXISTS `SurveyQuestionOptionConfig`;
CREATE TABLE `SurveyQuestionOptionConfig` (
    `questionid` INT(11) NOT NULL,
    `ordinal` INT(11) NOT NULL,
    `value` VARCHAR(512) NOT NULL,
    `display_order` INT(11) NOT NULL,
    `optionshort` VARCHAR(64) NOT NULL,
    `optionhover` VARCHAR(512) DEFAULT NULL,
    `allowothertext` TINYINT(1) DEFAULT '0',
    PRIMARY KEY (`questionid`,`ordinal`),
    CONSTRAINT `surveyquestionoptionconfig_ibfk_1` FOREIGN KEY (`questionid`) REFERENCES `SurveyQuestionConfig` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Entries for questions in the survey which are multiple choice.';

--
-- Table structure for table `SurveyQuestionTypeDefaults`
--

DROP TABLE IF EXISTS `SurveyQuestionTypeDefaults`;
CREATE TABLE `SurveyQuestionTypeDefaults` (
    `typeid` INT(11) NOT NULL,
    `ordinal` INT(11) NOT NULL,
    `value` VARCHAR(512) NOT NULL,
    `display_order` INT(11) NOT NULL,
    `optionshort` VARCHAR(64) NOT NULL,
    `optionhover` VARCHAR(512) DEFAULT NULL,
    `allowothertext` TINYINT(1) DEFAULT '0',
    PRIMARY KEY (`typeid`,`ordinal`),
    CONSTRAINT `surveyquestiontypedefaults_ibfk_1` FOREIGN KEY (`typeid`) REFERENCES `SurveyQuestionTypes` (`typeid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Default options for multiple choice question types in the survey.';

--
-- Table structure for table `ParticipantSurveyAnswers`
--

DROP TABLE IF EXISTS `ParticipantSurveyAnswers`;
CREATE TABLE `ParticipantSurveyAnswers` (
    `participantid` VARCHAR(15) NOT NULL DEFAULT '',
    `questionid` INT(11) NOT NULL,
    `privacy_setting` TINYINT(1) NOT NULL DEFAULT '0',
    `value` VARCHAR(8192) DEFAULT NULL,
    `othertext` VARCHAR(512) DEFAULT NULL,
    `lastupdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `updatedby` VARCHAR(15) NOT NULL,
    PRIMARY KEY (`participantid`,`questionid`),
    KEY `questionid` (`questionid`),
    CONSTRAINT `participantsurveyanswers_ibfk_1` FOREIGN KEY (`participantid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `participantsurveyanswers_ibfk_2` FOREIGN KEY (`questionid`) REFERENCES `SurveyQuestionConfig` (`questionid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Participant Survey Info.';

--
-- Table structure for table `TrackCompatibility`
--

DROP TABLE IF EXISTS `TrackCompatibility`;
CREATE TABLE `TrackCompatibility` (
    `previousconid` INT(11) NOT NULL,
    `previoustrackid` INT(11) NOT NULL,
    `currenttrackid` INT(11) NOT NULL,
    PRIMARY KEY (`previousconid`,`previoustrackid`),
    KEY `currenttrackid` (`currenttrackid`),
    CONSTRAINT `TrackCompatibility_ibfk_1` FOREIGN KEY (`previousconid`, `previoustrackid`) REFERENCES `PreviousConTracks` (`previousconid`, `previoustrackid`),
    CONSTRAINT `TrackCompatibility_ibfk_2` FOREIGN KEY (`currenttrackid`) REFERENCES `Tracks` (`trackid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `UserHasPermissionRole`
--

DROP TABLE IF EXISTS `UserHasPermissionRole`;
CREATE TABLE `UserHasPermissionRole` (
    `badgeid` VARCHAR(15) NOT NULL DEFAULT '',
    `permroleid` INT(11) NOT NULL DEFAULT '0',
    PRIMARY KEY (`badgeid`,`permroleid`),
    KEY `FK_UserHasPermissionRole` (`permroleid`),
    CONSTRAINT `UserHasPermissionRole_ibfk_1` FOREIGN KEY (`badgeid`) REFERENCES `Participants` (`badgeid`),
    CONSTRAINT `UserHasPermissionRole_ibfk_2` FOREIGN KEY (`permroleid`) REFERENCES `PermissionRoles` (`permroleid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `BioEditStatuses`
    (bioeditstatusid, bioeditstatusname, display_order)
    VALUES
    (1, 'Needs editing and translation', 10),
    (2, 'Editing done--needs translation', 20),
    (3, 'Translation done--needs editing', 30),
    (4, 'Editing and translation done', 40);

INSERT INTO `CustomText` (customtextid, page, tag, textcontents, active, html_block_level) VALUES
    (1,'My Profile','biography_note','<p>This is the text that people will read about you in our printed and online guides. Please be sure to review and proof it accordingly, edit out any typographic characters that appear if you paste a bio that displays them in our database system, and make updates as necessary. Your bio will be published as written by you in this section, subject to formatting cleanup.</p>',0,1),
    (2,'My Profile','registration_data','<p>Please confirm your contact information. If it is not correct, please email <a href=\"mailto:$PROGRAM_EMAIL$\" target=\"_blank\" rel=\"noopener\">$PROGRAM_EMAIL$</a> to update your contact info. This data is downloaded periodically from the registration database, and should be correct once they have updated it in the registration system.</p>',0,1),
    (3,'My Profile','policy_block_at_top','',0,1),
    (4,'My Availability','note_before_time_slots','<p>For each day you will be attending $CON_NAME$, please indicate the times when you will be available as a program panelist. Entering a single time for the whole con is fine. Splitting a day into multiple time slots is also fine. Change all items in a row to blank to delete the row. If you schedule yourself as unavailable across part of a panel timeblock we will not schedule you for any panels in that timeblock.  Remember that times are in our local timezone.</p>',0,1),
    (5,'My Availability','note_after_time_slots','<p>Thank you for sharing your availability with us. Please know that we are always looking for program participants who are available during the evening hours.</p>\n<p>You may return to this page to change your availability as needed</p>',0,1),
    (6,'Note to All Panelists 1','all_panelists_1','',0,1),
    (7,'Note to all Panelists 2','all_panelists_2','',0,1),
    (8,'Data Retention Consent','consent','<div><p>We collect personal data to allow us to schedule your programming items, allow other participants to contact you (with your explicit consent), and perform internal administrative tasks. We do not share personal data with other conventions or organizations.</p>\n<p>Public data, such as your biography, photo, and socials, will be published in our printed program guide and online schedule.</p>\n<p>Without your consent we are unable to have you as a program participant.</p></div>',0,1),
    (9,'My Photo','photo_note','<p>Note: Photos should be of type JPEG (.jpg) or PNG (.png), 1 MB or less in size and should be about 800x800. If you need to optimize your photo''s file size we suggest "Save For Web".</p>\n<p>After uploading, you will be able to crop and rotate the image. To upload a photo, either drag the file to the upload photo area or click the upload photo button to use a file picker to select the file to upload.</p>\n<p>All photos uploaded will be reviewed for approval—a default placeholder stock image will appear prior to approval. The approved photo will be available for use in publications, on-line guides, and marketing materials. If you already have an approved photo, any new photo uploaded will be added to the review queue and it will replace the approved photo once reviewed and accepted.</p>',0,1),
    (10,'Participant Survey','survey_displayonly','<p>Note: Some questions may no longer allow you to enter/change their answers. The time has passed for when you can change them and they have been changed from answerable to display only.</p>\n<p>If you need to have a display only answer changed, please reach out to programming at the email address below.</p>',0,1),
    (11,'Participant View','part_overview','',0,1),
    (12,'Staff Overview','staff_overview','<p>Please note the tabs above. One of them will take you to your participant view. Another will allow you to manage Sessions. Note that Sessions is the generic term Zambia uses for anything it can schedule, e.g. Panels, Events, Readings, etc.</p>\n<p>The general flow of sessions over time is:</p>\n<dl>\n<dt>Brainstorm</dt>\n<dd>If $CON_NAME$ is using the brainstorm functionality, these are sessions created by non-staff members which haven''t yet been edited by a staff member.</dd>\n<dt>Edit Me</dt>\n<dd>New session idea that a staff member entered. An idea entered by a brainstorm user that is non-offensive and the least bit feasible should be moved to this status. These are still rough and may well have issues. There still could be duplicates.</dd>\n<dt>Vetted</dt>\n<dd>A real session that we''d like to see happen. At this point the language should be fairly close to final in the description. Proofreading should have happened. More fields are required at this point. This is the minimal status that participants are allowed to sign up for. Avoid duplicates, but many of these still will not happen for various reasons.</dd>\n<dt>Assigned</dt>\n<dd>Session has participants assigned to it.</dd>\n<dt>Scheduled</dt>\n<dd>Session is in the schedule (don''t set this by hand as Zambia actually sets this for you when you schedule it in a room!) The language needs to match what you want to see <strong>published</strong>.</dd>\n</dl>\n<p>There are 3 other statuses that a session can have:</p>\n<dl>\n<dt>Dropped</dt>\n<dd>This item is no longer under consideration and is unlikely even to be mined for future ideas.</dd>\n<dt>Duplicate</dt>\n<dd>Might have been a good session, but was too close or identical to another one.</dd>\n<dt>Cancelled</dt>\n<dd>Over all a good idea, but it isn''t going to happen this year. Generally used later in the programming process. You should probably still say why it was cancelled in the "Notes for Program Committee" field. This is a category we can mine for ideas in future years</dd>\n</dl>\n<p>Some details regarding $CON_NAME$":</p>\n<dl class="ms-4">\n<dd>Convention dates: $CON_START_DATE$ - $CON_END_DATE$</dd>\n<dd>Number of days: $CON_NUM_DAYS$</dd>\n</dl>',0,1),
    (13,'Declined to Invite','declined_particpant','<h3 class="mb-2">Thank you so much for contacting $CON_NAME$.</h3>\n<p>If you are receiving this message, your record in the Zambia system has been closed. A closed record indicates one or more of three things:</p>\n<ol>\n<li>You contacted $CON_NAME$ to let us know that you are unable to participate in the program this year.</li>\n<li>You did not meet a deadline to contact us or provide required information.</li>\n<li>You were not selected to be on the $CON_NAME$ program. We received far more requests to be on program from qualified and amazing people than it is possible to accommodate.</li>\n</ol>\n<p>If you have any questions or if you believe that an error has been made, please contact us at <a href="mailto:$PROGRAM_EMAIL$">$PROGRAM_EMAIL$</a></p>',0,1),
    (14,'General Interests','panel_types_not_int','Panel types I am not interested in participating in:',0,0),
    (15,'General Interests','other_role_desc','Description for "Other" Roles:',0,0),
    (16,'General Interests','roles_checkboxes_label','Roles I\'m willing to take on:',0,0),
    (17,'General Interests','stuff_id_like_to_run','Workshops or presentations I''d like to run:',0,0),
    (18,'General Interests','people_want_on_sess_label','People with whom I\'d like to be on a session: (Leave blank for none)',0,0),
    (19,'General Interests','people_dont_want_label','People with whom I\'d rather not be on a session: (Leave blank for none)',0,0);

INSERT INTO `LanguageStatuses`
    (languagestatusid, languagestatusname, display_order)
    VALUES
    (1,'English',1);

INSERT INTO `PatchLog`
    (patchname)
    VALUES
    ('01_addpermtables.sql'),
    ('02_pubstats.sql'),
    ('03_pubstat_constraints.sql'),
    ('04_divinfo.sql'),
    ('05_PermissionAtoms_update.sql'),
    ('06_some_participant_perms.sql'),
    ('07_participant_write_perms.sql'),
    ('08_add_brainstorm.sql'),
    ('09_session_history.sql'),
    ('10_brainstorm_search_session.sql'),
    ('10_types.sql'),
    ('11_perm_bug.sql'),
    ('12_patchlog.sql'),
    ('13_part_avail_mon.sql'),
    ('14_interested_table.sql'),
    ('15_public_user.sql'),
    ('16_fix_charset.sql'),
    ('17_partavailnull.sql'),
    ('18_editbioperms.sql'),
    ('19_partavail.sql'),
    ('20_bilingual.sql'),
    ('21_more_bios.sql'),
    ('22_validate_session.sql'),
    ('29_email_queue.sql'),
    ('30_congodump_address.sql'),
    ('31_session_edit_history.sql'),
    ('32_import_sessions.sql'),
    ('33_import_participant_bios.sql'),
    ('34_share_email.sql'),
    ('35_staff_notes.sql'),
    ('39_times.sql'),
    ('40_custom_text.sql'),
    ('41_credentials.sql'),
    ('42_report_engine_schema.sql'),
    ('43_configure_rooms.sql'),
    ('44_participant_on_session_history.sql'),
    ('45_drop_report_tables.sql'),
    ('46_new_report_permissions.sql'),
    ('47_tags.sql'),
    ('48_password_reset_optional.sql'),
    ('49_phases.sql'),
    ('50_password_reset_self.sql'),
    ('51_admin_phases.sql'),
    ('52_password_security.sql'),
    ('53_data_retention.sql'),
    ('54_survey.sql'),
    ('55_record_history.sql'),
    ('56_html_bio.sql'),
    ('57_create_user_permission.sql'),
    ('58_roomhasset_key.sql'),
    ('59_config_edit_tables.sql'),
    ('60_photos.sql'),
    ('61_staff_overview_ct.sql'),
    ('62_import_session_html.sql'),
    ('66_permission_cleanup.sql'),
    ('67_session_links_cleanup.sql'),
    ('68_custom_text_new_columns.sql'),
    ('69_my_interests_new_custom_text.sql'),
    ('72_configure_permissions.sql');

INSERT INTO `PermissionAtoms`
    (permatomid, permatomtag, elementid, page, notes, display_order)
    VALUES
    (1, 'Staff', NULL, 'renderWelcome', 'Enables staff menu link', 10),
    (2, 'Administrator', NULL, 'many', 'Reconfigure reports + ???', 20),
    (3, 'Participant', NULL, 'many', 'Login as Participant', 30),
    (4, 'EditBio', NULL, 'renderMyContact', 'Allow write to biography on my contact page', 40),
    (5, 'my_availability', NULL, 'Participant Menu', 'Enables menu option throughout participant section and enables page.', 50),
    (6, 'search_panels', NULL, 'Participant Menu', 'Enables menu option throughout participant section and enables page.', 60),
    (7, 'my_panel_interests', NULL, 'Participant Menu', 'Enables menu option throughout participant section and enables page.', 70),
    (8, 'my_schedule', NULL, 'Participant Menu', 'Enables menu option throughout participant section and enables page.', 80),
    (9, 'my_suggestions_write', NULL, 'MySuggestions', 'Enables write access to the form elements on the page MySuggestions.', 90),
    (10, 'my_gen_int_write', NULL, 'MyGeneralInterests', 'Enables write access to the form elements on the page My General Interests', 100),
    (11, 'BrainstormSubmit', NULL, 'EditCreateSession', 'Brainstorm user can create session', 110),
    (12, 'BS_sear_sess', NULL, 'SearchSessions', 'Brainstorm user can view sessions', 120),
    (13, 'public_login', NULL, 'Login', 'Brainstorm user can log in', 130),
    (14, 'SendEmail', NULL, 'StaffManageParticipants', 'Access to Send email set of pages', 140),
    (15, 'postcon', NULL, 'renderWelcome', 'Forces participant welcome page to display only post con message.', 150),
    (16, 'EditSesNtsAsgnPartPg', NULL, 'StaffAssignParticipants', 'Edit ', 160),
    (17, 'ConfigureReports', NULL, 'ConfigureReports', 'Enable menu option', 170),
    (18, 'ResetUserPassword', NULL, 'AdminParticipants', 'No one needs this permission once password overhaul is implemented and system has email integration.', 180),
    (19, 'AdminPhases', NULL, 'AdminPhases', 'Change phase of Zambia use, allowing which sections are current.', 190),
    (20, 'CreateUser', NULL, 'CreateUser', 'Manually create user. Must have edit for one or more roles.', 200),
    (21, 'EditUserPermRoles', NULL, 'Admin Participants', 'Control all user permission roles. Also used on create user page.', 210),
    (22, 'EditUserPermRoles', 4, 'Admin Participants', 'Control participant user permission role only. Also used on create user page.', 220),
    (23, 'EditUserPermRoles', 3, 'Admin Participants', 'Control staff user permission role only. Also used on create user page.', 230),
    (24, 'edit_my_contact', NULL, 'profile/my_contact', 'Enables submit; All login roles should have it.', 240),
    (25, 'general_interests', NULL, 'Participant Menu', 'Enables menu option throughout participant section and enables page.', 250),
    (26, 'EditAnyConfigurationTable', NULL, 'Edit Configuration Tables', 'Global permission to edit configuration tables', 260),
    (27, 'photos', NULL, 'Participant Menu', 'Enable photo submission tab', 270),
    (28, 'survey', NULL, 'Participant Menu', 'Enable survey tab', 280),
    (29, 'AdminPhotos', NULL, 'Administer Photos', 'approve/deny photos', 290),
    (30, 'edit_participant_tags', NULL, 'Admin Participants', 'Change a participant\'s tags', 300),
    (31, 'edit_participant_responses', NULL, 'many', 'edit others\' survey responses, etc.', 310),
    (32, 'declined_participant', NULL, 'many', 'Upon login show only declined participant page', 320),
    (33, 'ConfigurePermissions', NULL, 'ConfigurePermissions', 'Allows editing the Permissions, Phases, and PermissionRoles tables', 330),
    (2000, 'ce_BioEditStatuses', NULL, 'Edit Configuration Tables', 'enables edit', 340),
    (2001, 'ce_Credentials', NULL, 'Edit Configuration Tables', 'enables edit', 350),
    (2002, 'ce_Roles', NULL, 'Edit Configuration Tables', 'enables edit', 360),
    (2003, 'ce_KidsCategories', NULL, 'Edit Configuration Tables', 'enables edit', 370),
    (2004, 'ce_LanguageStatuses', NULL, 'Edit Configuration Tables', 'enables edit', 380),
    (2005, 'ce_PubStatuses', NULL, 'Edit Configuration Tables', 'enables edit', 390),
    (2006, 'ce_SessionStatuses', NULL, 'Edit Configuration Tables', 'enables edit', 400),
    (2007, 'ce_Divisions', NULL, 'Edit Configuration Tables', 'enables edit', 410),
    (2008, 'ce_RegTypes', NULL, 'Edit Configuration Tables', 'enables edit', 420),
    (2009, 'ce_Tags', NULL, 'Edit Configuration Tables', 'enables edit', 430),
    (2010, 'ce_Times', NULL, 'Edit Configuration Tables', 'enables edit', 440),
    (2011, 'ce_Tracks', NULL, 'Edit Configuration Tables', 'enables edit', 450),
    (2012, 'ce_Types', NULL, 'Edit Configuration Tables', 'enables edit', 460),
    (2013, 'ce_Rooms', NULL, 'Edit Configuration Tables', 'enables edit', 470),
    (2014, 'ce_RoomSets', NULL, 'Edit Configuration Tables', 'enables edit', 480),
    (2015, 'ce_RoomHasSet', NULL, 'Edit Configuration Tables', 'enables edit', 490),
    (2016, 'ce_Features', NULL, 'Edit Configuration Tables', 'enables edit', 500),
    (2017, 'ce_Services', NULL, 'Edit Configuration Tables', 'enables edit', 510),
    (2018, 'ce_EmailFrom', NULL, 'Edit Configuration Tables', 'enables edit', 520),
    (2019, 'ce_EmailTo', NULL, 'Edit Configuration Tables', 'enables edit', 530),
    (2020, 'ce_EmailCC', NULL, 'Edit Configuration Tables', 'enables edit', 540),
    (2021, 'ce_PhotoDenialReasons', NULL, 'Edit Configuration Tables', 'enables edit', 550),
    (2022, 'ce_ParticipantTags', NULL, 'Edit Configuration Tables', 'enables edit', 560),
    (10001,'ConTrollImportUsers',NULL,'Import Reg User','enables importing users from ConTroll', 570);

INSERT INTO `PermissionRoles`
    (permroleid, permrolename, notes, display_order)
    VALUES
    (1,'Administrator','Damn well everything',10),
    (2,'Senior Staff','Send email & grant staff',20),
    (3,'Staff','Much stuff',30),
    (4,'Participant','Login as participant',40),
    (5,'Declined Participant','See only declined participant page',50),
    (6,'Brainstorm','Use for Brainstorm pages',60);

INSERT INTO `Phases`
    (phaseid, phasename, current, notes, implemented, display_order)
    VALUES
    (1, 'Initial invitation', 1, 'Login, welcome, and profile', 1, 10),
    (2, 'General information', 1, 'Availability, general interests, and suggestions', 1, 20),
    (3, 'Edit stuff for publications', 1, 'Even after print deadline might not deactivate if you want to allow updating of online publications', 1, 30),
    (4, 'Survey', 1, 'Edit survey responses', 1, 40),
    (5, 'Panel sign up', 0, 'Search and sign up for panels',1, 50),
    (6, 'Show schedule', 0, 'View schedule page',1, 60),
    (7, 'Post con', 0, 'Only post con page is visible to particpants',1, 70),
    (8, 'Brainstorm', 0, 'Users may suggest schedule items on brainstorm pages',1, 80);

INSERT INTO `Permissions`
    (permissionid, permatomid, phaseid, permroleid, badgeid)
    VALUES
    #######################
    ## Staff permissions ##
    #######################
    ## "Staff" Staff Menu and login -- permatomid: 1
    (1, 1, NULL, 1, NULL),
    (2, 1, NULL, 2, NULL),
    (3, 1, NULL, 3, NULL),
    ## "Administrator" Misc Administrator -- permatomid: 2
    ## 1) Config table editor render or post, 2) Edit custom text render or post,
    ## 3) Edit survey render, 4) Preview survey render, 5) Edit survey post,
    ## 6) 1-5 above in menus
    (4, 2, NULL, 1, NULL),
    ## "SendEmail" Send email -- permatomid: 14
    (5, 14, NULL, 1, NULL),
    (6, 14, NULL, 2, NULL),
    ## "EditSesNtsAsgnPartPg" Edit notes for program staff on assign participants page -- permatomid: 16
    (7, 16, NULL, 1, NULL),
    (8, 16, NULL, 2, NULL),
    (9, 16, NULL, 3, NULL),
    ## "ConfigureReports" Build Report Menus -- permatomid: 17
    ## Complete: Menu and page
    (10, 17, NULL, 1, NULL),
    ## "ResetUserPassword" Edit User Password -- permatomid: 18
    ## If self service email reset is working, don't grant to anyone
    ## Incomplete: Controls rendering on Admin Participants page, but doesn't gate back end
    ## "AdminPhases" Administer Phases -- permatomid: 19
    ## Complete: Staff menu, page rendering, back end action
    (11, 19, NULL, 1, NULL),
    ## "CreateUser" Create User -- permatomid: 20
    ## Must also have edit for one or more roles
    ## Complete: Staff menu, page rendering, back end action
    ## If integrated with ConTroll, don't grant to anyone or maybe only administrator to create a few initial users
    ## "EditUserPermRoles" Edit user permission roles -- permatomid: 21, 22, 23
    ## This is a non-standard permission atom with 3 different rows in PermissionAtoms table
    ## having the same permatomtag.  There is special code in EditPermRoles_FNC.php for wrangling
    ## this atom.
    ## Complete: Render and back end on Admin Participants, Create User, and Import User from ConTroll
    (12, 21, NULL, 1, NULL),
    (13, 22, NULL, 2, NULL),
    (14, 22, NULL, 3, NULL),
    ## "edit_my_contact" writability of my profile/my contact page -- permatomid: 24
    ## Also writability of participant photo
    ## Back end only
    ## Staff (and participants, NULL) need this to change their passwords
    (15, 24, NULL, 1, NULL),
    ## "EditAnyConfigurationTable" Edit Any Configuration Table -- permatomid: 26
    ## Complete: Menu, Render and back end
    (16, 26, NULL, 1, NULL),
    ## "AdminPhotos" Approve or deny photos -- permatomid: 29
    ## Incomplete: Page render and back end, but not menus
    (17, 29, NULL, 1, NULL),
    (18, 29, NULL, 2, NULL),
    ## "edit_participant_tags" Edit a user's tags -- permatomid: 30
    ## Complete: render and back end
    (19, 30, NULL, 1, NULL),
    (20, 30, NULL, 2, NULL),
    (21, 30, NULL, 3, NULL),
    ## "edit_participant_responses" Edit a participant's responses -- permatomid: 31
    ## Currently implemented for survey and general interests
    ## Complete: render and back end
    (22, 31, NULL, 1, NULL),
    (23, 31, NULL, 2, NULL),
    ## "ConfigurePermissions" Configure Permissions -- permatomid: 33
    (43, 33, NULL, 1, NULL),
    ## "ce_BioEditStatuses" -- permatomid: 2000 through
    ## "ce_ParticipantTags" -- permatomid: 2022
    ## These work very similarly to "EditAnyConfigurationTable" except that they control
    ## a single table each.
    ## Complete: Menu, Render and back end
    ## Not granted in standard configuration
    ## "ConTrollImportUsers" Inport user from Control -- permatomid: 10000
    ## User also needs permission to edit a role in order to grant the imported
    ## user a role.
    ## Complete: Menu, Render and back end
    (24, 10001, NULL, 1, NULL),
    (25, 10001, NULL, 2, NULL),
    (26, 10001, NULL, 3, NULL),
    #############################
    ## Participant permissions ##
    #############################
    ## "Participant" several particpant functions -- permatomid: 3
    ## Login as participant, get participant menu initially, flag self as interested
    (27, 3, 1, 4, NULL),
    ## "EditBio" several particpant functions -- permatomid: 4
    ## On "My Profile" page, edit bio, pubsname, and credentials
    ## Incomplete: Page render and back end except no back end protection for credentials
    ## No protection on admin participants
    (28, 4, 3, 4, NULL),
    ## "my_availability" availability page -- permatomid: 5
    ## Menu and rendering of availability page.  No back end protection.
    (29, 5, 2, 4, NULL),
    ## "search_panels" search panels page -- permatomid: 6
    ## Menu and rendering of availability page.  No back end protection.
    (30, 6, 5, 4, NULL),
    ## "my_panel_interests" my panel interests page -- permatomid: 7
    ## Menu and rendering of availability page.  No back end protection.
    (31, 7, 5, 4, NULL),
    ## "my_schedule" my schedule page -- permatomid: 8
    ## Menu and rendering of availability page.  There is no back end to protect because page is read only.
    (32, 8, 6, 4, NULL),
    ## "my_suggestions_write" suggestion page -- permatomid: 9
    ## Use for both enabling menu and enabling writeability
    ## Render and back end
    (33, 9, 2, 4, NULL),
    ## "my_gen_int_write" writability of my general interests page -- permatomid: 10
    ## Render and back end
    (34, 10, 2, 4, NULL),
    ## "postcon" force participant welcome page to be post con page -- permatomid: 15
    (35, 15, 7, 4, NULL),
    ## "edit_my_contact" writability of my profile/my contact page -- permatomid: 24
    ## Also writability of participant photo
    ## Back end only
    ## Also granted to staff so they can edit their passwords, etc.
    ## Why does this exist?
    (36, 24, 1, 4, NULL),
    ## "general_interests" my general interests page -- permatomid: 25
    ## Menu and render of my general interests page -- no backend
    (37, 25, 2, 4, NULL),
    ## "photos" photos page -- permatomid: 27
    ## Menu only -- no render or backend
    ## Don't need to use this to disable photos entirely -- use db_name
    (38, 27, 2, 4, NULL),
    ## "survey" photos page -- permatomid: 28
    ## Menu only -- no render or backend
    ## Don't need to use this to disable survey entirely -- use db_name
    (39, 28, 4, 4, NULL),
    ######################################
    ## Declined participant permissions ##
    ######################################
    ## "declined_participant" replace welcome page with declined participant page -- permatomid: 28
    ##
    (40, 32, NULL, 5, NULL),
    ############################
    ## Brainstorm permissions ##
    ############################
    ## Actually assigned to participants
    ## Brainstorm user, i.e. anonymous login is no longer supported and needs to be re-implemented
    ##
    ## "BrainstormSubmit" user can create session -- permatomid: 11
    ## Menu and render -- no backend
    ## "Brainstorm" menu item can appear on staff menu, but isn't configured here
    (41, 11, 8, 4, NULL),
    ## "BS_sear_sess" search session from brainstorm pages -- permatomid: 12
    ## backend only
    ## Some other paths for viewing brainstorm items are still supported without any permission protection
    ## other than login
    (42, 12, 8, 4, NULL);
    ## "public_login" special anonymous login for brainstorm pages -- permatomid: 13
    ## No longer supported

INSERT INTO `PubStatuses`
    (pubstatusid, pubstatusname, display_order)
    VALUES
    (2,'Public',2),(3,'Do not print',3);

INSERT INTO `SessionEditCodes`
    (sessioneditcode, description, display_order)
    VALUES
    (1, 'Created in brainstorm', 10),
    (2, 'Created in staff create session', 20),
    (3, 'Edit session contents', 40),
    (4, 'Add to schedule', 50),
    (5, 'Remove from schedule', 60),
    (6, 'Created by import', 30),
    (7, 'Rescheduled', 70);

INSERT INTO `SessionStatuses`
    (statusid, statusname, validate, may_be_scheduled, display_order)
    VALUES
    (1,'Brainstorm',0,0,10),
    (2,'Vetted',1,1,30),
    (3,'Scheduled',1,1,50),
    (4,'Dropped',0,0,60),
    (5,'Cancelled',0,0,80),
    (6,'Edit Me',0,0,20),
    (7,'Assigned',1,1,40),
    (10,'Duplicate',0,0,70);

INSERT INTO `SurveyQuestionTypes`
    (typeid, shortname, description, current, display_order)
    VALUES
    (5, 'heading', 'HTML heading/prompt without input field', 1, 5),
    (10, 'hor-radio', 'Radio button list, select one of many-horizontal output', 1, 10),
    (15, 'vert-radio', 'Radio button list, select one of many-vertical output', 1, 15),
    (20, 'single-pulldown', 'Select list, select one of many', 1, 20),
    (30, 'multi-select list', 'Select list, select multiple of many with Ctrl/click', 1, 30),
    (40, 'multi-checkbox list', 'Checkbox list, check multiple of many', 1, 31),
    (50, 'multi-display', 'left<-->right select boxes, move selected options to right box', 1, 32),
    (60, 'openend', 'Single line of text', 1, 40),
    (70, 'text', 'Multi-line text box', 1, 50),
    (71, 'html-text', 'HTML editor based multi-line text box', 1, 55),
    (80, 'numberselect', 'Number from min to max, ascending or descending', 1, 60),
    (90, 'number', 'Integer fill in the blank', 1, 61),
    (100, 'monthnum', 'Months by number (1-12)', 1, 140),
    (110, 'monthabv', 'Months by abbreviation (Jan-Dec)', 1, 150),
    (120, 'monthyear', 'Months by abbreviation (Jan-Dec, current year-1900)', 1, 160),
    (130, 'country', 'Country List', 1, 170),
    (140, 'states', 'State/Province List', 1, 180);

INSERT INTO `SurveyQuestionTypeDefaults`
    (typeid, ordinal, value, display_order, optionshort, optionhover, allowothertext)
    VALUES
    (100, 1, '1', 1, 'Jan', 'January', 0),
    (100, 2, '2', 2, 'Feb', 'February', 0),
    (100, 3, '3', 3, 'Mar', 'March', 0),
    (100, 4, '4', 4, 'Apr', 'April', 0),
    (100, 5, '5', 5, 'May', 'May', 0),
    (100, 6, '6', 6, 'Jun', 'June', 0),
    (100, 7, '7', 7, 'Jul', 'July', 0),
    (100, 8, '8', 8, 'Aug', 'August', 0),
    (100, 9, '9', 9, 'Sep', 'September', 0),
    (100, 10, '10', 10, 'Oct', 'October', 0),
    (100, 11, '11', 11, 'Nov', 'November', 0),
    (100, 12, '12', 12, 'Dec', 'December', 0),
    (110, 1, 'Jan', 1, 'Jan', 'January', 0),
    (110, 2, 'Feb', 2, 'Feb', 'February', 0),
    (110, 3, 'Mar', 3, 'Mar', 'March', 0),
    (110, 4, 'Apr', 4, 'Apr', 'April', 0),
    (110, 5, 'May', 5, 'May', 'May', 0),
    (110, 6, 'Jun', 6, 'Jun', 'June', 0),
    (110, 7, 'Jul', 7, 'Jul', 'July', 0),
    (110, 8, 'Aug', 8, 'Aug', 'August', 0),
    (110, 9, 'Sep', 9, 'Sep', 'September', 0),
    (110, 10, 'Oct', 10, 'Oct', 'October', 0),
    (110, 11, 'Nov', 11, 'Nov', 'November', 0),
    (110, 12, 'Dec', 12, 'Dec', 'December', 0),
    (120, 1, 'Jan', 1, 'Jan', 'January', 0),
    (120, 2, 'Feb', 2, 'Feb', 'February', 0),
    (120, 3, 'Mar', 3, 'Mar', 'March', 0),
    (120, 4, 'Apr', 4, 'Apr', 'April', 0),
    (120, 5, 'May', 5, 'May', 'May', 0),
    (120, 6, 'Jun', 6, 'Jun', 'June', 0),
    (120, 7, 'Jul', 7, 'Jul', 'July', 0),
    (120, 8, 'Aug', 8, 'Aug', 'August', 0),
    (120, 9, 'Sep', 9, 'Sep', 'September', 0),
    (120, 10, 'Oct', 10, 'Oct', 'October', 0),
    (120, 11, 'Nov', 11, 'Nov', 'November', 0),
    (120, 12, 'Dec', 12, 'Dec', 'December', 0),
    (130, 1, 'US', 1, 'United States', NULL, 0),
    (130, 2, 'CA', 2, 'Canada', NULL, 0),
    (130, 3, 'AF', 3, 'Afghanistan', NULL, 0),
    (130, 4, 'AX', 4, 'Aland Islands', NULL, 0),
    (130, 5, 'AL', 5, 'Albania', NULL, 0),
    (130, 6, 'DZ', 6, 'Algeria', NULL, 0),
    (130, 7, 'AS', 7, 'American Samoa', NULL, 0),
    (130, 8, 'AD', 8, 'Andorra', NULL, 0),
    (130, 9, 'AO', 9, 'Angola', NULL, 0),
    (130, 10, 'AI', 10, 'Anguilla', NULL, 0),
    (130, 11, 'AQ', 11, 'Antarctica', NULL, 0),
    (130, 12, 'AG', 12, 'Antigua and Barbuda', NULL, 0),
    (130, 13, 'AR', 13, 'Argentina', NULL, 0),
    (130, 14, 'AM', 14, 'Armenia', NULL, 0),
    (130, 15, 'AW', 15, 'Aruba', NULL, 0),
    (130, 16, 'AU', 16, 'Australia', NULL, 0),
    (130, 17, 'AT', 17, 'Austria', NULL, 0),
    (130, 18, 'AZ', 18, 'Azerbaijan', NULL, 0),
    (130, 19, 'BS', 19, 'Bahamas', NULL, 0),
    (130, 20, 'BH', 20, 'Bahrain', NULL, 0),
    (130, 21, 'BD', 21, 'Bangladesh', NULL, 0),
    (130, 22, 'BB', 22, 'Barbados', NULL, 0),
    (130, 23, 'BY', 23, 'Belarus', NULL, 0),
    (130, 24, 'BE', 24, 'Belgium', NULL, 0),
    (130, 25, 'BZ', 25, 'Belize', NULL, 0),
    (130, 26, 'BJ', 26, 'Benin', NULL, 0),
    (130, 27, 'BM', 27, 'Bermuda', NULL, 0),
    (130, 28, 'BT', 28, 'Bhutan', NULL, 0),
    (130, 29, 'BO', 29, 'Bolivia', NULL, 0),
    (130, 30, 'BA', 30, 'Bosnia and Herzegovina', NULL, 0),
    (130, 31, 'BW', 31, 'Botswana', NULL, 0),
    (130, 32, 'BV', 32, 'Bouvet Island', NULL, 0),
    (130, 33, 'BR', 33, 'Brazil', NULL, 0),
    (130, 34, 'VG', 34, 'British Virgin Islands', NULL, 0),
    (130, 35, 'IO', 35, 'British Indian Ocean Territory', NULL, 0),
    (130, 36, 'BN', 36, 'Brunei Darussalam', NULL, 0),
    (130, 37, 'BG', 37, 'Bulgaria', NULL, 0),
    (130, 38, 'BF', 38, 'Burkina Faso', NULL, 0),
    (130, 39, 'BI', 39, 'Burundi', NULL, 0),
    (130, 40, 'KH', 40, 'Cambodia', NULL, 0),
    (130, 41, 'CM', 41, 'Cameroon', NULL, 0),
    (130, 42, 'CV', 42, 'Cape Verde', NULL, 0),
    (130, 43, 'KY', 43, 'Cayman Islands', NULL, 0),
    (130, 44, 'CF', 44, 'Central African Republic', NULL, 0),
    (130, 45, 'TD', 45, 'Chad', NULL, 0),
    (130, 46, 'CL', 46, 'Chile', NULL, 0),
    (130, 47, 'CN', 47, 'China', NULL, 0),
    (130, 48, 'HK', 48, 'Hong Kong, SAR China', NULL, 0),
    (130, 49, 'MO', 49, 'Macao, SAR China', NULL, 0),
    (130, 50, 'CX', 50, 'Christmas Island', NULL, 0),
    (130, 51, 'CC', 51, 'Cocos (Keeling Islands)', NULL, 0),
    (130, 52, 'CO', 52, 'Colombia', NULL, 0),
    (130, 53, 'KM', 53, 'Comoros', NULL, 0),
    (130, 54, 'CG', 54, 'Congo (Brazzaville)', NULL, 0),
    (130, 55, 'CD', 55, 'Congo, (Kinshasa)', NULL, 0),
    (130, 56, 'CK', 56, 'Cook Islands', NULL, 0),
    (130, 57, 'CR', 57, 'Costa Rica', NULL, 0),
    (130, 58, 'CI', 58, 'Côte dNULLIvoire', '', 0),
    (130, 59, 'HR', 59, 'Croatia', NULL, 0),
    (130, 60, 'CU', 60, 'Cuba', NULL, 0),
    (130, 61, 'CY', 61, 'Cyprus', NULL, 0),
    (130, 62, 'CZ', 62, 'Czech Republic', NULL, 0),
    (130, 63, 'DK', 63, 'Denmark', NULL, 0),
    (130, 64, 'DJ', 64, 'Djibouti', NULL, 0),
    (130, 65, 'DM', 65, 'Dominica', NULL, 0),
    (130, 66, 'DO', 66, 'Dominican Republic', NULL, 0),
    (130, 67, 'EC', 67, 'Ecuador', NULL, 0),
    (130, 68, 'EG', 68, 'Egypt', NULL, 0),
    (130, 69, 'SV', 69, 'El Salvador', NULL, 0),
    (130, 70, 'GQ', 70, 'Equatorial Guinea', NULL, 0),
    (130, 71, 'ER', 71, 'Eritrea', NULL, 0),
    (130, 72, 'EE', 72, 'Estonia', NULL, 0),
    (130, 73, 'ET', 73, 'Ethiopia', NULL, 0),
    (130, 74, 'FK', 74, 'Falkland Islands (Malvinas)', NULL, 0),
    (130, 75, 'FO', 75, 'Faroe Islands', NULL, 0),
    (130, 76, 'FJ', 76, 'Fiji', NULL, 0),
    (130, 77, 'FI', 77, 'Finland', NULL, 0),
    (130, 78, 'FR', 78, 'France', NULL, 0),
    (130, 79, 'GF', 79, 'French Guiana', NULL, 0),
    (130, 80, 'PF', 80, 'French Polynesia', NULL, 0),
    (130, 81, 'TF', 81, 'French Southern Territories', NULL, 0),
    (130, 82, 'GA', 82, 'Gabon', NULL, 0),
    (130, 83, 'GM', 83, 'Gambia', NULL, 0),
    (130, 84, 'GE', 84, 'Georgia', NULL, 0),
    (130, 85, 'DE', 85, 'Germany', NULL, 0),
    (130, 86, 'GH', 86, 'Ghana', NULL, 0),
    (130, 87, 'GI', 87, 'Gibraltar', NULL, 0),
    (130, 88, 'GR', 88, 'Greece', NULL, 0),
    (130, 89, 'GL', 89, 'Greenland', NULL, 0),
    (130, 90, 'GD', 90, 'Grenada', NULL, 0),
    (130, 91, 'GP', 91, 'Guadeloupe', NULL, 0),
    (130, 92, 'GU', 92, 'Guam', NULL, 0),
    (130, 93, 'GT', 93, 'Guatemala', NULL, 0),
    (130, 94, 'GG', 94, 'Guernsey', NULL, 0),
    (130, 95, 'GN', 95, 'Guinea', NULL, 0),
    (130, 96, 'GW', 96, 'Guinea-Bissau', NULL, 0),
    (130, 97, 'GY', 97, 'Guyana', NULL, 0),
    (130, 98, 'HT', 98, 'Haiti', NULL, 0),
    (130, 99, 'HM', 99, 'Heard and Mcdonald Islands', NULL, 0),
    (130, 100, 'VA', 100, 'Holy See (Vatican City State)', NULL, 0),
    (130, 101, 'HN', 101, 'Honduras', NULL, 0),
    (130, 102, 'HU', 102, 'Hungary', NULL, 0),
    (130, 103, 'IS', 103, 'Iceland', NULL, 0),
    (130, 104, 'IN', 104, 'India', NULL, 0),
    (130, 105, 'ID', 105, 'Indonesia', NULL, 0),
    (130, 106, 'IR', 106, 'Iran, Islamic Republic of', NULL, 0),
    (130, 107, 'IQ', 107, 'Iraq', NULL, 0),
    (130, 108, 'IE', 108, 'Ireland', NULL, 0),
    (130, 109, 'IM', 109, 'Isle of Man', NULL, 0),
    (130, 110, 'IL', 110, 'Israel', NULL, 0),
    (130, 111, 'IT', 111, 'Italy', NULL, 0),
    (130, 112, 'JM', 112, 'Jamaica', NULL, 0),
    (130, 113, 'JP', 113, 'Japan', NULL, 0),
    (130, 114, 'JE', 114, 'Jersey', NULL, 0),
    (130, 115, 'JO', 115, 'Jordan', NULL, 0),
    (130, 116, 'KZ', 116, 'Kazakhstan', NULL, 0),
    (130, 117, 'KE', 117, 'Kenya', NULL, 0),
    (130, 118, 'KI', 118, 'Kiribati', NULL, 0),
    (130, 119, 'KP', 119, 'Korea (North)', NULL, 0),
    (130, 120, 'KR', 120, 'Korea (South)', NULL, 0),
    (130, 121, 'KW', 121, 'Kuwait', NULL, 0),
    (130, 122, 'KG', 122, 'Kyrgyzstan', NULL, 0),
    (130, 123, 'LA', 123, 'Lao PDR', NULL, 0),
    (130, 124, 'LV', 124, 'Latvia', NULL, 0),
    (130, 125, 'LB', 125, 'Lebanon', NULL, 0),
    (130, 126, 'LS', 126, 'Lesotho', NULL, 0),
    (130, 127, 'LR', 127, 'Liberia', NULL, 0),
    (130, 128, 'LY', 128, 'Libya', NULL, 0),
    (130, 129, 'LI', 129, 'Liechtenstein', NULL, 0),
    (130, 130, 'LT', 130, 'Lithuania', NULL, 0),
    (130, 131, 'LU', 131, 'Luxembourg', NULL, 0),
    (130, 132, 'MK', 132, 'Macedonia, Republic of', NULL, 0),
    (130, 133, 'MG', 133, 'Madagascar', NULL, 0),
    (130, 134, 'MW', 134, 'Malawi', NULL, 0),
    (130, 135, 'MY', 135, 'Malaysia', NULL, 0),
    (130, 136, 'MV', 136, 'Maldives', NULL, 0),
    (130, 137, 'ML', 137, 'Mali', NULL, 0),
    (130, 138, 'MT', 138, 'Malta', NULL, 0),
    (130, 139, 'MH', 139, 'Marshall Islands', NULL, 0),
    (130, 140, 'MQ', 140, 'Martinique', NULL, 0),
    (130, 141, 'MR', 141, 'Mauritania', NULL, 0),
    (130, 142, 'MU', 142, 'Mauritius', NULL, 0),
    (130, 143, 'YT', 143, 'Mayotte', NULL, 0),
    (130, 144, 'MX', 144, 'Mexico', NULL, 0),
    (130, 145, 'FM', 145, 'Micronesia, Federated States of', NULL, 0),
    (130, 146, 'MD', 146, 'Moldova, Republic of', NULL, 0),
    (130, 147, 'MC', 147, 'Monaco', NULL, 0),
    (130, 148, 'MN', 148, 'Mongolia', NULL, 0),
    (130, 149, 'ME', 149, 'Montenegro', NULL, 0),
    (130, 150, 'MS', 150, 'Montserrat', NULL, 0),
    (130, 151, 'MA', 151, 'Morocco', NULL, 0),
    (130, 152, 'MZ', 152, 'Mozambique', NULL, 0),
    (130, 153, 'MM', 153, 'Myanmar', NULL, 0),
    (130, 154, 'NA', 154, 'Namibia', NULL, 0),
    (130, 155, 'NR', 155, 'Nauru', NULL, 0),
    (130, 156, 'NP', 156, 'Nepal', NULL, 0),
    (130, 157, 'NL', 157, 'Netherlands', NULL, 0),
    (130, 158, 'AN', 158, 'Netherlands Antilles', NULL, 0),
    (130, 159, 'NC', 159, 'New Caledonia', NULL, 0),
    (130, 160, 'NZ', 160, 'New Zealand', NULL, 0),
    (130, 161, 'NI', 161, 'Nicaragua', NULL, 0),
    (130, 162, 'NE', 162, 'Niger', NULL, 0),
    (130, 163, 'NG', 163, 'Nigeria', NULL, 0),
    (130, 164, 'NU', 164, 'Niue', NULL, 0),
    (130, 165, 'NF', 165, 'Norfolk Island', NULL, 0),
    (130, 166, 'MP', 166, 'Northern Mariana Islands', NULL, 0),
    (130, 167, 'NO', 167, 'Norway', NULL, 0),
    (130, 168, 'OM', 168, 'Oman', NULL, 0),
    (130, 169, 'PK', 169, 'Pakistan', NULL, 0),
    (130, 170, 'PW', 170, 'Palau', NULL, 0),
    (130, 171, 'PS', 171, 'Palestinian Territory', NULL, 0),
    (130, 172, 'PA', 172, 'Panama', NULL, 0),
    (130, 173, 'PG', 173, 'Papua New Guinea', NULL, 0),
    (130, 174, 'PY', 174, 'Paraguay', NULL, 0),
    (130, 175, 'PE', 175, 'Peru', NULL, 0),
    (130, 176, 'PH', 176, 'Philippines', NULL, 0),
    (130, 177, 'PN', 177, 'Pitcairn', NULL, 0),
    (130, 178, 'PL', 178, 'Poland', NULL, 0),
    (130, 179, 'PT', 179, 'Portugal', NULL, 0),
    (130, 180, 'PR', 180, 'Puerto Rico', NULL, 0),
    (130, 181, 'QA', 181, 'Qatar', NULL, 0),
    (130, 182, 'RE', 182, 'Réunion', NULL, 0),
    (130, 183, 'RO', 183, 'Romania', NULL, 0),
    (130, 184, 'RU', 184, 'Russian Federation', NULL, 0),
    (130, 185, 'RW', 185, 'Rwanda', NULL, 0),
    (130, 186, 'BL', 186, 'Saint-Barthélemy', NULL, 0),
    (130, 187, 'SH', 187, 'Saint Helena', NULL, 0),
    (130, 188, 'KN', 188, 'Saint Kitts and Nevis', NULL, 0),
    (130, 189, 'LC', 189, 'Saint Lucia', NULL, 0),
    (130, 190, 'MF', 190, 'Saint-Martin (French)', NULL, 0),
    (130, 191, 'PM', 191, 'Saint Pierre and Miquelon', NULL, 0),
    (130, 192, 'VC', 192, 'Saint Vincent and Grenadines', NULL, 0),
    (130, 193, 'WS', 193, 'Samoa', NULL, 0),
    (130, 194, 'SM', 194, 'San Marino', NULL, 0),
    (130, 195, 'ST', 195, 'Sao Tome and Principe', NULL, 0),
    (130, 196, 'SA', 196, 'Saudi Arabia', NULL, 0),
    (130, 197, 'SN', 197, 'Senegal', NULL, 0),
    (130, 198, 'RS', 198, 'Serbia', NULL, 0),
    (130, 199, 'SC', 199, 'Seychelles', NULL, 0),
    (130, 200, 'SL', 200, 'Sierra Leone', NULL, 0),
    (130, 201, 'SG', 201, 'Singapore', NULL, 0),
    (130, 202, 'SK', 202, 'Slovakia', NULL, 0),
    (130, 203, 'SI', 203, 'Slovenia', NULL, 0),
    (130, 204, 'SB', 204, 'Solomon Islands', NULL, 0),
    (130, 205, 'SO', 205, 'Somalia', NULL, 0),
    (130, 206, 'ZA', 206, 'South Africa', NULL, 0),
    (130, 207, 'GS', 207, 'South Georgia and the South Sandwich Islands', NULL, 0),
    (130, 209, 'SS', 209, 'South Sudan', NULL, 0),
    (130, 210, 'ES', 210, 'Spain', NULL, 0),
    (130, 211, 'LK', 211, 'Sri Lanka', NULL, 0),
    (130, 212, 'SD', 212, 'Sudan', NULL, 0),
    (130, 213, 'SR', 213, 'Suriname', NULL, 0),
    (130, 214, 'SJ', 214, 'Svalbard and Jan Mayen Islands', NULL, 0),
    (130, 215, 'SZ', 215, 'Swaziland', NULL, 0),
    (130, 216, 'SE', 216, 'Sweden', NULL, 0),
    (130, 217, 'CH', 217, 'Switzerland', NULL, 0),
    (130, 218, 'SY', 218, 'Syrian Arab Republic (Syria)', NULL, 0),
    (130, 219, 'TW', 219, 'Taiwan, Republic of China', NULL, 0),
    (130, 220, 'TJ', 220, 'Tajikistan', NULL, 0),
    (130, 221, 'TZ', 221, 'Tanzania, United Republic of', NULL, 0),
    (130, 222, 'TH', 222, 'Thailand', NULL, 0),
    (130, 223, 'TL', 223, 'Timor-Leste', NULL, 0),
    (130, 224, 'TG', 224, 'Togo', NULL, 0),
    (130, 225, 'TK', 225, 'Tokelau', NULL, 0),
    (130, 226, 'TO', 226, 'Tonga', NULL, 0),
    (130, 227, 'TT', 227, 'Trinidad and Tobago', NULL, 0),
    (130, 228, 'TN', 228, 'Tunisia', NULL, 0),
    (130, 229, 'TR', 229, 'Turkey', NULL, 0),
    (130, 230, 'TM', 230, 'Turkmenistan', NULL, 0),
    (130, 231, 'TC', 231, 'Turks and Caicos Islands', NULL, 0),
    (130, 232, 'TV', 232, 'Tuvalu', NULL, 0),
    (130, 233, 'UG', 233, 'Uganda', NULL, 0),
    (130, 234, 'UA', 234, 'Ukraine', NULL, 0),
    (130, 235, 'AE', 235, 'United Arab Emirates', NULL, 0),
    (130, 236, 'GB', 236, 'United Kingdom', NULL, 0),
    (130, 237, 'UM', 237, 'US Minor Outlying Islands', NULL, 0),
    (130, 238, 'UY', 238, 'Uruguay', NULL, 0),
    (130, 239, 'UZ', 239, 'Uzbekistan', NULL, 0),
    (130, 240, 'VU', 240, 'Vanuatu', NULL, 0),
    (130, 241, 'VE', 241, 'Venezuela (Bolivarian Republic)', NULL, 0),
    (130, 242, 'VN', 242, 'Viet Nam', NULL, 0),
    (130, 243, 'VI', 243, 'Virgin Islands, US', NULL, 0),
    (130, 244, 'WF', 244, 'Wallis and Futuna Islands', NULL, 0),
    (130, 245, 'EH', 245, 'Western Sahara', NULL, 0),
    (130, 246, 'YE', 246, 'Yemen', NULL, 0),
    (130, 247, 'ZM', 247, 'Zambia', NULL, 0),
    (130, 248, 'ZW', 248, 'Zimbabwe', NULL, 0),
    (140, 1, 'AL', 1, 'AL', 'Alabama', 0),
    (140, 2, 'AK', 2, 'AK', 'Alaska', 0),
    (140, 3, 'AB', 3, 'AB', 'Alberta', 0),
    (140, 4, 'AS', 4, 'AS', 'American Samoa', 0),
    (140, 5, 'AZ', 5, 'AZ', 'Arizona', 0),
    (140, 6, 'AR', 6, 'AR', 'Arkansas', 0),
    (140, 7, 'BC', 7, 'BC', 'British Columbia', 0),
    (140, 8, 'CA', 8, 'CA', 'California', 0),
    (140, 9, 'CO', 9, 'CO', 'Colorado', 0),
    (140, 10, 'CT', 10, 'CT', 'Connecticut', 0),
    (140, 11, 'DE', 11, 'DE', 'Delaware', 0),
    (140, 12, 'DC', 12, 'DC', 'District of Columbia', 0),
    (140, 13, 'FL', 13, 'FL', 'Florida', 0),
    (140, 14, 'GA', 14, 'GA', 'Georgia', 0),
    (140, 15, 'GU', 15, 'GU', 'Guam', 0),
    (140, 16, 'HI', 16, 'HI', 'Hawaii', 0),
    (140, 17, 'ID', 17, 'ID', 'Idaho', 0),
    (140, 18, 'IL', 18, 'IL', 'Illinois', 0),
    (140, 19, 'IN', 19, 'IN', 'Indiana', 0),
    (140, 20, 'IA', 20, 'IA', 'Iowa', 0),
    (140, 21, 'KS', 21, 'KS', 'Kansas', 0),
    (140, 22, 'KY', 22, 'KY', 'Kentucky', 0),
    (140, 23, 'LA', 23, 'LA', 'Louisiana', 0),
    (140, 24, 'ME', 24, 'ME', 'Maine', 0),
    (140, 25, 'MB', 25, 'MB', 'Manitoba', 0),
    (140, 26, 'MH', 26, 'MH', 'Marshall Islands', 0),
    (140, 27, 'MD', 27, 'MD', 'Maryland', 0),
    (140, 28, 'MA', 28, 'MA', 'Massachusetts', 0),
    (140, 29, 'MI', 29, 'MI', 'Michigan', 0),
    (140, 30, 'FM', 30, 'FM', 'Micronesia', 0),
    (140, 31, 'MN', 31, 'MN', 'Minnesota', 0),
    (140, 32, 'MS', 32, 'MS', 'Mississippi', 0),
    (140, 33, 'MO', 33, 'MO', 'Missouri', 0),
    (140, 34, 'MT', 34, 'MT', 'Montana', 0),
    (140, 35, 'NE', 35, 'NE', 'Nebraska', 0),
    (140, 36, 'NV', 36, 'NV', 'Nevada', 0),
    (140, 37, 'NB', 37, 'NB', 'New Brunswick', 0),
    (140, 38, 'NH', 38, 'NH', 'New Hampshire', 0),
    (140, 39, 'NJ', 39, 'NJ', 'New Jersey', 0),
    (140, 40, 'NM', 40, 'NM', 'New Mexico', 0),
    (140, 41, 'NY', 41, 'NY', 'New York', 0),
    (140, 42, 'NL', 42, 'NL', 'Newfoundland and Labrador', 0),
    (140, 43, 'NC', 43, 'NC', 'North Carolina', 0),
    (140, 44, 'ND', 44, 'ND', 'North Dakota', 0),
    (140, 45, 'MP', 45, 'MP', 'Northern Marianas', 0),
    (140, 46, 'NT', 46, 'NT', 'Northwest Territories', 0),
    (140, 47, 'NS', 47, 'NS', 'Nova Scotia', 0),
    (140, 48, 'NU', 48, 'NU', 'Nunavut', 0),
    (140, 49, 'OH', 49, 'OH', 'Ohio', 0),
    (140, 50, 'OK', 50, 'OK', 'Oklahoma', 0),
    (140, 51, 'ON', 51, 'ON', 'Ontario', 0),
    (140, 52, 'OR', 52, 'OR', 'Oregon', 0),
    (140, 53, 'PW', 53, 'PW', 'Palau', 0),
    (140, 54, 'PA', 54, 'PA', 'Pennsylvania', 0),
    (140, 55, 'PE', 55, 'PE', 'Prince Edward Island', 0),
    (140, 56, 'PR', 56, 'PR', 'Puerto Rico', 0),
    (140, 57, 'QC', 57, 'QC', 'Quebec', 0),
    (140, 58, 'RI', 58, 'RI', 'Rhode Island', 0),
    (140, 59, 'SK', 59, 'SK', 'Saskatchewan', 0),
    (140, 60, 'SC', 60, 'SC', 'South Carolina', 0),
    (140, 61, 'SD', 61, 'SD', 'South Dakota', 0),
    (140, 62, 'TN', 62, 'TN', 'Tennessee', 0),
    (140, 63, 'TX', 63, 'TX', 'Texas', 0),
    (140, 64, 'UT', 64, 'UT', 'Utah', 0),
    (140, 65, 'VT', 65, 'VT', 'Vermont', 0),
    (140, 66, 'VI', 66, 'VI', 'Virgin Islands', 0),
    (140, 67, 'VA', 67, 'VA', 'Virginia', 0),
    (140, 68, 'WA', 68, 'WA', 'Washington', 0),
    (140, 69, 'WV', 69, 'WV', 'West Virginia', 0),
    (140, 70, 'WI', 70, 'WI', 'Wisconsin', 0),
    (140, 71, 'WY', 71, 'WY', 'Wyoming', 0),
    (140, 72, 'YT', 72, 'YT', 'Yukon', 0);

INSERT INTO `Times`
    (timeid, timedisplay, timevalue, next_day, display_order, avail_start, avail_end)
    VALUES
    (1,'8:30a','08:30:00',0,1,1,0),
    (2,'10:00a','10:00:00',0,2,1,1),
    (3,'11:30a','11:30:00',0,3,1,1),
    (4,'1:00p','13:00:00',0,4,1,1),
    (5,'2:30p','14:30:00',0,5,1,1),
    (6,'4:00p','16:00:00',0,6,1,1),
    (7,'5:30p','17:30:00',0,7,1,1),
    (8,'7:00p','19:00:00',0,8,1,1),
    (9,'8:30p','20:30:00',0,9,1,1),
    (10,'10:00p','22:00:00',0,10,1,1),
    (11,'11:30p','23:30:00',0,11,1,1),
    (12,'1:00a (+1d)','01:00:00',1,12,0,1);
