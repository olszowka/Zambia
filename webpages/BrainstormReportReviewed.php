<?php
//	Copyright (c) 2007-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('BrainstormCommonCode.php');
$ConStartDatim = CON_START_DATIM;
$title = "Reviewed Suggestions";
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
        SS.statusname IN ('Edit Me', 'Vetted', 'Assigned', 'Scheduled')
    GROUP BY
        S.sessionid
    ORDER BY
        T.trackname, S.title;
EOD;
if (($result = mysqli_query_exit_on_error($query)) === false) {
    exit(); // Should have exited already
}
brainstorm_header($title);
echo "<p> We've seen these.   They have varying degrees of merit.  We have or will sort through these suggestions: combining duplicates; splitting big ones into pieces; checking general feasability; finding needed people to present; looking for an appropiate time and location; rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</p>";
echo "<p> Note that ideas that we like and are pursuing further will stay on this list.  That is to make it easier to find the idea you suggested.</p>";
echo "<p> If you want to help, email us at ";
echo "<a href=\"mailto:" . PROGRAM_EMAIL . "\">" . PROGRAM_EMAIL . "</a> </p>\n";
echo "This list is sorted by Track and then Title.";
RenderPrecis($result, false);
brainstorm_footer();
exit();
?> 

