## This script adds db table rows to allow control over the new BrainstormSearchSession page. 
INSERT INTO `PermissionAtoms` VALUES (19,'BS_sear_sess',NULL,'BrainstormSearchSession','Control whether brainstorm users have access to search sessions tab and following pages');
INSERT INTO `Permissions` VALUES (24, 19, null, 1, null),(25, 19,null,3,null),(26, 19,null,5,null);
