##This script implements some more of the phase model including the "Suggestions" phase
## and 2 permission atoms.  After running it, all optional functionality will be
## available.  Modify the Phases table to remove what you don't want.
INSERT INTO `PermissionAtoms` VALUES (16,'my_suggestions_write',NULL,'MySuggestions','Enables write access to the form elements on the page MySuggestions.'),(17,'my_gen_int_write',NULL,'MyGeneralInterests','Enables write access to the form elements on the page My General Interests');
INSERT INTO `Phases` VALUES (5, 'Suggestions', 1, 'Allow write access to My Suggestions and My General Interests.');
INSERT INTO `Permissions` VALUES (null, 16,5,1,null),(null,16,5,3,null);
INSERT INTO `Permissions` VALUES (null, 17,5,1,null),(null,17,5,3,null);
