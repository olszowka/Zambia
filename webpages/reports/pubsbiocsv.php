<?php
// Copyright (c) 2009-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Biographies';
$report['description'] = 'Export CSV file of scheduled participants and their biographies';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 100,
    'Publication Reports' => 40
);
$report['csv_output'] = true;
$report['group_concat_expand'] = false;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        P.badgeid, CD.lastname, CD.firstname,
	    CD.badgename, P.pubsname, P.bio 
	FROM
	    Participants P JOIN
	    CongoDump CD USING (badgeid) JOIN
	    (SELECT DISTINCT badgeid 
	       FROM ParticipantOnSession POS JOIN 
	            Schedule SCH USING (sessionid)
	     ) AS X
	   USING (badgeid) 
	ORDER BY
	    IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;
$report['output_filename'] = 'PubBio.csv';
$report['column_headings'] = 'badgeid,lastname,firstname,badgename,pubsname,bio';
