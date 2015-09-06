## This script creates the 'Edit Bio' phase; removes the 'EditBio' atom from all other phases; and
## adds it to the new phase.
INSERT INTO Phases (phaseid, phasename, current, notes) values
  (8,'Edit Bio',1,'Participants may edit their bios.  Otherwide field is readonly.');
DELETE FROM Permissions where permatomid=4;
INSERT INTO Permissions (permissionid, permatomid, phaseid, permroleid, badgeid) values
  (null, 4, 8, 3, null);
Insert into PatchLog (patchname) values ('18_editbioperms.sql');
