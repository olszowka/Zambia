## This script adds the ConfigurePermissions permission atom, used to gate the upcoming
## "Configure Permissions" admin page and its related server side endpoints, which will
## allow editing of the Permissions, Phases, and PermissionRoles tables. It also adds a
## display_order column to PermissionAtoms, so that page can offer drag-to-reorder of the
## permission matrix rows, same as it already does for PermissionRoles and Phases, and a
## permatomname column to give each permission atom a human-friendly display name distinct
## from its internal permatomtag.
##
##	Created by Peter Olszowka on 2026-07-22;
## 	Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##

INSERT INTO PermissionAtoms(permatomid, permatomtag, page, notes)
VALUES (33, 'ConfigurePermissions', 'ConfigurePermissions', 'Allows editing the Permissions, Phases, and PermissionRoles tables');

INSERT INTO Permissions(permatomid, phaseid, permroleid)
SELECT a.permatomid, null, r.permroleid
FROM PermissionAtoms a
JOIN PermissionRoles r ON (r.permrolename = 'Administrator')
WHERE permatomtag = 'ConfigurePermissions';

ALTER TABLE PermissionAtoms ADD COLUMN display_order INT(11) AFTER notes;
ALTER TABLE PermissionAtoms ADD COLUMN permatomname VARCHAR(50) AFTER permatomtag;

