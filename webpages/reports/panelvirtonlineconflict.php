<?php
// Copyright (c) 2017-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Virtual/Online Conflict Report';
$report['description'] = 'Export CSV file of sessions including participant attendance type';
$report['categories'] = array(
    'Reports downloadable as CSVs' => 42,
    'Programming Reports' => 45
);
$starttime = CON_START_DATIM;
$report['csv_output'] = true;
$report['group_concat_expand'] = true;
$report['queries'] = [];
$report['queries']['master'] =<<<EOD
SELECT s.sessionid, s.title, ss.statusname, t.typename, 
rs.roomsetname, r.roomname, ADDTIME('$starttime', c.starttime) AS StartTime, 
p.badgeid, p.pubsname, sa.value
FROM Sessions s
LEFT OUTER JOIN SessionStatuses ss ON (s.statusid = ss.statusid)
LEFT OUTER JOIN Types t on (s.typeid = t.typeid)
LEFT OUTER JOIN RoomSets rs ON (rs.roomsetid = s.roomsetid)
LEFT OUTER JOIN Schedule c ON (c.sessionid = s.sessionid)
LEFT OUTER JOIN Rooms r ON (r.roomid = c.roomid)
LEFT OUTER JOIN ParticipantOnSession ps ON (ps.sessionid = s.sessionid)
LEFT OUTER JOIN Participants p ON (p.badgeid = ps.badgeid)
LEFT OUTER JOIN ParticipantSurveyAnswers sa ON (sa.participantid = p.badgeid AND sa.questionid = 10)
LEFT OUTER JOIN SessionStatuses st ON (st.statusid = s.statusid)
LEFT OUTER JOIN PubStatuses pub ON (s.pubstatusid = pub.pubstatusid)
WHERE IFNULL(st.statusname, '') NOT IN ('Brainstorm', 'Dropped', 'Cancelled', 'Edit Me', 'Duplicate')
AND IFNULL(pub.pubstatusname, 'Public') = 'Public'
ORDER BY
        s.sessionid, p.badgeid
EOD;
$report['output_filename'] = 'sessionparttype.csv';
$report['column_headings'] = 'SessionID,Title,Status,Type,Roomset,Room,StartTime,badgeid,Pubsname,AttentanceType';
