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

CREATE TABLE PhotoUploadStatus (
  photouploadstatus int NOT NULL,
  statustext varchar(64) NOT NULL,
  PRIMARY KEY (photouploadstatus)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
## bit 0 = uploaded photo available
## bit 1 = uploaded photo denied
## bit 2 = approved photo available
INSERT INTO PhotoUploadStatus (photouploadstatus, statustext)
VALUES (0, "No photo uploaded"),
(1, "Photo waiting for approval"),
(2, "Denied - see denial reason"),
(3, "Denied photo replaced, waiting for approval"),
(4, "Photo approved"),
(5, "Updated photo waiting for approval"),
(6, "Updated photo denied and deleted, existing photo still available"),
(7, "Updated photo denied, existing photo still available");

## Table name is too long for the atom id, lengthen it
ALTER TABLE PermissionAtoms MODIFY COLUMN permatomtag varchar(32) NOT NULL;

## NOW Add in the permissionatom and permission for both edit config table and the page
INSERT INTO PermissionAtoms(permatomid, permatomtag, page, notes)
VALUES (2021, 'ce_PhotoDenialReasons', 'Edit Configuration Tables', 'enables edit'),
(2022, 'AdminPhotos', 'Administer Photos', 'approve/deny photos');

INSERT INTO Permissions(permatomid, phaseid, permroleid)
SELECT a.permatomid, null, r.permroleid 
FROM PermissionAtoms a
JOIN PermissionRoles r ON (r.permrolename = 'Administrator')
WHERE permatomtag IN  'ce_PhotoDenialReasons', 'AdminPhotos');

INSERT INTO Permissions(permatomid, phaseid, permroleid)
SELECT a.permatomid, null, r.permroleid 
FROM PermissionAtoms a
JOIN PermissionRoles r ON (r.permrolename = 'Senior Staff')
WHERE permatomtag = 'AdminPhotos';

ALTER TABLE Participants ADD CONSTRAINT Participants_photodeny FOREIGN KEY (photodenialreasonid) REFERENCES PhotoDenialReasons (photodenialreasonid);

ALTER TABLE Participants ADD CONSTRAINT participantphotostatus_fk
	FOREIGN KEY (photouploadstatus) REFERENCES PhotoUploadStatus (photouploadstatus);

INSERT INTO CustomText(page, tag, textcontents)
VALUES ('My Profile', 'photo_note', 'Note: Photos should be of type JPEG (.jpg) or PNG (.png) and should be about 800x800. You will be able to crop and rotate the image. To upload a photo, either drag the file to be uploaded to the upload photo area or click the upload photo button to use a file picker to select the file to upload. All photos uploaded will be reviewed for approval. The approved photo will be available to publications and on-line guides. If you have an approved photo, the new photo uploaded will be added to the review queue and it will replace the approved photo once reviewed and accepted.');

INSERT INTO PatchLog (patchname) VALUES ('60_photos.sql');
