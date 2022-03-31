<?php
// Copyright (c) 2017-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Event Organizer Packet Merge 2';
$report['description'] = 'Export CSV file of Event Organizer Packet Export 2';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 160,
    'Events Reports' => 40
);
$report['csv_output'] = true;
$report['group_concat_expand'] = false;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        POS.badgeid, P.pubsname, DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        CASE
			WHEN HOUR(S.duration) < 1 THEN CONCAT(DATE_FORMAT(S.duration,'%i'),'min')
			WHEN MINUTE(S.duration)=0 THEN CONCAT(DATE_FORMAT(S.duration,'%k'),'hr')
			ELSE CONCAT(DATE_FORMAT(S.duration,'%k'),'hr ',DATE_FORMAT(S.duration,'%i'),'min')
			END
			AS duration,
        R.roomname, S.title, IF(POS.moderator=1,'(M)','') AS moderator, CD.firstname, CD.lastname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid)
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
    WHERE EXISTS (
        SELECT * FROM UserHasPermissionRole UHPR
            WHERE 
                    UHPR.badgeid = P.badgeid
                AND UHPR.permroleid = 6 /* Event Organizer */
        )
    ORDER BY
        P.pubsname;
EOD;
$report['output_filename'] = 'event_organizer_packet_merge2.csv';
$report['column_headings'] = 'badgeid, pubs name, start time, duration, room name, title, moderator, first name, last name';
