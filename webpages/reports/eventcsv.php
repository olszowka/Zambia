<?php
// Copyright (c) 2017-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
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
WITH Parts AS
    (SELECT
            POS.sessionid, GROUP_CONCAT(P.pubsname SEPARATOR "\n") AS participants
        FROM
                 ParticipantOnSession POS
            JOIN Participants P USING (badgeid)
        GROUP BY
            POS.sessionid
    )
SELECT
        S.sessionid, S.title, DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS when2,
        R.roomname, Parts.participants
    FROM
                  Schedule SCH
             JOIN Sessions S USING (sessionid)
             JOIN Rooms R USING (roomid)
        LEFT JOIN Parts USING (sessionid)
    WHERE 
        S.divisionid = 3 /* Events */
    ORDER BY
        SCH.starttime
EOD;
$report['output_filename'] = 'event_csv_report.csv';
$report['column_headings'] = 'Session Id,Title,When,Room,Participants';
