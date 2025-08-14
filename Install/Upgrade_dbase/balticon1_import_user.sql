## This script adds permission atoms for importing users from the Balitcon Reg system
##
##	Created by Syd Weinstein on March 8,2021
## 	Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

INSERT INTO PermissionAtoms(permatomid, permatomtag, page, notes)
VALUES
	(10001, 'balt_ImportUsers', 'Import Reg User', 'enables importing users form Balticon Reg System');

INSERT INTO Permissions(permatomid, phaseid, permroleid)
SELECT a.permatomid, null, r.permroleid 
FROM PermissionAtoms a
JOIN PermissionRoles r ON (r.permrolename = 'Administrator')
WHERE permatomtag = 'balt_ImportUsers';

INSERT INTO Permissions(permatomid, phaseid, permroleid)
SELECT a.permatomid, null, r.permroleid 
FROM PermissionAtoms a
JOIN PermissionRoles r ON (r.permrolename = 'Senior Staff')
WHERE permatomtag = 'balt_ImportUsers';

INSERT INTO PatchLog (patchname) VALUES ('balticon1_import_user.sql');
