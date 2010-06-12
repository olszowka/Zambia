## This script does all the schema changes to support the updated coloration in the grid tables.
ALTER TABLE Types
    ADD COLUMN htmlcellcolor VARCHAR(8) NOT NULL DEFAULT "#FFFFFF" AFTER selfselect;
INSERT INTO PatchLog (patchname) VALUES ('23_room_colours.sql');
