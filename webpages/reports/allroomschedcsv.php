<?php
// Copyright (c) 2015-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant Schedule by Room';
$report['description'] = 'Export CSV file of full participant schedule by room, time';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 30,
    'Programming Reports' => 39
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        R.roomname,
        R.function,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',starttime),'%a %l:%i %p') as 'Start Time', 
        CASE
            WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
            WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
            ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
            END AS 'duration',
        T.Trackname,
        S.sessionid,
        S.title,
        GROUP_CONCAT(CONCAT(P.pubsname,' (',P.badgeid,')') SEPARATOR '; ') AS 'Participants' 
    FROM
                  Sessions S
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
        LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
        LEFT JOIN Participants P ON POS.badgeid=P.badgeid
        LEFT JOIN Tracks T ON T.trackid=S.trackid
    GROUP BY
        SCH.scheduleid 
    ORDER BY
        R.roomname, SCH.starttime;
EOD;
$report['output_filename'] = 'allroomsched.csv';
$report['column_headings'] = 'Room Name, Room Function, Start Time, Duration, Track, Session ID, Title, Participants';
