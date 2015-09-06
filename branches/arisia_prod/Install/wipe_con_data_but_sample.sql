-- this script can be run to wipe most con specific data out of a dbase
-- it is used to build the script for building dbase with only minimal sample data
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE EmailHistory;
TRUNCATE TABLE EmailQueue;
TRUNCATE TABLE EmailTracking;
TRUNCATE TABLE UserHasPermissionRole;
TRUNCATE TABLE ParticipantAvailabilityTimes;
TRUNCATE TABLE ParticipantAvailabilityDays;
TRUNCATE TABLE ParticipantAvailability;
TRUNCATE TABLE ParticipantHasCredential;
TRUNCATE TABLE ParticipantHasRole;
TRUNCATE TABLE ParticipantInterests;
TRUNCATE TABLE ParticipantOnSession;
TRUNCATE TABLE ParticipantSessionInterest;
TRUNCATE TABLE ParticipantSuggestions;
TRUNCATE TABLE CongoDump;
TRUNCATE TABLE TrackCompatibility;
TRUNCATE TABLE PreviousSessions;
TRUNCATE TABLE PreviousConTracks;
TRUNCATE TABLE PreviousCons;
TRUNCATE TABLE PreviousParticipants;
TRUNCATE TABLE `Schedule`;
TRUNCATE TABLE SessionHasFeature;
TRUNCATE TABLE SessionHasService;
TRUNCATE TABLE SessionEditHistory;
TRUNCATE TABLE SessionHasPubChar;
TRUNCATE TABLE Sessions;
TRUNCATE TABLE Participants;
SET FOREIGN_KEY_CHECKS = 1;
--
-- Now you want to pull a backup using:
--    mysqldump -u username -p dbasename > EmptyDbase.dump
-- 


