## This script adds permission atoms for table editing and does default assignment to admin of those atoms
##
##	Created by Syd Weinstein on January 9,2021
## 	Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

INSERT INTO PermissionAtoms(permatomtag, page, notes)
VALUES
	('ce_BioEditStatuses', 'Edit Configuration Tables', 'enables edit'),
	('ce_Credentials', 'Edit Configuration Tables', 'enables edit'),
	('ce_Roles', 'Edit Configuration Tables', 'enables edit'),
	('ce_KidsCategories', 'Edit Configuration Tables', 'enables edit'),
	('ce_LanguageStatuses', 'Edit Configuration Tables', 'enables edit'),
	('ce_PubStatuses', 'Edit Configuration Tables', 'enables edit'),
	('ce_SessionStatuses', 'Edit Configuration Tables', 'enables edit'),
	('ce_Divisions', 'Edit Configuration Tables', 'enables edit'),
	('ce_RegTypes', 'Edit Configuration Tables', 'enables edit'),
	('ce_Tags', 'Edit Configuration Tables', 'enables edit'),
	('ce_Times', 'Edit Configuration Tables', 'enables edit'),
	('ce_Tracks', 'Edit Configuration Tables', 'enables edit'),
	('ce_Types', 'Edit Configuration Tables', 'enables edit'),
	('ce_Rooms', 'Edit Configuration Tables', 'enables edit'),
	('ce_RoomSets', 'Edit Configuration Tables', 'enables edit'),
	('ce_RoomHasSet', 'Edit Configuration Tables', 'enables edit'),
	('ce_Features', 'Edit Configuration Tables', 'enables edit'),
	('ce_Services', 'Edit Configuration Tables', 'enables edit'),
	('ce_EmailFrom', 'Edit Configuration Tables', 'enables edit'),
	('ce_EmailTo', 'Edit Configuration Tables', 'enables edit'),
	('ce_EmailCC', 'Edit Configuration Tables', 'enables edit');

INSERT INTO Permissions(permatomid, phaseid, permroleid)
SELECT a.permatomid, null, r.permroleid 
FROM PermissionAtoms a
JOIN PermissionRoles r ON (r.permrolename = 'Administrator')
WHERE permatomtag IN ('ce_Credentials', 'ce_Roles', 'ce_KidsCategories', 
	'ce_LanguageStatuses', 'ce_PubStatuses', 'ce_SessionStatuses',
	'ce_Divisions', 'ce_RegTypes', 'ce_Tags', 'ce_Times', 'ce_Tracks',
	'ce_Types', 'ce_Rooms', 'ce_RoomSets', 'ce_RoomHasSet',
	'ce_Features', 'ce_Services', 'ce_EmailFrom', 'ce_EmailTo',
	'ce_EmailCC');

INSERT INTO PatchLog (patchname) VALUES ('59_config_edit_tables.sql');
