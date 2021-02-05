## This script Adds fields to Participants in prep for photo addition
## It also creates the PhotoDenialReasons table
##
##	Created by Syd Weinstein on February 5, 2021
## 	Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

## Add new fields for photos for Participants
ALTER TABLE Participants ADD COLUMN uploadedphotofilename varchar(64) NULL AFTER pubsname;
ALTER TABLE Participants ADD COLUMN approvedphotofilename varchar(64) NULL AFTER uploadedphotofilename;
ALTER TABLE Participants ADD COLUMN photodenialreasonothertext varchar(512) NULL AFTER approvedphotofilename;
ALTER TABLE Participants ADD COLUMN photodenialreasonid int NULL AFTER photodenialreasonothertext;
ALTER TABLE Participants ADD COLUMN photouploadstatus INT NULL AFTER photodenialreasonid;

CREATE TABLE PhotoDenialReasons (
  photodenialreasonid int NOT NULL AUTO_INCREMENT,
  reasontext varchar(512) DEFAULT NULL,
  display_order int DEFAULT NULL,
  PRIMARY KEY (photodenialreasonid)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

ALTER TABLE Participants ADD CONSTRAINT Participants_photodeny FOREIGN KEY (photodenialreasonid) REFERENCES PhotoDenialReasons (photodenialreasonid);

INSERT INTO PatchLog (patchname) VALUES ('60_photos.sql');
