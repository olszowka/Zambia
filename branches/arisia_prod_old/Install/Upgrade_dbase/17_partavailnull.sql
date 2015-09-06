## This script changes the default on ParticipantAvailability.mondaymaxprog to null
## to match the other similar columns.
ALTER TABLE ParticipantAvailability ALTER COLUMN mondaymaxprog SET DEFAULT NULL;
UPDATE ParticipantAvailability SET mondaymaxprog = NULL WHERE
   fridaymaxprog is null and saturdaymaxprog is null;
Insert into PatchLog (patchname) values ('17_partavailnull.sql');
