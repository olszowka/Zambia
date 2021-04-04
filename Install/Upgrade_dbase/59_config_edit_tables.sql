## This script adds permission atoms for table editing and does default assignment to admin of those atoms
##
##	Created by Syd Weinstein on January 9, 2021
## 	Copyright (c) 2021 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

INSERT INTO PermissionAtoms(permatomid, permatomtag, page, notes)
VALUES
    (2000, 'ce_All', 'Edit Configuration Tables', 'enables edit'),
    (2001, 'ce_BioEditStatuses', 'Edit Configuration Tables', 'enables edit'),
    (2002, 'ce_Credentials', 'Edit Configuration Tables', 'enables edit'),
    (2003, 'ce_Divisions', 'Edit Configuration Tables', 'enables edit'),
    (2004, 'ce_EmailCC', 'Edit Configuration Tables', 'enables edit'),
    (2005, 'ce_EmailFrom', 'Edit Configuration Tables', 'enables edit'),
    (2006, 'ce_EmailTo', 'Edit Configuration Tables', 'enables edit'),
    (2007, 'ce_Features', 'Edit Configuration Tables', 'enables edit'),
    (2008, 'ce_KidsCategories', 'Edit Configuration Tables', 'enables edit'),
    (2009, 'ce_LanguageStatuses', 'Edit Configuration Tables', 'enables edit'),
    (2010, 'ce_PubStatuses', 'Edit Configuration Tables', 'enables edit'),
    (2011, 'ce_RegTypes', 'Edit Configuration Tables', 'enables edit'),
    (2012, 'ce_Roles', 'Edit Configuration Tables', 'enables edit'),
    (2013, 'ce_Rooms', 'Edit Configuration Tables', 'enables edit'),
    (2014, 'ce_RoomSets', 'Edit Configuration Tables', 'enables edit'),
    (2015, 'ce_RoomHasSet', 'Edit Configuration Tables', 'enables edit'),
    (2016, 'ce_Services', 'Edit Configuration Tables', 'enables edit'),
    (2017, 'ce_SessionStatuses', 'Edit Configuration Tables', 'enables edit'),
    (2018, 'ce_Tags', 'Edit Configuration Tables', 'enables edit'),
    (2019, 'ce_Times', 'Edit Configuration Tables', 'enables edit'),
    (2020, 'ce_Tracks', 'Edit Configuration Tables', 'enables edit'),
    (2021, 'ce_Types', 'Edit Configuration Tables', 'enables edit');

INSERT INTO Permissions(permatomid, phaseid, permroleid, badgeid)
    SELECT 2000, null, permroleid, null
        FROM
            PermissionRoles
        WHERE
            permrolename = 'Administrator';

ALTER TABLE RegTypes RENAME RegTypes_obsolete;

CREATE TABLE RegTypes (
    regtypeid int NOT NULL AUTO_INCREMENT,
    regtype varchar(40) NOT NULL DEFAULT '',
    message varchar(100) DEFAULT NULL,
    display_order int NULL DEFAULT 0,
    PRIMARY KEY (regtypeid),
    UNIQUE KEY `RegTypes_Regtype` (`regtype`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO RegTypes(regtype, message)
    SELECT regtype, message 
        FROM RegTypes_obsolete;

INSERT INTO PatchLog (patchname) VALUES ('59_config_edit_tables.sql');
