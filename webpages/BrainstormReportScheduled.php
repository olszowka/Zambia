<?php
// Copyright (c) 2005-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message_error, $title;
require_once('BrainstormCommonCode.php');
$title = "Scheduled Suggestions";
$ConStartDatim = CON_START_DATIM;
$query = <<<EOD
WITH SessionTags AS (
    SELECT
            SHT.sessionid, GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist
        FROM
                      SessionHasTag SHT
            LEFT JOIN Tags TA USING (tagid)
        GROUP BY
            SHT.sessionid
    )
SELECT
        S.sessionid, T.trackname, NULL typename, S.title, 
        concat( if(left(S.duration,2)=00, '', 
                if(left(S.duration,1)=0, concat(right(left(S.duration,2),1),'hr '), concat(left(S.duration,2),'hr '))),
                if(date_format(S.duration,'%i')=00, '', 
                if(left(date_format(S.duration,'%i'),1)=0, concat(right(date_format(S.duration,'%i'),1),'min'), 
                concat(date_format(S.duration,'%i'),'min')))) Duration,
        S.estatten, S.progguiddesc, S.persppartinfo, 
        DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime,
        R.roomname, SS.statusname, SessionTags.taglist
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
        LEFT JOIN SessionTags USING (sessionid)
    ORDER BY
        T.trackname, S.title;
EOD;
brainstorm_header($title);
$result = mysqli_query_exit_on_error($query);
echo "<p> These ideas are highly likely to make it into the final schedule. Things are looking good for them.  Please remember events out of our control and last minute emergencies cause this to change!  No promises, but we are doing our best to have this happen. ";
echo "<p> If you want to help, email us at ";
echo "<a href=\"mailto:" . PROGRAM_EMAIL . "\">" . PROGRAM_EMAIL . "</a> </p>\n";
echo "This list is sorted by Track and then Title.";
RenderPrecis($result, false);
brainstorm_footer();
exit();
?> 
