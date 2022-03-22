<?php
// Copyright (c) 2015-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant Schedule';
$report['description'] = 'Export CSV file of full participant schedule by participant, time';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 20,
    'GOH Reports' => 26,
    'Programming Reports' => 38
);
$report['csv_output'] = true;
$report['group_concat_expand'] = false;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT 
		IF ((P.pubsname IS NULL), ' ', CONCAT(' ',P.pubsname,' (',P.badgeid,')')) AS 'Participant', 
		IF ((moderator=1),'moderator', ' ') AS Moderator,
		DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') AS 'Start Time', 
		CONCAT(IF(LEFT(duration,2)=00, '', IF(LEFT(duration,1)=0, 
			CONCAT(RIGHT(LEFT(duration,2),1),'hr '), CONCAT(LEFT(duration,2),'hr '))), 
			IF(DATE_FORMAT(duration,'%i')=00, '', IF(LEFT(DATE_FORMAT(duration,'%i'),1)=0, 
			CONCAT(RIGHT(DATE_FORMAT(duration,'%i'),1),'min'), 
			CONCAT(DATE_FORMAT(DURATION,'%i'),'min')))) AS Duration,
		R.roomname,
		R.function, 
		trackname,
		S.sessionid,
		S.title
	FROM
				Sessions S
   		   JOIN Schedule SCH USING (sessionid)
		   JOIN Rooms R USING (roomid)
	  LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid 
	  LEFT JOIN Participants P ON POS.badgeid=P.badgeid 
	  LEFT JOIN Tracks T ON T.trackid=S.trackid 
	ORDER BY
		CAST(P.badgeid AS unsigned),
		SCH.starttime;
EOD;
$report['output_filename'] = 'allpartsched.csv';
$report['column_headings'] = 'Participant, Moderator, Start Time, Duration, Room, Function, Track, Session ID, Title';
