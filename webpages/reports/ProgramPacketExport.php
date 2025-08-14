<?php
// Copyright (c) 2017-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Program Packet Export';
$report['description'] = 'Export CSV file of Program Packet Export';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 120,
    'Programming Reports' => 40
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
WITH PartSched AS (
    SELECT
            POS.badgeid, 
            GROUP_CONCAT(
                DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p')," ",
                CASE
                    WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
                    WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
                    ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
                    END," ",
                R.roomname, "-",
                S.title,
                IF(moderator=1,'(M)','')
                ORDER BY SCH.starttime
                SEPARATOR "\n") AS panelinfo
            FROM
                     ParticipantOnSession POS
                JOIN Sessions S USING (sessionid)
                JOIN Schedule SCH USING (sessionid)
                JOIN Rooms R USING (roomid)
            GROUP BY
                POS.badgeid
    )
SELECT
        P.badgeid, P.pubsname, PartSched.panelinfo
    FROM
             Participants P
        JOIN PartSched USING (badgeid)
    WHERE EXISTS (
        SELECT * FROM UserHasPermissionRole UHPR
            WHERE 
                    UHPR.badgeid = P.badgeid
                AND UHPR.permroleid = 4 /* Participant (B61) */
        )
    ORDER BY
        P.pubsname;
EOD;
$report['output_filename'] = 'program_participant_packet_merge.csv';
$report['column_headings'] = 'badgeid,pubs name,panel info';
