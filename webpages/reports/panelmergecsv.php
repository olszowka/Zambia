<?php
// Copyright (c) 2009-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Panel Merge';
$report['description'] = 'Export CSV file of entire schedule (including unpublished) for mailmerge';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 70
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        S.sessionid, 
        R.roomname, 
        DATE_FORMAT(ADDTIME('2010-01-15 00:00:00',SCH.starttime),'%a %l:%i %p') starttime, 
        CASE
            WHEN HOUR(S.duration) < 1 THEN concat(date_format(S.duration,'%i'),'min')
            WHEN MINUTE(S.duration)=0 THEN concat(date_format(S.duration,'%k'),'hr')
            ELSE concat(date_format(S.duration,'%k'),'hr ',date_format(S.duration,'%i'),'min')
            END
            AS duration,
        T.trackname, 
        S.title, 
        group_concat(P.pubsname, if(POS.moderator=1,'(m)','') ORDER BY POS.moderator DESC SEPARATOR ', ') panelinfo,
        PUB.pubstatusname
    FROM
                Sessions S
           JOIN Schedule SCH USING(sessionid)
           JOIN Rooms R USING(roomid)
           JOIN Tracks T USING(trackid)
           JOIN PubStatuses PUB USING(pubstatusid)
      LEFT JOIN ParticipantOnSession POS USING(sessionid)
      LEFT JOIN Participants P USING(badgeid)
   GROUP BY
        S.sessionid
   ORDER BY
        SCH.starttime
EOD;
$report['output_filename'] = 'panelmerge.csv';
$report['column_headings'] = 'sessionid,room,"start time",duration,track,title,participants';
