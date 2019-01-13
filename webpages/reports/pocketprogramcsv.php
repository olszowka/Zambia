<?php
// Copyright (c) 2009-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Pocket Program';
$report['description'] = 'Export CSV file of public schedule for generating pocket program';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 80
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        S.sessionid, 
        DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a') as Day, 
        DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') as 'Time', 
        concat(if(left(duration,2)=00, '', 
                  if(left(duration,1)=0, 
                     concat(right(left(duration,2),1), 'hr '),
                   concat(left(duration,2),'hr '))), 
               if(date_format(duration,'%i')=00, '', 
                  if(left(date_format(duration,'%i'),1)=0, 
                     concat(right(date_format(duration,'%i'),1),'min'), 
                     concat(date_format(duration,'%i'),'min')))) Duration, 
        roomname, 
        trackname as TRACK, 
        typename as TYPE,
        K.kidscatname,
        title, 
        progguiddesc as 'Long Text', 
        group_concat(' ',pubsname, if (moderator=1,' (m)','')) as 'PARTIC' 
    FROM
                Sessions S
           JOIN Schedule SCH USING (sessionid)
           JOIN Rooms R USING (roomid)
           JOIN Tracks T USING (trackid)
           JOIN Types Ty USING (typeid)
           JOIN KidsCategories K USING (kidscatid)
      LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
      LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE 
        S.pubstatusid = 2
    GROUP BY
        SCH.sessionid
    ORDER BY 
        SCH.starttime, 
        R.roomname;
EOD;
$report['output_filename'] = 'pocketprogram.csv';
$report['column_headings'] = 'sessionid,day,time,duration,room,track,type,"kids category",title,description,participants';
