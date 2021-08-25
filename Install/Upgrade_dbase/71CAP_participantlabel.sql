## This script adds a new field to the sessions table.
##
##  Created by Leane Verhulst on August 24, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

## Add new field
ALTER TABLE `Sessions` ADD COLUMN `participantlabel` varchar(50) NOT NULL DEFAULT 'Panelists' AFTER `notesforprog`;
ALTER TABLE `PreviousSessions` ADD COLUMN `participantlabel` varchar(50) DEFAULT NULL AFTER `notesforprog`;



INSERT INTO PatchLog (patchname) VALUES ('71CAP_participantlabel.sql');
