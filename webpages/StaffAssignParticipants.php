<?php
$title="Assign Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
staff_header($title);

SELECT POS.badgeid, POS.moderator, CD.badgeid, CD.firstname, CD.lastname, PSI.rank,
PSI.willmoderate, PSI.comments FROM CongoDump AS CD JOIN ParticipantSessionInterest AS PSI
ON CD.badgeid=PSI.badgeid LEFT JOIN ParticipantOnSession AS POS ON (CD.badgeid=POS.badgeid
and PSI.sessionid=POS.sessionid) where PSI.sessionid=$sessionid

SELECT POS.badgeid, POS.moderator, CD.badgeid, CD.firstname, CD.lastname, PSI.rank,
PSI.willmoderate, PSI.comments FROM CongoDump AS CD JOIN ParticipantSessionInterest AS PSI
ON CD.badgeid=PSI.badgeid LEFT JOIN ParticipantOnSession AS POS ON (CD.badgeid=POS.badgeid
and PSI.sessionid=POS.sessionid) where PSI.sessionid=36


