## This script adds permission atoms for table editing and does default assignment to admin of those atoms
##
##	Created by Syd Weinstein on January 9,2021
## 	Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

INSERT INTO PermissionAtoms(permatomid, permatomtag, page, notes)
VALUES
	(2000, 'ce_BioEditStatuses', 'Edit Configuration Tables', 'enables edit'),
	(2001, 'ce_Credentials', 'Edit Configuration Tables', 'enables edit'),
	(2002, 'ce_Roles', 'Edit Configuration Tables', 'enables edit'),
	(2003, 'ce_KidsCategories', 'Edit Configuration Tables', 'enables edit'),
	(2004, 'ce_LanguageStatuses', 'Edit Configuration Tables', 'enables edit'),
	(2005, 'ce_PubStatuses', 'Edit Configuration Tables', 'enables edit'),
	(2006, 'ce_SessionStatuses', 'Edit Configuration Tables', 'enables edit'),
	(2007, 'ce_Divisions', 'Edit Configuration Tables', 'enables edit'),
	(2008, 'ce_RegTypes', 'Edit Configuration Tables', 'enables edit'),
	(2009, 'ce_Tags', 'Edit Configuration Tables', 'enables edit'),
	(2010, 'ce_Times', 'Edit Configuration Tables', 'enables edit'),
	(2011, 'ce_Tracks', 'Edit Configuration Tables', 'enables edit'),
	(2012, 'ce_Types', 'Edit Configuration Tables', 'enables edit'),
	(2013, 'ce_Rooms', 'Edit Configuration Tables', 'enables edit'),
	(2014, 'ce_RoomSets', 'Edit Configuration Tables', 'enables edit'),
	(2015, 'ce_RoomHasSet', 'Edit Configuration Tables', 'enables edit'),
	(2016, 'ce_Features', 'Edit Configuration Tables', 'enables edit'),
	(2017, 'ce_Services', 'Edit Configuration Tables', 'enables edit'),
	(2018, 'ce_EmailFrom', 'Edit Configuration Tables', 'enables edit'),
	(2019, 'ce_EmailTo', 'Edit Configuration Tables', 'enables edit'),
	(2020, 'ce_EmailCC', 'Edit Configuration Tables', 'enables edit');

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
