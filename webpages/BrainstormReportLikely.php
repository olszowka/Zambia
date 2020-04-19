<?php
// Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title;
require_once('BrainstormCommonCode.php');
$title = "Likely to Occur Suggestions";
$ConStartDatim = CON_START_DATIM;
$query = <<<EOD
SELECT
        S.sessionid, T.trackname, NULL typename, S.title, 
        concat( if(left(S.duration,2)=00, '', 
                if(left(S.duration,1)=0, concat(right(left(S.duration,2),1),'hr '), concat(left(S.duration,2),'hr '))),
                if(date_format(S.duration,'%i')=00, '', 
                if(left(date_format(S.duration,'%i'),1)=0, concat(right(date_format(S.duration,'%i'),1),'min'), 
                concat(date_format(S.duration,'%i'),'min')))) Duration,
        S.estatten, S.progguiddesc, S.persppartinfo, 
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime,
		R.roomname, SS.statusname, GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
		LEFT JOIN Schedule SCH USING (sessionid)
		LEFT JOIN Rooms R USING (roomid)
		LEFT JOIN SessionHasTag SHT USING (sessionid)
		LEFT JOIN Tags TA USING (tagid)
    WHERE
        SS.statusname IN ('Vetted', 'Assigned', 'Scheduled')
    GROUP BY
        S.sessionid
    ORDER BY
        T.trackname, S.title;
EOD;
brainstorm_header($title);
$result = mysqli_query_exit_on_error($query);
echo "<p> These ideas have made the first cut.  We like them and would like to see them happen.   Now to just find all the right people... ";
echo "<p> If you want to help, email us at ";
echo "<a href=\"mailto:" . PROGRAM_EMAIL . "\">" . PROGRAM_EMAIL . "</a> </p>\n";
echo "This list is sorted by Track and then Title.";
RenderPrecis($result, false);
brainstorm_footer();
exit();
?> 

