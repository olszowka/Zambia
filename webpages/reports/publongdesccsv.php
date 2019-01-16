<?php
// Copyright (c) 2009-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Long Description';
$report['description'] = 'Export CSV file of yet another full public schedule';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 90,
    'Publication Reports' => 40
);
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        S.sessionid,
        T.trackname,
        TY.typename,
        DV.divisionname,
        PS.pubstatusname,
        S.pubsno,
        group_concat(PC.pubcharname SEPARATOR ' ') pubcharacteristics,
        K.kidscatname,
        S.title,
        S.progguiddesc AS 'Description'
	FROM
                  Schedule SCH
             JOIN Sessions S USING(sessionid)
             JOIN Tracks T USING(trackid)
             JOIN Types TY USING(typeid)
             JOIN Divisions DV USING(divisionid)
             JOIN PubStatuses PS USING(pubstatusid)
             JOIN KidsCategories K USING(kidscatid)
        LEFT JOIN SessionHasPubChar SHPC USING(sessionid)
        LEFT JOIN PubCharacteristics PC USING(pubcharid)
    WHERE PS.pubstatusname = 'Public'
    GROUP BY scheduleid
EOD;
$report['output_filename'] = 'longdesc.csv';
$report['column_headings'] = 'sessionid,track,type,division,"publication status",pubsno,"publication characteristics","kids category",title,description';
