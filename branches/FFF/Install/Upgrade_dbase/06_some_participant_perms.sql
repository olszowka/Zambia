##This script implements some of the phase model and 4 permission atoms. After running it, all optional
##functionality will be available.  Modify the Phases table to remove what you don't want.
INSERT INTO `PermissionAtoms` VALUES (12,'my_availability',NULL,'ParticipantHeader','Enables menu option throughout participant section and enables page.'),(13,'search_panels',NULL,'ParticipantHeader','Enables menu option throughout participant section and enables page.'),(14,'my_panel_interests',NULL,'ParticipantHeader','Enables menu option throughout participant section and enables page.'),(15,'my_schedule',NULL,'ParticipantHeader','Enables menu option throughout participant section and enables page.');
INSERT INTO `Permissions` VALUES (null, 12,2,1,null),(null,12,2,3,null);
INSERT INTO `Permissions` VALUES (null, 13,3,1,null),(null,13,3,3,null);
INSERT INTO `Permissions` VALUES (null, 14,3,1,null),(null,14,3,3,null);
INSERT INTO `Permissions` VALUES (null, 15,4,1,null),(null,15,4,3,null);
UPDATE Phases set phasename="Invitation", notes="Welcome, My Profile, My Suggestions and My General Interests",current=1 where phaseid=1;
UPDATE Phases set phasename="Availability", notes="Add on My Availability",
current=1 where phaseid=2;
UPDATE Phases set phasename="Panel Sign Up", notes="Add on Search Panels and My Panel Interests",
current=1 where phaseid=3;
UPDATE Phases set phasename="Show Schedule", notes="Add on My Schedule",
current=1 where phaseid=4;
