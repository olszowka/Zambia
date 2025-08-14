<?php
// Copyright (c) 2023 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant Schedule 2';
$report['description'] = 'Export CSV file of full participant schedule by participant, time';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 23,
);
$report['csv_output'] = true;
$report['group_concat_expand'] = false;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT 
        P.pubsname AS 'Participant', 
        IF ((moderator=1),'moderator', ' ') AS Moderator,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',starttime),'%a %l:%i %p') AS 'Start Time', 
        CASE
            WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
            WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
            ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
            END
            AS duration,
        R.roomname, S.sessionid, S.title, S.progguiddesc, S.notesforpart, SUBQ.participants
    FROM
                  Schedule SCH
             JOIN Sessions S USING (sessionid)
             JOIN Rooms R USING (roomid)
             JOIN ParticipantOnSession POS USING (sessionid)
             JOIN Participants P USING (badgeid)
        LEFT JOIN (SELECT 
                SCH2.sessionid, GROUP_CONCAT(P2.pubsname, IF(POS2.moderator=1,' (m)','') ORDER BY POS2.moderator DESC SEPARATOR ', ') AS "participants"
            FROM
                     Schedule SCH2
                JOIN ParticipantOnSession POS2 USING (sessionid)
                JOIN Participants P2 USING (badgeid)
            GROUP BY
                SCH2.sessionid
        ) AS SUBQ USING (sessionid)
    ORDER BY
        CAST(P.badgeid AS unsigned),
        SCH.starttime;
EOD;
$report['output_filename'] = 'allpartsched2.csv';
$report['column_headings'] = 'Participant, Moderator, Start Time, Duration, Room, Session ID, Title, Descriptions, Notes for Participants, All Participants';
