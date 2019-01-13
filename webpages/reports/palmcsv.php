<?php
// Copyright (c) 2009-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Palm Calendar';
$report['description'] = 'Export CSV file for Palm device calendars';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 60
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a') as 'Day',
        DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') as 'Start Time',
        left(duration,5) Length,
        Roomname,
        trackname as Track,
        Title,
        if(group_concat(pubsname) is NULL,'',group_concat(pubsname SEPARATOR ', ')) as 'Participants'
    FROM
                Rooms R
           JOIN Schedule SCH USING (roomid)
           JOIN Sessions S USING (sessionid)
      LEFT JOIN Tracks T USING (trackid)
      LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
      LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE
        S.pubstatusid = 2
    GROUP BY
        SCH.sessionid
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
$report['output_filename'] = 'PDASchedule.csv';
$report['column_headings'] = 'day,start time,duration,room name,track,title,participants';
