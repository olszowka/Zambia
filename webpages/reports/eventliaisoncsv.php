<?php
// Copyright (c) 2017-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Event Liaison Export';
$report['description'] = 'Export CSV file of Event Liaison info';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 150
);
$report['csv_output'] = true;
$report['group_concat_expand'] = false;
$report['queries'] = [];
$report['queries']['master'] =<<<'EOD'
SELECT
        S.sessionid, DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a') AS 'day',
        DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%l:%i %p') AS 'starttime',
        left(S.duration, 5) AS duration, R.roomname, S.title, P.pubsname,
        IF(ISNULL(EO.badgeid), "", "Event Organizer") AS eventorganizer,
        IF(ISNULL(EO.badgeid), "", CD.phone) AS phone,
        IF(ISNULL(EO.badgeid), "", CD.email) AS email
    FROM
                Schedule SCH
           JOIN Rooms R USING (roomid)
           JOIN Sessions S USING (sessionid)
      LEFT JOIN ParticipantOnSession POS USING (sessionid)
      LEFT JOIN Participants P USING (badgeid)
      LEFT JOIN CongoDump CD USING (badgeid)
      LEFT JOIN (SELECT
                  badgeid
              FROM
                  UserHasPermissionRole
              WHERE
                  permroleid = 6 /* Event Organizer */
          ) AS EO USING (badgeid)
    WHERE
            S.divisionid = 3 /* Events */
        AND EXISTS (SELECT *
            FROM
                UserHasPermissionRole UHPR
            WHERE
                    UHPR.badgeid = P.badgeid
                AND (   UHPR.permroleid = 5 /* Event Participant */
                     OR UHPR.permroleid = 6 /* Event Organizer */
                    )
            )
    ORDER BY
            SCH.starttime, S.sessionid, IF(ISNULL(EO.badgeid), 2, 1);
EOD;
$report['output_filename'] = 'EventLiaison.csv';
$report['column_headings'] = 'sessionid,day,start time,duration,room name,title,participant name,event organizer,phone,email';
