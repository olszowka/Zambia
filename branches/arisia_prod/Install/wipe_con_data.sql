-- this script can be run to wipe any con specific data out of a dbase
-- it is used to build the script for building an empty dbase
drop table if exists save;
drop table if exists Roomtemp;
truncate table UserHasPermissionRole;
truncate table ParticipantAvailability;
truncate table ParticipantAvailabilityTimes;
truncate table ParticipantAvailabilityDays;
truncate table ParticipantHasCredential;
truncate table ParticipantHasRole;
truncate table ParticipantInterests;
truncate table ParticipantOnSession;
truncate table ParticipantSessionInterest;
truncate table ParticipantSuggestions;
truncate table CongoDump;
truncate table Schedule;
truncate table SessionHasFeature;
truncate table SessionHasService;
truncate table SessionEditHistory;
truncate table SessionHasPubChar;
truncate table Sessions;
truncate table Participants;
truncate table PreviousParticipants;

-- you don't want to disable constraints. Really! 
-- you want to run down the real problem instead  
-- because you missed zeroing a table and you need to zero it or eek!
--
-- Now you want to pull a backup using:
--    mysqldump -u username -p dbasename > EmptyDbase.dump
-- 
-- you might very well want the following: 
INSERT INTO `Participants` (badgeid, pubsname, password, bestway, interested, bio)
 VALUES ('brainstorm',null,'ecf65a5d41056d7dd4d548e3ef200476',null,null,null);
INSERT INTO `UserHasPermissionRole` VALUES ('brainstorm',5);