## Populate permatomname with a human-friendly display name for each atom (including the one
## just inserted above), refresh notes with a matching "(Role) description" convention, and set
## display_order to group administrative atoms first, then config-table-editor atoms, then
## participant-facing atoms, then brainstorm atoms last.
UPDATE PermissionAtoms SET permatomname = 'Staff General', notes = '(Admin & Staff) Enables staff login, menu, welcome page, etc.', display_order = 10 WHERE permatomtag = 'Staff';
UPDATE PermissionAtoms SET permatomname = 'Administrator General', notes = '(Admin) Configure survey, custom text, etc.', display_order = 20 WHERE permatomtag = 'Administrator';
UPDATE PermissionAtoms SET permatomname = 'Participant General', notes = '(Participant) Login as Participant', display_order = 410 WHERE permatomtag = 'Participant';
UPDATE PermissionAtoms SET permatomname = 'Edit Published Items', notes = '(Participant) Allow edit of biography and name for publications.', display_order = 430 WHERE permatomtag = 'EditBio';
UPDATE PermissionAtoms SET permatomname = 'Availability', notes = '(Participant) Availability page', display_order = 460 WHERE permatomtag = 'my_availability';
UPDATE PermissionAtoms SET permatomname = 'Search Sessions', notes = '(Participant) Access Search Sessions page', display_order = 500 WHERE permatomtag = 'search_panels';
UPDATE PermissionAtoms SET permatomname = 'Session Interests', notes = '(Participant) Access Session Interests (Panel Interests) page', display_order = 510 WHERE permatomtag = 'my_panel_interests';
UPDATE PermissionAtoms SET permatomname = 'My Schedule', notes = '(Participant) Access My Schedule page', display_order = 520 WHERE permatomtag = 'my_schedule';
UPDATE PermissionAtoms SET permatomname = 'My Suggestions Write', notes = '(Participant) Edit My Suggestions page', display_order = 470 WHERE permatomtag = 'my_suggestions_write';
UPDATE PermissionAtoms SET permatomname = 'General Interests Write', notes = '(Participant) Edit General Interests page', display_order = 450 WHERE permatomtag = 'my_gen_int_write';
UPDATE PermissionAtoms SET permatomname = 'Brainstorm Submit Session', notes = '(Participant) Can view Brainstorm section and submit brainstorm session', display_order = 550 WHERE permatomtag = 'BrainstormSubmit';
UPDATE PermissionAtoms SET permatomname = 'Brainstorm Search Session', notes = '(Participant) Can view sessions from Brainstorm section', display_order = 560 WHERE permatomtag = 'BS_sear_sess';
UPDATE PermissionAtoms SET permatomname = 'Brainstorm Public Login', notes = '(Other) Not used', display_order = 570 WHERE permatomtag = 'public_login';
UPDATE PermissionAtoms SET permatomname = 'Send Email', notes = '(Staff) Access to Send email set of pages', display_order = 70 WHERE permatomtag = 'SendEmail';
UPDATE PermissionAtoms SET permatomname = 'Post Con', notes = '(Participant) Can view only Post Con page', display_order = 530 WHERE permatomtag = 'postcon';
UPDATE PermissionAtoms SET permatomname = 'Notes For Program Staff', notes = '(Staff) Edit notes for program staff on assign participants page ', display_order = 80 WHERE permatomtag = 'EditSesNtsAsgnPartPg';
UPDATE PermissionAtoms SET permatomname = 'Report Menus', notes = '(Admin) Run process to update report menus', display_order = 40 WHERE permatomtag = 'ConfigureReports';
UPDATE PermissionAtoms SET permatomname = 'Reset User Password', notes = '(Staff) No one needs this permission once password overhaul is implemented and system has email integration.', display_order = 90 WHERE permatomtag = 'ResetUserPassword';
UPDATE PermissionAtoms SET permatomname = 'Phase Activation', notes = '(Admin) Change phase of Zambia use, allowing which sections are current.', display_order = 30 WHERE permatomtag = 'AdminPhases';
UPDATE PermissionAtoms SET permatomname = 'Create User', notes = '(Staff) Manually create user. Must have edit for one or more roles.', display_order = 100 WHERE permatomtag = 'CreateUser';
UPDATE PermissionAtoms SET permatomname = 'Edit User Permission Roles', notes = '(Staff) Control all user permission roles. Also used on create user page.', display_order = 110 WHERE permatomtag = 'EditUserPermRoles' AND elementid IS NULL;
UPDATE PermissionAtoms SET permatomname = 'Edit User Permission Roles (Participant)', notes = '(Staff) Control participant user permission role only. Also used on create user page.', display_order = 130 WHERE permatomtag = 'EditUserPermRoles' AND elementid = 4;
UPDATE PermissionAtoms SET permatomname = 'Edit User Permission Roles (Staff)', notes = '(Staff) Control staff user permission role only. Also used on create user page.', display_order = 120 WHERE permatomtag = 'EditUserPermRoles' AND elementid = 3;
UPDATE PermissionAtoms SET permatomname = 'Edit Profile', notes = '(Staff & Participant) All login roles should have it to change their passwords.', display_order = 420 WHERE permatomtag = 'edit_my_contact';
UPDATE PermissionAtoms SET permatomname = 'General Interests', notes = '(Participant) Access General Interests page', display_order = 440 WHERE permatomtag = 'general_interests';
UPDATE PermissionAtoms SET permatomname = 'Edit Any Configuration Table', notes = '(Admin) Global permission to edit configuration tables', display_order = 50 WHERE permatomtag = 'EditAnyConfigurationTable';
UPDATE PermissionAtoms SET permatomname = 'Photo', notes = '(Participant) Access Photo page', display_order = 480 WHERE permatomtag = 'photos';
UPDATE PermissionAtoms SET permatomname = 'Survey', notes = '(Participant) Access Survey page', display_order = 490 WHERE permatomtag = 'survey';
UPDATE PermissionAtoms SET permatomname = 'Approve Photos', notes = '(Staff) Approve and deny participant photos', display_order = 140 WHERE permatomtag = 'AdminPhotos';
UPDATE PermissionAtoms SET permatomname = 'Edit Participant Tags', notes = '(Staff) Change a participant\'s tags', display_order = 150 WHERE permatomtag = 'edit_participant_tags';
UPDATE PermissionAtoms SET permatomname = 'Edit Participant Responses', notes = '(Staff) edit others\' survey responses, etc.', display_order = 160 WHERE permatomtag = 'edit_participant_responses';
UPDATE PermissionAtoms SET permatomname = 'Declined Participant', notes = '(Other) Can view only Declined Participant page', display_order = 540 WHERE permatomtag = 'declined_participant';
UPDATE PermissionAtoms SET permatomname = 'Configure Permissions', notes = '(Admin) Use Configure Permissions page to edit the Permissions, Phases, and PermissionRoles', display_order = 60 WHERE permatomtag = 'ConfigurePermissions';
UPDATE PermissionAtoms SET permatomname = 'Configure BioEditStatuses', notes = '(Admin) This table is not currently used', display_order = 170 WHERE permatomtag = 'ce_BioEditStatuses';
UPDATE PermissionAtoms SET permatomname = 'Configure Credentials', notes = '(Admin) Characteristic of participant to distinguish author, editor, etc.', display_order = 180 WHERE permatomtag = 'ce_Credentials';
UPDATE PermissionAtoms SET permatomname = 'Configure Roles', notes = '(Admin) Characteristic of participant such as panelist, lecturer, etc.', display_order = 190 WHERE permatomtag = 'ce_Roles';
UPDATE PermissionAtoms SET permatomname = 'Configure KidsCategories', notes = '(Admin) Characteristic of session to descript age appropriateness', display_order = 200 WHERE permatomtag = 'ce_KidsCategories';
UPDATE PermissionAtoms SET permatomname = 'Configure LanguageStatuses', notes = '(Admin) This table is not currently used', display_order = 210 WHERE permatomtag = 'ce_LanguageStatuses';
UPDATE PermissionAtoms SET permatomname = 'Configure PubStatuses', notes = '(Admin) Whether a session will be publicized', display_order = 220 WHERE permatomtag = 'ce_PubStatuses';
UPDATE PermissionAtoms SET permatomname = 'Configure SessionStatuses', notes = '(Admin) Steps in workflow for a session', display_order = 230 WHERE permatomtag = 'ce_SessionStatuses';
UPDATE PermissionAtoms SET permatomname = 'Configure Divisions', notes = '(Admin) Characteristic of session to indicate responsible team within convention', display_order = 240 WHERE permatomtag = 'ce_Divisions';
UPDATE PermissionAtoms SET permatomname = 'Configure RegTypes', notes = '(Admin) Provide a description for a code from the registration system', display_order = 250 WHERE permatomtag = 'ce_RegTypes';
UPDATE PermissionAtoms SET permatomname = 'Configure Tags', notes = '(Admin) Characteristic of session to categorize by topic; may be published', display_order = 260 WHERE permatomtag = 'ce_Tags';
UPDATE PermissionAtoms SET permatomname = 'Configure Times', notes = '(Admin) Time slots which appear on the participant availability page', display_order = 270 WHERE permatomtag = 'ce_Times';
UPDATE PermissionAtoms SET permatomname = 'Configure Tracks', notes = '(Admin) Characteristic of session to categorize by topic; may be published', display_order = 280 WHERE permatomtag = 'ce_Tracks';
UPDATE PermissionAtoms SET permatomname = 'Configure Types', notes = '(Admin) Characteristic of session to categorize by activity; may be published', display_order = 290 WHERE permatomtag = 'ce_Types';
UPDATE PermissionAtoms SET permatomname = 'Configure Rooms', notes = '(Admin) Real or virtual location of session', display_order = 300 WHERE permatomtag = 'ce_Rooms';
UPDATE PermissionAtoms SET permatomname = 'Configure RoomSets', notes = '(Admin) Arrangement of furniture in a room', display_order = 310 WHERE permatomtag = 'ce_RoomSets';
UPDATE PermissionAtoms SET permatomname = 'Configure RoomHasSet', notes = '(Admin) Which furniture arrangement fits in which room and what the capacity will be', display_order = 320 WHERE permatomtag = 'ce_RoomHasSet';
UPDATE PermissionAtoms SET permatomname = 'Configure Features', notes = '(Admin) Characteristic of session describing aspects of the room it requires', display_order = 330 WHERE permatomtag = 'ce_Features';
UPDATE PermissionAtoms SET permatomname = 'Configure Services', notes = '(Admin) Characteristic of session describing things staff must provide for it', display_order = 340 WHERE permatomtag = 'ce_Services';
UPDATE PermissionAtoms SET permatomname = 'Configure EmailFrom', notes = '(Admin) From addresses for email messages', display_order = 350 WHERE permatomtag = 'ce_EmailFrom';
UPDATE PermissionAtoms SET permatomname = 'Configure EmailTo', notes = '(Admin) Queries to select whom to send email messages to', display_order = 360 WHERE permatomtag = 'ce_EmailTo';
UPDATE PermissionAtoms SET permatomname = 'Configure EmailCC', notes = '(Admin) CC addresses for email message', display_order = 370 WHERE permatomtag = 'ce_EmailCC';
UPDATE PermissionAtoms SET permatomname = 'Configure PhotoDenialReasons', notes = '(Admin) Reasons for denying a photo', display_order = 380 WHERE permatomtag = 'ce_PhotoDenialReasons';
UPDATE PermissionAtoms SET permatomname = 'Configure ParticipantTags', notes = '(Admin) Miscellaneous characteristic of a particpant', display_order = 390 WHERE permatomtag = 'ce_ParticipantTags';
UPDATE PermissionAtoms SET permatomname = 'ConTroll Import Users', notes = '(Staff) Enables importing users from ConTroll', display_order = 400 WHERE permatomtag = 'ConTrollImportUsers';

INSERT INTO PatchLog (patchname) VALUES ('72_configure_permissions.sql');
