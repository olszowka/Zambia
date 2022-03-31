<?php
// Copyright (c) 2017-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Events CSV Export';
$report['description'] = 'Export CSV file of Event Division sessions';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 140,
    'Events Reports' => 39
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        S.sessionid,
        S.title,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS when2,
        R.roomname,
        GROUP_CONCAT(
            P.pubsname
            SEPARATOR "\n"
            ) AS participants
    FROM
                  Schedule SCH
        LEFT JOIN ParticipantOnSession POS USING (sessionid)
             JOIN Sessions S USING (sessionid)
             JOIN Rooms R USING (roomid)
        LEFT JOIN Participants P USING (badgeid)
    WHERE 
        S.divisionid = 3 /* Events */
    GROUP BY
        S.sessionid
    ORDER BY
        SCH.starttime
EOD;
$report['output_filename'] = 'event_csv_report.csv';
$report['column_headings'] = 'Session Id,Title,When,Room,Participants';
