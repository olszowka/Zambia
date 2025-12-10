<?php
// Copyright (c) 2025 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka on 2025-01-18
$report = [];
$report['name'] = 'Pocket Program 2';
$report['description'] = 'Export CSV file of public schedule for generating pocket program with tags instead of tracks';
$report['categories'] = array(
    'Boskone Central' => 620,
    'Reports downloadable as CSVs' => 79,
    'Publication Reports' => 45
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
WITH ST AS (
    SELECT
            S.sessionid, GROUP_CONCAT(TA.tagname SEPARATOR ", ") AS "taglist"
        FROM
                      Schedule SCH
                 JOIN Sessions S USING (sessionid)
            LEFT JOIN SessionHasTag USING (sessionid)
            LEFT JOIN Tags TA USING (tagid)
        GROUP BY
            S.sessionid
),
SP AS (
    SELECT
             SCH.sessionid, GROUP_CONCAT(' ',P.pubsname, if (POS.moderator=1,' (m)','')) AS "partlist"
         FROM
                       Schedule SCH
             LEFT JOIN ParticipantOnSession POS USING (sessionid) 
             LEFT JOIN Participants P USING (badgeid)
        GROUP BY
            SCH.sessionid
)
SELECT
        S.sessionid, 
        DATE_FORMAT(ADDTIME('$ConStartDatim$', SCH.starttime),'%a') AS Day, 
        DATE_FORMAT(ADDTIME('$ConStartDatim$', SCH.starttime),'%l:%i %p') AS 'Time', 
        concat(if(left(S.duration,2)=00, '', 
                  if(left(S.duration,1)=0, 
                     concat(right(left(S.duration,2),1), 'hr '),
                   concat(left(S.duration,2),'hr '))), 
               if(date_format(S.duration,'%i')=00, '', 
                  if(left(date_format(S.duration,'%i'),1)=0, 
                     concat(right(date_format(S.duration,'%i'),1),'min'), 
                     concat(date_format(S.duration,'%i'),'min')))) AS Duration, 
        R.roomname, ST.taglist AS TAGS, TY.typename AS TYPE, S.title,
        S.progguiddesc AS "Long Text",
        SP.partlist AS "PARTIC"
    FROM
                  Sessions S
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
             JOIN Tracks T USING (trackid)
             JOIN Types TY USING (typeid)
        LEFT JOIN ST USING (sessionid)
        LEFT JOIN SP USING (sessionid)
    WHERE 
        S.pubstatusid = 2 /* Public */
    ORDER BY 
        SCH.starttime, 
        R.roomname;
EOD;
$report['output_filename'] = 'pocketprogram.csv';
$report['column_headings'] = 'sessionid,day,time,duration,room,tags,type,title,description,participants';
$report['map_functions'][7] = function($inp) : string {
    return(trim($inp));
};
$report['map_functions'][8] = function($inp) : string {
    while (mb_ereg("[\n\r\x{00a0}]", $inp)) {
        $inp = mb_ereg_replace("[\n\r\x{00a0} ]+", " ", $inp);
    }
    while (mb_ereg("  +", $inp)) {
        $inp = mb_ereg_replace("  +", " ", $inp);
    }
    return(trim($inp));
};
