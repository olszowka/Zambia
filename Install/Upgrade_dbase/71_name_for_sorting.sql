## This script adds a name_for_sorting column to the Participants table, used to sort
## a list of published names (pubsname) of participants in publications.
##
##	Created by Peter Olszowka on 2026-07-09;
## 	Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Participants
    ADD COLUMN name_for_sorting VARCHAR(50) DEFAULT NULL AFTER pubsname;

INSERT INTO PatchLog (patchname) VALUES ('71_name_for_sorting.sql');
