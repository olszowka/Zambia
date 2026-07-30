/* Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
File created by Peter Olszowka on 2026-06-03
This MySQL script will clear out all the data from a Zambia database for a single year's con, but
leave all configuration and past year data in place.
It will leave the participants main records, but delete all child participant records.  It will
also set them all to not having logged in and not given permission to store data.   */
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE SessionHasFeature;
TRUNCATE TABLE SessionHasService;
TRUNCATE TABLE SessionHasTag;
TRUNCATE TABLE SessionEditHistory;
TRUNCATE TABLE Schedule;
TRUNCATE TABLE ParticipantOnSessionHistory;
TRUNCATE TABLE ParticipantOnSession;
TRUNCATE TABLE ParticipantSessionInterest;
TRUNCATE TABLE Sessions;
TRUNCATE TABLE ParticipantAvailabilityTimes;
TRUNCATE TABLE ParticipantAvailabilityDays;
TRUNCATE TABLE ParticipantAvailability;
TRUNCATE TABLE ParticipantHasCredential;
TRUNCATE TABLE ParticipantHasTag;
TRUNCATE TABLE ParticipantHasRole;
TRUNCATE TABLE ParticipantInterests;
TRUNCATE TABLE ParticipantSuggestions;
TRUNCATE TABLE ParticipantSurveyAnswers;
TRUNCATE TABLE EmailQueue;
TRUNCATE TABLE EmailHistory;
DELETE FROM UserHasPermissionRole WHERE badgeid != '13479'; /* Set this badgeid to your primary administrator */
UPDATE Participants SET interested = NULL, data_retention = 0;
SET FOREIGN_KEY_CHECKS = 1;
