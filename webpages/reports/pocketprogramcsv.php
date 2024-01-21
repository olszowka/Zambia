<?php
// Copyright (c) 2009-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Pocket Program';
$report['description'] = 'Export CSV file of public schedule for generating pocket program';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 80,
    'Publication Reports' => 40
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        S.sessionid, 
        DATE_FORMAT(ADDTIME('$ConStartDatim$',starttime),'%a') AS Day, 
        DATE_FORMAT(ADDTIME('$ConStartDatim$',starttime),'%l:%i %p') AS 'Time', 
        concat(if(left(duration,2)=00, '', 
                  if(left(duration,1)=0, 
                     concat(right(left(duration,2),1), 'hr '),
                   concat(left(duration,2),'hr '))), 
               if(date_format(duration,'%i')=00, '', 
                  if(left(date_format(duration,'%i'),1)=0, 
                     concat(right(date_format(duration,'%i'),1),'min'), 
                     concat(date_format(duration,'%i'),'min')))) AS Duration, 
        roomname, trackname AS TRACK,
        CASE typeid
            WHEN 8 THEN '' /* Autographing */
            WHEN 1 THEN '' /* Kaffeeklatsch */
            WHEN 7 THEN '' /* Reading */
            ELSE typename END AS TYPE,
        K.kidscatname, title, progguiddesc AS 'Long Text',
        SUBQ.participants AS 'PARTIC'
    FROM
                  Sessions S
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
             JOIN Tracks T USING (trackid)
             JOIN Types Ty USING (typeid)
             JOIN KidsCategories K USING (kidscatid)
        LEFT JOIN (SELECT
                         SCH2.sessionid, group_concat(' ',P.pubsname, if (POS.moderator=1,' (m)','')) AS 'participants'
                     FROM
                                   Schedule SCH2
                         LEFT JOIN ParticipantOnSession POS USING (sessionid) 
                         LEFT JOIN Participants P USING (badgeid)
                    GROUP BY
                        SCH2.sessionid
                ) AS SUBQ USING (sessionid)
    WHERE 
        S.pubstatusid = 2
    ORDER BY 
        SCH.starttime, 
        R.roomname;
EOD;
$report['output_filename'] = 'pocketprogram.csv';
$report['column_headings'] = 'sessionid,day,time,duration,room,track,type,"kids category",title,description,participants';
$report['map_functions'][8] = function($inp) : string {
    return(trim($inp));
};
$report['map_functions'][9] = function($inp) : string {
    while (mb_ereg("[\n\r\x{00a0}]", $inp)) {
        $inp = mb_ereg_replace("[\n\r\x{00a0}]", " ", $inp);
    }
    while (mb_ereg("  +", $inp)) {
        $inp = mb_ereg_replace("  +", " ", $inp);
    }
    return(trim($inp));
};
