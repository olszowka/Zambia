<?php
// Copyright (c) 2009-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Palm Calendar';
$report['description'] = 'Export CSV file for Palm device calendars';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 60,
    'Publication Reports' => 40
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
WITH Parts AS (
    SELECT
            sessionid, GROUP_CONCAT(P.pubsname SEPARATOR ', ') AS participants
        FROM
                 ParticipantOnSession POS
            JOIN Participants P USING (badgeid)
        GROUP BY
            sessionid
    )
SELECT
        DATE_FORMAT(ADDTIME('$ConStartDatim$',starttime),'%a') as day,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',starttime),'%l:%i %p') as "start time", LEFT(duration,5) AS length,
        roomname, trackname AS track, title, ifnull(Parts.participants, '') AS participants
    FROM
                Rooms R
           JOIN Schedule SCH USING (roomid)
           JOIN Sessions S USING (sessionid)
      LEFT JOIN Tracks T USING (trackid)
      LEFT JOIN Parts USING (sessionid)
    WHERE
        S.pubstatusid = 2
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
$report['output_filename'] = 'PDASchedule.csv';
$report['column_headings'] = 'day,start time,duration,room name,track,title,participants';
