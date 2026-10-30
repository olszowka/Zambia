## This script adds the allow_grid_simplification flag to Rooms and Types. The Grid Scheduler's "Grid Simplification"
## option draws a default-duration session as if it ran to the next grid line, but only in rooms and for session
## types where this flag is set. It defaults to 0 (prevented), so nothing changes until an admin turns it on.
##
##  Created by Peter Olszowka on 2026-10-15;
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE `Rooms`
    ADD COLUMN `allow_grid_simplification` TINYINT(1) NOT NULL DEFAULT '0' AFTER `is_scheduled`;

ALTER TABLE `Types`
    ADD COLUMN `allow_grid_simplification` TINYINT(1) NOT NULL DEFAULT '0' AFTER `selfselect`;

INSERT INTO `PatchLog` (patchname) VALUES ('77_grid_simplification.sql');
