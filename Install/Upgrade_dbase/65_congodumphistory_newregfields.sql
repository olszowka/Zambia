## This script adds new fields and changes some old ones in CongoDumpHistory for better reg system integration.
##
##	Created by Syd Weinstein on September 10, 2022
## 	Copyright (c) 2022 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

ALTER TABLE CongoDumpHistory
CHANGE loginid createdbybadgeid varchar(15) NOT NULL DEFAULT '';

ALTER TABLE CongoDumpHistory
CHANGE timeoverwritten createdts timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP;


ALTER TABLE CongoDumpHistory
ADD inactivatedts timestamp NULL DEFAULT NULL,
ADD inactivatedbybadgeid varchar(15) DEFAULT NULL;

ALTER TABLE CongoDumpHistory
DROP PRIMARY KEY,
ADD PRIMARY KEY(badgeid,createdts),
ADD KEY badgeid (badgeid),
ADD KEY createdbybadgeid (createdbybadgeid);

ALTER TABLE CongoDumpHistory
ADD CONSTRAINT CongoDumpHistory_ibfk_1 FOREIGN KEY (badgeid) REFERENCES Participants (badgeid);

ALTER TABLE CongoDumpHistory
ADD CONSTRAINT CongoDumpHistory_ibfk_2 FOREIGN KEY (createdbybadgeid) REFERENCES Participants (badgeid);

ALTER TABLE CongoDumpHistory
ADD CONSTRAINT CongoDumpHistory_ibfk_3 FOREIGN KEY (inactivatedbybadgeid) REFERENCES Participants (badgeid);

INSERT INTO PatchLog (patchname) VALUES ('65_congodumphistory_newregfields.sql');
