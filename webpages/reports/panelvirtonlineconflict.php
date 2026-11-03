<?php
// Copyright (c) 2017-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Virtual/Online Conflict Report';
$report['description'] = 'Export CSV file of sessions including participant attendance type (Assumes there is a survey question with a name of "AttendanceType")';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 42,
    'Programming Reports' => 45
);
$starttime = CON_START_DATIM;
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<EOD
SELECT
        S.sessionid, S.title, SS.statusname, TY.typename, RS.roomsetname, R.roomname,
        ADDTIME('$starttime', SCH.starttime) AS StartTime, P.badgeid, P.pubsname,
        JSON_VALUE(SPR.answers, '$.AttendanceType.value') AS AttendanceType
    FROM
             Sessions S
        JOIN SessionStatuses SS USING (statusid)
        JOIN Types TY USING (typeid)
        JOIN RoomSets RS USING (roomsetid)
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN PubStatuses PS USING (pubstatusid)
    LEFT OUTER JOIN ParticipantSurveyResponses SPR ON (SPR.badgeid = P.badgeid)
    WHERE
            IFNULL(SS.statusname, '') NOT IN ('Brainstorm', 'Dropped', 'Cancelled', 'Edit Me', 'Duplicate')
        AND IFNULL(PS.pubstatusname, 'Public') = 'Public'
    ORDER BY
        S.sessionid, P.badgeid
EOD;
$report['output_filename'] = 'sessionparttype.csv';
$report['column_headings'] = 'SessionID,Title,Status,Type,Roomset,Room,StartTime,badgeid,Pubsname,AttendanceType';
