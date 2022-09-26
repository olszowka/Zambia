/* This MySQL script will clear out all the data from a Zambia database for a single year's con, but
   leave all configuration and past year data in place. This will also save the bios in table PreviousParticipants
   for use in following year.  Note, table PreviousParticipants is used by Congo interface script, but
   not by Zambia per se.*/
TRUNCATE TABLE PreviousParticipants;
TRUNCATE TABLE Schedule;
TRUNCATE TABLE ParticipantAvailabilityDays;
TRUNCATE TABLE ParticipantAvailabilityTimes;
TRUNCATE TABLE ParticipantAvailability;
TRUNCATE TABLE ParticipantHasCredential;
TRUNCATE TABLE ParticipantInterests;
TRUNCATE TABLE ParticipantOnSession;
TRUNCATE TABLE ParticipantSessionInterest;
TRUNCATE TABLE ParticipantOnSessionHistory;
TRUNCATE TABLE ParticipantSuggestions;
TRUNCATE TABLE ParticipantPasswordResetRequests;
TRUNCATE TABLE ParticipantSurveyAnswers;
TRUNCATE TABLE SessionEditHistory;
TRUNCATE TABLE SessionHasTag;
TRUNCATE TABLE SessionHasFeature;
TRUNCATE TABLE SessionHasService;
TRUNCATE TABLE Sessions;
TRUNCATE TABLE EmailQueue;
TRUNCATE TABLE EmailHistory;
/*TRUNCATE TABLE UserHasPermissionRole;
TRUNCATE TABLE Participants;
TRUNCATE TABLE CongoDump; */
