## This script adds a field to Participants for better sorting.
##
##  Created by Leane Verhulst on August 20, 2021
##  Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

## Add new field for sorting
ALTER TABLE `Participants` ADD COLUMN `sortedpubsname` varchar(50) DEFAULT NULL AFTER `pubsname`;


INSERT INTO PatchLog (patchname) VALUES ('65CAP_add_sortedpubsname.sql');
