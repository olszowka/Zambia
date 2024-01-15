<?php
// Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka on 2022-10-12
// Configured for B61 survey
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
        ROLES.permroles, PSA1.value AS pronouns, PSA3.value AS accessibility, PSA4.value AS diversity,
        PSA5.value AS moderator, PSA6.value AS contact, PSA7.value AS experience
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
                                    AND UHPR4.permroleid IN (4) /* partcipant */
                                )
                GROUP BY
                    UHPR3.badgeid
                   ) AS ROLES USING (badgeid)
        LEFT JOIN ParticipantSurveyAnswers PSA1 ON PSA1.participantid = P.badgeid AND PSA1.questionid = 1
        LEFT JOIN ParticipantSurveyAnswers PSA3 ON PSA3.participantid = P.badgeid AND PSA3.questionid = 3
        LEFT JOIN ParticipantSurveyAnswers PSA4 ON PSA4.participantid = P.badgeid AND PSA4.questionid = 4
        LEFT JOIN ParticipantSurveyAnswers PSA5 ON PSA5.participantid = P.badgeid AND PSA5.questionid = 5
        LEFT JOIN ParticipantSurveyAnswers PSA6 ON PSA6.participantid = P.badgeid AND PSA6.questionid = 6
        LEFT JOIN ParticipantSurveyAnswers PSA7 ON PSA7.participantid = P.badgeid AND PSA7.questionid = 7
    WHERE
        EXISTS ( SELECT *
                    FROM
                        UserHasPermissionRole UHPR2
                    WHERE
                            UHPR2.badgeid = P.badgeid
                        AND UHPR2.permroleid IN (4) /* partcipant */
            )
    ORDER BY
        P.pubsname;
EOD;
$report['queries']['roles'] =<<<'EOD'

EOD;
$report['output_filename'] = 'participant_data_dump.csv';
$report['column_headings'] = 'userid, interested, bio,"name for pubs", "badge name", "last name", "first name", ' .
    'phone, email, address1, address2, city, state, "postal code", country, "permission roles", "pronouns", ' .
    'accessibility, diversity, experience, moderator, contact';
