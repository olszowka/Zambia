## This script adds the history tables for emails
##
##	Created by Syd Weinstein on September 3, 2020
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

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
