## This script adds support to track whether participants give permission to share their email address with other participants.
## It adds a column to Participants.
Alter Table Participants Add Column `share_email` tinyint(11) Default Null After pubsname;
Insert Into `PatchLog` (`patchname`) Values ('34_share_email.sql');
