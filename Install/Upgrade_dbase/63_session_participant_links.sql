## This script Adds fields to Sessions table for program participants
##
##	Created by Syd Weinstein on November 30, 2021
## 	Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

ALTER TABLE Sessions
ADD COLUMN panelistlink varchar(512) NULL AFTER meetinglink;
ALTER TABLE Sessions
ADD COLUMN captionlink varchar(512) NULL AFTER panelistlink;

INSERT INTO PatchLog (patchname) VALUES ('63_session_participant_links.sql');
