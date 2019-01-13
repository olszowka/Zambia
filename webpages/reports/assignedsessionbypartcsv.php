<?php
// Copyright (c) 2015-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Assigned Sessions';
$report['description'] = 'Export CSV file of all session assignments by participant';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 40
);
$report['csv_output'] = true;
$report['group_concat_expand'] = false;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
           P.badgeid, 
           P.pubsname, 
           IF ((moderator=1), 'Yes', ' ') AS 'Moderator',
           S.sessionid,
           S.title
    FROM
            Sessions S
       JOIN ParticipantOnSession POS USING (sessionid) 
       JOIN Participants P USING (badgeid)
    ORDER BY CAST(P.badgeid AS UNSIGNED);
EOD;
$report['output_filename'] = 'assignsessionbypart.csv';
$report['column_headings'] = 'badgeid,pubs name,moderator,sessionid,title';
