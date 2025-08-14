## This script adds the history tables for congo and emails
##
##	Created by Syd Weinstein on September 3, 2020
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Participants
ADD COLUMN htmlbio TEXT AFTER bio;

ALTER TABLE Sessions
ADD meetinglink VARCHAR(512) AFTER persppartinfo;

ALTER TABLE Sessions
ADD progguidhtml TEXT AFTER progguiddesc;

INSERT INTO PatchLog (patchname) VALUES ('56_html_bio.sql');
