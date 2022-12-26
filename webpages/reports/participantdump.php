<?php
// Copyright (c) 2022 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka on 2022-10-12
$report = [];
$report['name'] = 'Participant data dump';
$report['description'] = 'Export CSV file of all participant contact and survey information';
$report['categories'] = array(
    'Participant Info Reports' => 500
);
$report['csv_output'] = true;
$report['group_concat_expand'] = false;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        P.badgeid,
        CASE IFNULL(P.interested, 3)
             WHEN 0 THEN 'DIDN\'T ANSWER'
             WHEN 1 THEN 'YES'
             WHEN 2 THEN 'NO'
             WHEN 3 THEN 'DIDN\'T LOG IN' END AS interested,
        P.htmlbio, P.pubsname, CD.badgename, CD.lastname, CD.firstname, CD.phone, CD.email,
        CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip, CD.postcountry,
        ROLES.permroles, PSA1.value AS parttype, PSA2.value AS record, PSA3.value AS accessibility, PSA4.value AS stem,
        PSA5.value AS diversity, PSA6.value AS formats, PSA7.value AS ideas, PSA8.value as booklaunch, 
        PSA10.value AS experience, PSA11.value AS website, PSA12.value AS facebook, PSA13.value AS twitter,
        PSA14.value AS instagram, PSA15.value AS moderator, PSA16.value AS avoid, PSA17.value as pronouns
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN (
            SELECT
                    UHPR3.badgeid, GROUP_CONCAT(PR.permrolename, ', ') AS permroles
                FROM
                         UserHasPermissionRole UHPR3
                    JOIN PermissionRoles PR USING (permroleid)
                WHERE
                     EXISTS (SELECT *
                                FROM
                                    UserHasPermissionRole UHPR4
                                WHERE
                                        UHPR4.badgeid = UHPR3.badgeid
                                    AND UHPR4.permroleid IN (3, 4, 6) /* potential, confirm, or denied partcipant */
                                )
                GROUP BY
                    UHPR3.badgeid
                   ) AS ROLES USING (badgeid)
        LEFT JOIN ParticipantSurveyAnswers PSA1 ON PSA1.participantid = P.badgeid AND PSA1.questionid = 1
        LEFT JOIN ParticipantSurveyAnswers PSA2 ON PSA2.participantid = P.badgeid AND PSA2.questionid = 2
        LEFT JOIN ParticipantSurveyAnswers PSA3 ON PSA3.participantid = P.badgeid AND PSA3.questionid = 3
        LEFT JOIN ParticipantSurveyAnswers PSA4 ON PSA4.participantid = P.badgeid AND PSA4.questionid = 4
        LEFT JOIN ParticipantSurveyAnswers PSA5 ON PSA5.participantid = P.badgeid AND PSA5.questionid = 5
        LEFT JOIN ParticipantSurveyAnswers PSA6 ON PSA6.participantid = P.badgeid AND PSA6.questionid = 6
        LEFT JOIN ParticipantSurveyAnswers PSA7 ON PSA7.participantid = P.badgeid AND PSA7.questionid = 7
        LEFT JOIN ParticipantSurveyAnswers PSA8 ON PSA8.participantid = P.badgeid AND PSA8.questionid = 8
        LEFT JOIN ParticipantSurveyAnswers PSA10 ON PSA10.participantid = P.badgeid AND PSA10.questionid = 10
        LEFT JOIN ParticipantSurveyAnswers PSA11 ON PSA11.participantid = P.badgeid AND PSA11.questionid = 11
        LEFT JOIN ParticipantSurveyAnswers PSA12 ON PSA12.participantid = P.badgeid AND PSA12.questionid = 12
        LEFT JOIN ParticipantSurveyAnswers PSA13 ON PSA13.participantid = P.badgeid AND PSA13.questionid = 13
        LEFT JOIN ParticipantSurveyAnswers PSA14 ON PSA14.participantid = P.badgeid AND PSA14.questionid = 14
        LEFT JOIN ParticipantSurveyAnswers PSA15 ON PSA15.participantid = P.badgeid AND PSA15.questionid = 15
        LEFT JOIN ParticipantSurveyAnswers PSA16 ON PSA16.participantid = P.badgeid AND PSA16.questionid = 16
        LEFT JOIN ParticipantSurveyAnswers PSA17 ON PSA17.participantid = P.badgeid AND PSA17.questionid = 17
    WHERE
        EXISTS ( SELECT *
                    FROM
                        UserHasPermissionRole UHPR2
                    WHERE
                            UHPR2.badgeid = P.badgeid
                        AND UHPR2.permroleid IN (3, 4, 6) /* potential, confirm, or denied partcipant */
            )
    ORDER BY
        P.pubsname;
EOD;
$report['queries']['roles'] =<<<'EOD'

EOD;
$report['output_filename'] = 'participant_data_dump.csv';
$report['column_headings'] = 'userid,interested,bio,"name for pubs","badge name","last name","first name",phone,email,' .
    'address1,address2,city,state,"postal code",country,"permission roles","participation type",record,accessibility,' .
    'stem,diversity,formats,ideas,"book launch",experience,website,facebook,twitter,instagram,moderator,' .
    'avoid,pronouns';
