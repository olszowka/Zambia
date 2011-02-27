## This script renames some tables and columns to make room for new flexible particpant roles functionality
ALTER TABLE ParticipantRoles RENAME TO ParticipantActivities;
ALTER TABLE ParticipantHasRole RENAME TO ParticipantHasActivity;
ALTER TABLE ParticipantHasRole DROP FOREIGN KEY ParticipantHasRole_ibfk_2;
ALTER TABLE ParticipantActivities CHANGE COLUMN roleid activityid INT NOT NULL AUTO_INCREMENT PRIMARY KEY;
ALTER TABLE ParticipantActivities CHANGE COLUMN rolename activityname VARCHAR(50);
ALTER TABLE ParticipantHasActivity CHANGE COLUMN roleid activityid INT NOT NULL;
ALTER TABLE ParticipantHasActivity DROP INDEX roleid;
ALTER TABLE ParticipantHasActivity ADD FOREIGN KEY ParticipantHasActivity_ibfk_activityid (activityid) REFERENCES ParticipantActivities(activityid);
INSERT INTO PatchLog (patchname) VALUES ('37_participant_roles_to_activities.sql');

