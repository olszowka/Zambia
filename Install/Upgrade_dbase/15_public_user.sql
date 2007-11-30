## This script makes the permission data changes to distinguish between public user login permission
## and brainstorm (suggest session) permission.
Update PermissionAtoms
     set permatomtag='BrainstormSubmit', notes='Allow suggestion from special brainstorm user and pages.'
     where permatomid=18;
Insert into PermissionAtoms
     set permatomid=20, permatomtag='public_login', notes='Allow login as public user(s).';
Update Phases
     set phasename='PublicLogin', notes='Public login user(s) enabled.'
     where phaseid=6;
Insert into Phases
     set phaseid=7, phasename='Brainstorm', notes='Brainstorming (submitting session suggestions) allowed.';
Update Permissions
     set phaseid=7 where permatomid=18;
Insert into Permissions (permissionid, permatomid, phaseid, permroleid, badgeid)
     values (null, 20, 6, 1, null), (null, 20, 6, 3, null), (null, 20, 6, 5, null);
Insert into PatchLog (patchname) values ('15_public_user.sql');
