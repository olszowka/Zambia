## This script adds a missing field to the PreviousSessions table.
##
##  Created by Leane Verhulst on August 24, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

## Add missing field
ALTER TABLE `PreviousSessions` ADD COLUMN `servicenotes` text DEFAULT NULL AFTER `notesforpart`;



INSERT INTO PatchLog (patchname) VALUES ('70CAP_missing_servicenotes.sql');
