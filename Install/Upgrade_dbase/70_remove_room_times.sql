## This script removes the unused open/close time slot fields from the Rooms table.
##
##	Created by Peter Olszowka on 2026-07-07;
## 	Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Rooms
    DROP COLUMN opentime1,
    DROP COLUMN closetime1,
    DROP COLUMN opentime2,
    DROP COLUMN closetime2,
    DROP COLUMN opentime3,
    DROP COLUMN closetime3;

INSERT INTO PatchLog (patchname) VALUES ('70_remove_room_times.sql');
