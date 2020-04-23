## This script Adds fields to phases in prep for new phases screen and to allow for display order sort
##
##	Created by Syd Weinstein on April 19, 2020
## 	Copyright (c) 2020 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

## Add columns for display order and implemented phase to the phases table
ALTER TABLE phases ADD COLUMN display_order int AFTER notes;
ALTER TABLE phases ADD COLUMN implemented tinyint(1) DEFAULT '0' AFTER notes;
## Phaseid 9 (Events Show Schedule) is reserved, but currently not in the implemented code base
UPDATE phases SET implemented = 1 WHERE phaseid <> 9;
## Display order is a sort by field for getting the rows of phases into a display order, initial by 100
## to allow for inserting of ones in the middle later
UPDATE phases SET display_order = phaseid * 100;
UPDATE phases SET display_order = 450 WHERE phaseid = 9;

## Add missing permission atom my_suggestions_write(9) for Phase 6(Suggestions) to the
## permissions table
INSERT INTO permissions (permatomid, phaseid, permroleid, badgeid)
VALUES (9, 6, 2, NULL), (9, 6, 3, NULL);

INSERT INTO PatchLog (patchname) VALUES ('49_phases.sql');
