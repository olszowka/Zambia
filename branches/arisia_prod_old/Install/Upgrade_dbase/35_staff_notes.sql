## This script adds support for staff recording notes about each participant.
## It adds a column to Participants.
ALTER TABLE Participants ADD COLUMN `staff_notes` TEXT NULL AFTER `share_email`;
INSERT INTO `PatchLog` (`patchname`) VALUES ('35_staff_notes.sql');
