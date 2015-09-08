## This script adds support to track participant availability on Monday to support a 4-day con.
## It adds a column to participant availability.
Alter Table ParticipantAvailability Add Column `mondaymaxprog` int(11) default '0' after sundaymaxprog;
insert into `PatchLog` (`patchname`) values ('13_part_avail_mon.sql');
