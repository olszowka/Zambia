## Adds ParticipantSurveyResponses, a JSON mirror of ParticipantSurveyAnswers with one row per participant.
##
## Created by Peter Olszowka on 2026-08-06.
## Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
## ParticipantSurveyAnswers (one row per participant/question) remains the authoritative source and is left
## completely untouched by this patch, so any installation-specific override reports built against it keep
## working unchanged. ParticipantSurveyResponses is a derived, application-maintained mirror keyed by
## SurveyQuestionConfig.shortname: PartSurvey.php rebuilds a participant's row here every time it writes to
## ParticipantSurveyAnswers (see sync_participant_survey_responses_json() in db_functions.php), so the two
## tables never drift.
##
## Both badgeid and updatedby are FK'd to Participants(badgeid) with no ON DELETE clause (default
## RESTRICT), matching the "who did this" audit-column convention used elsewhere (e.g.
## CongoDumpHistory.createdbybadgeid, ParticipantOnSessionHistory.createdbybadgeid). Note this differs from
## ParticipantSurveyAnswers, whose participantid FK is ON DELETE CASCADE; ParticipantSurveyAnswers.updatedby
## has no FK at all. Those are pre-existing traits of the older table, out of scope here.
##
## The JSON column uses the JSON type -- native binary JSON on MySQL 8/9, and a LONGTEXT column with an
## automatic JSON_VALID() CHECK constraint on MariaDB (the JSON alias has existed since MariaDB 10.2.7, well
## below this project's 10.11 floor). The one-time backfill below deliberately avoids JSON_OBJECTAGG /
## JSON_ARRAYAGG (MariaDB 10.5+ only) in favor of GROUP_CONCAT combined with JSON_OBJECT/JSON_QUOTE (MySQL
## 5.7.22+ / MariaDB 10.2.7+), so the migration itself runs on the widest possible range of MariaDB 10.x
## servers even though this project only requires 10.11+. It also falls back to the participant's own
## badgeid for updatedby when a historical ParticipantSurveyAnswers.updatedby value doesn't (or no longer)
## correspond to a Participants row, so the new FK can't make the backfill fail on messy old data.

DROP TABLE IF EXISTS ParticipantSurveyResponses;

CREATE TABLE ParticipantSurveyResponses (
    badgeid VARCHAR(15) NOT NULL,
    answers JSON NOT NULL,
    lastupdate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updatedby VARCHAR(15) NOT NULL,
    PRIMARY KEY (badgeid),
    KEY updatedby (updatedby),
    CONSTRAINT participantsurveyresponses_ibfk_1 FOREIGN KEY (badgeid) REFERENCES Participants(badgeid),
    CONSTRAINT participantsurveyresponses_ibfk_2 FOREIGN KEY (updatedby) REFERENCES Participants(badgeid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC COMMENT='One row per participant: JSON mirror of ParticipantSurveyAnswers, keyed by SurveyQuestionConfig.shortname.';

## Backfill existing answers. group_concat_max_len is raised for this session only, so a participant with
## many/long answers doesn't get silently truncated into invalid JSON.
SET SESSION group_concat_max_len = 4194304;

INSERT INTO ParticipantSurveyResponses (badgeid, answers, updatedby, lastupdate)
SELECT
        agg.participantid,
        agg.answers,
        COALESCE(P.badgeid, agg.participantid),
        agg.lastupdate
    FROM (
        SELECT
                PSA.participantid,
                CONCAT('{', GROUP_CONCAT(
                    JSON_QUOTE(SQC.shortname), ':',
                    JSON_OBJECT('value', PSA.value, 'othertext', PSA.othertext, 'privacy_setting', PSA.privacy_setting)
                    ORDER BY SQC.display_order SEPARATOR ','
                ), '}') AS answers,
                SUBSTRING_INDEX(GROUP_CONCAT(PSA.updatedby ORDER BY PSA.lastupdate DESC SEPARATOR ','), ',', 1) AS last_updatedby,
                MAX(PSA.lastupdate) AS lastupdate
            FROM
                     ParticipantSurveyAnswers PSA
                JOIN SurveyQuestionConfig SQC ON SQC.questionid = PSA.questionid
            GROUP BY
                PSA.participantid
    ) agg
    LEFT JOIN Participants P ON P.badgeid = agg.last_updatedby;

INSERT INTO PatchLog (patchname) VALUES ('74_survey_responses_json.sql');
