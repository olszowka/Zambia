<?php
//	Copyright (c) 2006-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('BrainstormCommonCode.php');
$ConStartDatim = CON_START_DATIM;
$title = "All Suggestions";
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
        R.roomname, SS.statusname, GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist,
        NULL notesforprog, NULL notesforpart, NULL servicenotes, NULL pubstatusname,
        concat('Made by: ', SEH.name, ' Email: ', SEH.email_address) sessionhistory
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
             JOIN SessionEditHistory SEH USING (sessionid)
             JOIN Schedule SCH USING (sessionid)
             JOIN Rooms R USING (roomid)
        LEFT JOIN SessionHasTag SHT USING (sessionid)
        LEFT JOIN Tags TA USING (tagid)
    WHERE
            SS.statusid IN (6, 1, 2, 7, 3)  ##6=Edit Me, 1=Brainstorm, 2=Vetted, 7=Assigned, 3=Scheduled
            AND SEH.sessioneditcode IN (1, 2)   # 1-Created in brainstorm , 2-Created in staff create session
    GROUP BY
        S.sessionid
    ORDER BY
        T.trackname, S.title;
EOD;
if (($result = mysqli_query_exit_on_error($query)) === false) {
    exit(); // Should have exited already
}
brainstorm_header($title);
echo "<p> This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</p>";
echo "<p> We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces; checking general feasability; finding needed people to present; looking for an appropiate time and location; rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</p>";
echo "<p> If you want to help, email us at ";
echo "<a href=\"mailto:" . PROGRAM_EMAIL . "\">" . PROGRAM_EMAIL . "</a> </p>\n";
echo "This list is sorted by Track and then Title.";
RenderPrecis($result, false);
brainstorm_footer();
exit();
?> 

