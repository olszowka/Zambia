## This script updates PermissionAtoms, PermissionRoles, and Permissions tables to latest
## It does not convert an existing Permissions table.
##
## !!!!!! This script assumes ConTroll Integration !!!!!!
##
## This script is intended that a user will have one and only one staff/administrator type permission role.
## However, staff who are also participants should have participant role as well. Violating that won't
## break anything, but will complicate analysis and configuration of permissions
##
##  Created by Peter Olszowka on August 19, 2025
##  Copyright (c) 2025-2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

DROP TABLE IF EXISTS `UHPR_SAVE`;

CREATE TABLE `UHPR_SAVE` (
    `badgeid` varchar(15) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
    `permroleid` int NOT NULL DEFAULT '0',
    PRIMARY KEY (`badgeid`,`permroleid`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `UHPR_SAVE`
        (badgeid, permroleid)
    SELECT
            UHPR.badgeid, 1 as permroleid
        FROM
                 PermissionRoles PR
            JOIN UserHasPermissionRole UHPR USING (permroleid)
        WHERE
            LOWER(PR.permrolename) LIKE '%administrator%';

INSERT INTO `UHPR_SAVE`
    (badgeid, permroleid)
    WITH UHPR2 AS (
        SELECT UHPR.badgeid, UHPR.permroleid
            FROM
                UserHasPermissionRole UHPR
            WHERE NOT EXISTS (SELECT * FROM
                    UHPR_SAVE
                WHERE
                    UHPR_SAVE.badgeid = UHPR.badgeid
            )
    )
    SELECT
            UHPR2.badgeid, 2 as permroleid
        FROM
                 PermissionRoles PR
            JOIN UHPR2 USING (permroleid)
        WHERE
            LOWER(PR.permrolename) LIKE '%senior%';

INSERT INTO `UHPR_SAVE`
    (badgeid, permroleid)
    WITH UHPR2 AS (
        SELECT UHPR.badgeid, UHPR.permroleid
            FROM
                UserHasPermissionRole UHPR
            WHERE NOT EXISTS (SELECT * FROM
                    UHPR_SAVE
                WHERE
                    UHPR_SAVE.badgeid = UHPR.badgeid
        )
    )
    SELECT
            UHPR2.badgeid, 3 as permroleid
        FROM
                 PermissionRoles PR
            JOIN UHPR2 USING (permroleid)
        WHERE
            LOWER(PR.permrolename) LIKE '%staff%';

INSERT INTO `UHPR_SAVE`
    (badgeid, permroleid)
    SELECT DISTINCT
            UHPR.badgeid, 4 as permroleid
        FROM
                 PermissionRoles PR
            JOIN UserHasPermissionRole UHPR USING (permroleid)
        WHERE
            LOWER(PR.permrolename) LIKE '%part%';

DELETE FROM Permissions;
DELETE FROM UserHasPermissionRole;
DELETE FROM PermissionRoles;
DELETE FROM PermissionAtoms;
DELETE FROM Phases;

INSERT INTO `PermissionAtoms`
        (permatomid, permatomtag, elementid, page, notes)
    VALUES
        (1,'Staff',NULL,'renderWelcome','Enables staff menu link'),
        (2,'Administrator',NULL,'many','Reconfigure reports + ???'),
        (3,'Participant',NULL,'many','Login as Participant'),
        (4,'EditBio',NULL,'renderMyContact','Allow write to biography on my contact page'),
        (5,'my_availability',NULL,'Participant Menu','Enables menu option throughout participant section and enables page.'),
        (6,'search_panels',NULL,'Participant Menu','Enables menu option throughout participant section and enables page.'),
        (7,'my_panel_interests',NULL,'Participant Menu','Enables menu option throughout participant section and enables page.'),
        (8,'my_schedule',NULL,'Participant Menu','Enables menu option throughout participant section and enables page.'),
        (9,'my_suggestions_write',NULL,'MySuggestions','Enables write access to the form elements on the page MySuggestions.'),
        (10,'my_gen_int_write',NULL,'MyGeneralInterests','Enables write access to the form elements on the page My General Interests'),
        (11,'BrainstormSubmit',NULL,'EditCreateSession','Brainstorm user can create session'),
        (12,'BS_sear_sess',NULL,'SearchSessions','Brainstorm user can view sessions'),
        (13,'public_login',NULL,'Login','Brainstorm user can log in'),
        (14,'SendEmail',NULL,'StaffManageParticipants','Access to Send email set of pages'),
        (15,'postcon',NULL,'renderWelcome','Forces participant welcome page to display only post con message.'),
        (16,'EditSesNtsAsgnPartPg',NULL,'StaffAssignParticipants','Edit '),
        (17,'ConfigureReports',NULL,'ConfigureReports','Enable menu option'),
        (18,'ResetUserPassword',NULL,'AdminParticipants','No one needs this permission once password overhaul is implemented and system has email integration.'),
        (19,'AdminPhases',NULL,'AdminPhases','Change phase of Zambia use, allowing which sections are current.'),
        (20,'CreateUser',NULL,'CreateUser','Manually create user. Must have edit for one or more roles.'),
        (21,'EditUserPermRoles',NULL,'Admin Participants','Control all user permission roles. Also used on create user page.'),
        (22,'EditUserPermRoles',4,'Admin Participants','Control participant user permission role only. Also used on create user page.'),
        (23,'EditUserPermRoles',3,'Admin Participants','Control staff user permission role only. Also used on create user page.'),
        (24,'edit_my_contact',NULL,'profile/my_contact','Enables submit; All login roles should have it.'),
        (25,'general_interests',NULL,'Participant Menu','Enables menu option throughout participant section and enables page.'),
        (26,'EditAnyConfigurationTable',NULL,'Edit Configuration Tables','Global permission to edit configuration tables'),
        (27,'photos',NULL,'Participant Menu','Enable photo submission tab'),
        (28,'survey',NULL,'Participant Menu','Enable survey tab'),
        (29,'AdminPhotos',NULL,'Administer Photos','approve/deny photos'),
        (30,'edit_participant_tags',NULL,'Admin Participants','Change a participant\'s tags'),
        (31,'edit_participant_responses',NULL,'many','edit others\' survey responses, etc.'),
        (32,'declined_participant',NULL,'many','Upon login show only declined participant page'),
        (2000,'ce_BioEditStatuses',NULL,'Edit Configuration Tables','enables edit'),
        (2001,'ce_Credentials',NULL,'Edit Configuration Tables','enables edit'),
        (2002,'ce_Roles',NULL,'Edit Configuration Tables','enables edit'),
        (2003,'ce_KidsCategories',NULL,'Edit Configuration Tables','enables edit'),
        (2004,'ce_LanguageStatuses',NULL,'Edit Configuration Tables','enables edit'),
        (2005,'ce_PubStatuses',NULL,'Edit Configuration Tables','enables edit'),
        (2006,'ce_SessionStatuses',NULL,'Edit Configuration Tables','enables edit'),
        (2007,'ce_Divisions',NULL,'Edit Configuration Tables','enables edit'),
        (2008,'ce_RegTypes',NULL,'Edit Configuration Tables','enables edit'),
        (2009,'ce_Tags',NULL,'Edit Configuration Tables','enables edit'),
        (2010,'ce_Times',NULL,'Edit Configuration Tables','enables edit'),
        (2011,'ce_Tracks',NULL,'Edit Configuration Tables','enables edit'),
        (2012,'ce_Types',NULL,'Edit Configuration Tables','enables edit'),
        (2013,'ce_Rooms',NULL,'Edit Configuration Tables','enables edit'),
        (2014,'ce_RoomSets',NULL,'Edit Configuration Tables','enables edit'),
        (2015,'ce_RoomHasSet',NULL,'Edit Configuration Tables','enables edit'),
        (2016,'ce_Features',NULL,'Edit Configuration Tables','enables edit'),
        (2017,'ce_Services',NULL,'Edit Configuration Tables','enables edit'),
        (2018,'ce_EmailFrom',NULL,'Edit Configuration Tables','enables edit'),
        (2019,'ce_EmailTo',NULL,'Edit Configuration Tables','enables edit'),
        (2020,'ce_EmailCC',NULL,'Edit Configuration Tables','enables edit'),
        (2021,'ce_PhotoDenialReasons',NULL,'Edit Configuration Tables','enables edit'),
        (2022,'ce_ParticipantTags',NULL,'Edit Configuration Tables','enables edit'),
        (10001,'ConTrollImportUsers',NULL,'Import Reg User','enables importing users from ConTroll');

INSERT INTO `PermissionRoles`
        (permroleid, permrolename, notes, display_order)
    VALUES
        (1,'Administrator','Damn well everything',10),
        (2,'Senior Staff','Send email & grant staff',20),
        (3,'Staff','Much stuff',30),
        (4,'Participant','Login as participant',40),
        (5,'Declined Participant','See only declined participant page',50),
        (6,'Brainstorm','Use for Brainstorm pages',60);

INSERT INTO `Phases`
        (phaseid, phasename, current, notes, implemented, display_order)
    VALUES
        (1, 'Initial invitation', 1, 'Login, welcome, and profile', 1, 10),
        (2, 'General information', 1, 'Availability, general interests, and suggestions', 2, 20),
        (3, 'Edit stuff for publications', 1, 'Even after print deadline might not deactivate if you want to allow updating of online publications', 1, 30),
        (4, 'Survey', 1, 'Edit survey responses', 1, 40),
        (5, 'Panel sign up', 0, 'Search and sign up for panels',1, 50),
        (6, 'Show schedule', 0, 'View schedule page',1, 60),
        (7, 'Post con', 0, 'Only post con page is visible to particpants',1, 70),
        (8, 'Brainstorm', 0, 'Users may suggest schedule items on brainstorm pages',1, 80);

    INSERT INTO `Permissions`
        (permissionid, permatomid, phaseid, permroleid)
    VALUES
        #######################
        ## Staff permissions ##
        #######################
        ## "Staff" Staff Menu and login -- permatomid: 1
        (1, 1, NULL, 1),
        (2, 1, NULL, 2),
        (3, 1, NULL, 3),
        ## "Administrator" Misc Administrator -- permatomid: 2
        ## 1) Config table editor render or post, 2) Edit custom text render or post,
        ## 3) Edit survey render, 4) Preview survey render, 5) Edit survey post,
        ## 6) 1-5 above in menus
        (4, 2, NULL, 1),
        ## "SendEmail" Send email -- permatomid: 14
        (5, 14, NULL, 1),
        (6, 14, NULL, 2),
        ## "EditSesNtsAsgnPartPg" Edit notes for program staff on assign participants page -- permatomid: 16
        (7, 16, NULL, 1),
        (8, 16, NULL, 2),
        (9, 16, NULL, 3),
        ## "ConfigureReports" Build Report Menus -- permatomid: 17
        ## Complete: Menu and page
        (10, 17, NULL, 1),
        ## "ResetUserPassword" Edit User Password -- permatomid: 18
        ## If self service email reset is working, don't grant to anyone
        ## Incomplete: Controls rendering on Admin Participants page, but doesn't gate back end
        ## "AdminPhases" Administer Phases -- permatomid: 19
        ## Complete: Staff menu, page rendering, back end action
        (11, 19, NULL, 1),
        ## "CreateUser" Create User -- permatomid: 20
        ## Must also have edit for one or more roles
        ## Complete: Staff menu, page rendering, back end action
        ## If integrated with ConTroll, don't grant to anyone or maybe only administrator to create a few initial users
        ## "EditUserPermRoles" Edit user permission roles -- permatomid: 21, 22, 23
        ## This is a non-standard permission atom with 3 different rows in PermissionAtoms table
        ## having the same permatomtag.  There is special code in EditPermRoles_FNC.php for wrangling
        ## this atom.
        ## Complete: Render and back end on Admin Participants, Create User, and Import User from ConTroll
        (12, 21, NULL, 1),
        (13, 22, NULL, 2),
        (14, 22, NULL, 3),
        ## "edit_my_contact" writability of my profile/my contact page -- permatomid: 24
        ## Also writability of participant photo
        ## Back end only
        ## Staff (and participants) need this to change their passwords
        (15, 24, NULL, 1),
        ## "EditAnyConfigurationTable" Edit Any Configuration Table -- permatomid: 26
        ## Complete: Menu, Render and back end
        (16, 26, NULL, 1),
        ## "AdminPhotos" Approve or deny photos -- permatomid: 29
        ## Incomplete: Page render and back end, but not menus
        (17, 29, NULL, 1),
        (18, 29, NULL, 2),
        ## "edit_participant_tags" Edit a user's tags -- permatomid: 30
        ## Complete: render and back end
        (19, 30, NULL, 1),
        (20, 30, NULL, 2),
        (21, 30, NULL, 3),
        ## "edit_participant_responses" Edit a participant's responses -- permatomid: 31
        ## Currently implemented for survey and general interests
        ## Complete: render and back end
        (22, 31, NULL, 1),
        (23, 31, NULL, 2),
        ## "ce_BioEditStatuses" -- permatomid: 2000 through
        ## "ce_ParticipantTags" -- permatomid: 2022
        ## These work very similarly to "EditAnyConfigurationTable" except that they control
        ## a single table each.
        ## Complete: Menu, Render and back end
        ## Not granted in standard configuration
        ## "ConTrollImportUsers" Inport user from Control -- permatomid: 10000
        ## User also needs permission to edit a role in order to grant the imported
        ## user a role.
        ## Complete: Menu, Render and back end
        (24, 10001, NULL, 1),
        (25, 10001, NULL, 2),
        (26, 10001, NULL, 3),
        #############################
        ## Participant permissions ##
        #############################
        ## "Participant" several particpant functions -- permatomid: 3
        ## Login as participant, get participant menu initially, flag self as interested
        (27, 3, 1, 4),
        ## "EditBio" several particpant functions -- permatomid: 4
        ## On "My Profile" page, edit bio, pubsname, and credentials
        ## Incomplete: Page render and back end except no back end protection for credentials
        ## No protection on admin participants
        (28, 4, 3, 4),
        ## "my_availability" availability page -- permatomid: 5
        ## Menu and rendering of availability page.  No back end protection.
        (29, 5, 2, 4),
        ## "search_panels" search panels page -- permatomid: 6
        ## Menu and rendering of availability page.  No back end protection.
        (30, 6, 5, 4),
        ## "my_panel_interests" my panel interests page -- permatomid: 7
        ## Menu and rendering of availability page.  No back end protection.
        (31, 7, 5, 4),
        ## "my_schedule" my schedule page -- permatomid: 8
        ## Menu and rendering of availability page.  There is no back end to protect because page is read only.
        (32, 8, 6, 4),
        ## "my_suggestions_write" writability of suggestion page -- permatomid: 9
        ## Render and back end
        (33, 9, 2, 4),
        ## "my_gen_int_write" writability of my general interests page -- permatomid: 10
        ## Render and back end
        (34, 10, 2, 4),
        ## "postcon" force participant welcome page to be post con page -- permatomid: 15
        (35, 15, 7, 4),
        ## "edit_my_contact" writability of my profile/my contact page -- permatomid: 24
        ## Also writability of participant photo
        ## Back end only
        ## Also granted to staff so they can edit their passwords, etc.
        ## Why does this exist?
        (36, 24, 1, 4),
        ## "general_interests" my general interests page -- permatomid: 25
        ## Menu and render of my general interests page -- no backend
        (37, 25, 2, 4),
        ## "photos" photos page -- permatomid: 27
        ## Menu only -- no render or backend
        ## Don't need to use this to disable photos entirely -- use db_name
        (38, 27, 2, 4),
        ## "survey" photos page -- permatomid: 28
        ## Menu only -- no render or backend
        ## Don't need to use this to disable survey entirely -- use db_name
        (39, 28, 4, 4),
        ######################################
        ## Declined participant permissions ##
        ######################################
        ## "declined_participant" replace welcome page with declined participant page -- permatomid: 28
        ##
        (40, 32, NULL, 5),
        ############################
        ## Brainstorm permissions ##
        ############################
        ## Actually assigned to participants
        ## Brainstorm user, i.e. anonymous login is no longer supported and needs to be re-implemented
        ##
        ## "BrainstormSubmit" user can create session -- permatomid: 11
        ## Menu and render -- no backend
        ## "Brainstorm" menu item can appear on staff menu, but isn't configured here
        (41, 11, 8, 4),
        ## "BS_sear_sess" search session from brainstorm pages -- permatomid: 12
        ## backend only
        ## Some other paths for viewing brainstorm items are still supported without any permission protection
        ## other than login
        (42, 12, 8, 4);
        ## "public_login" special anonymous login for brainstorm pages -- permatomid: 13
        ## No longer supported

INSERT INTO `UserHasPermissionRole`
        (badgeid, permroleid)
    SELECT
            badgeid, permroleid
        FROM
            UHPR_SAVE;

DROP TABLE `UHPR_SAVE`;

INSERT INTO PatchLog (patchname) VALUES ('66_permission_cleanup.sql');
