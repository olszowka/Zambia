## This script adds the history tables for congo and emails
##
##	Created by Syd Weinstein on September 3, 2020
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
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
  `loginid` varchar(15) DEFAULT NULL,
  `timeoverwritten` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`badgeid`,`timeoverwritten`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contains changes to user information from registration system.';

CREATE TABLE `EmailHistory` (
  `emailqueueid` int NOT NULL AUTO_INCREMENT,
  `emailto` varchar(255) DEFAULT NULL,
  `emailfrom` varchar(255) DEFAULT NULL,
  `emailcc` varchar(255) DEFAULT NULL,
  `emailsubject` varchar(255) DEFAULT NULL,
  `status` int NOT NULL,
  `emailtimestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`emailqueueid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO PatchLog (patchname) VALUES ('55_record_history.sql');
