## This script adds a missing field to the PreviousConTracks table.
##
##  Created by Leane Verhulst on August 27, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

## Add missing field
ALTER TABLE `PreviousConTracks` ADD COLUMN `display_order` int(11) NOT NULL AFTER `trackname`;
ALTER TABLE `PreviousConTracks` ADD COLUMN `selfselect` tinyint(1) NOT NULL AFTER `display_order`;



INSERT INTO PatchLog (patchname) VALUES ('74CAP_update_previouscontracks.sql');
