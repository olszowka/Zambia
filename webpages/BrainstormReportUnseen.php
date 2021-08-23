<?php
//	Copyright (c) 2007-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('db_functions.php');
require_once('BrainstormCommonCode.php');
require_once('BrainstormHeader.php');
require_once('BrainstormFooter.php');
$title = "New (Unseen) Suggestions";
$query = <<<EOD
SELECT
        S.sessionid, TR.trackname, NULL typename, S.title, 
        CONCAT( IF(LEFT(S.duration,2)=00, '', 
                IF(LEFT(S.duration,1)=0, CONCAT(RIGHT(LEFT(S.duration,2),1),'hr '), CONCAT(LEFT(S.duration,2),'hr '))),
                IF(DATE_FORMAT(S.duration,'%i')=00, '', 
                IF(LEFT(DATE_FORMAT(S.duration,'%i'),1)=0, CONCAT(RIGHT(DATE_FORMAT(S.duration,'%i'),1),'min'), 
                CONCAT(DATE_FORMAT(S.duration,'%i'),'min')))) Duration,
        S.estatten, S.progguiddesc, S.persppartinfo, NULL starttime, NULL roomname, SS.statusname,
        NULL taglist, NULL notesforprog, NULL notesforpart, NULL servicenotes, NULL pubstatusname,
        concat('Made by: ', SEH.name, ' Email: ', SEH.email_address) sessionhistory
    FROM
             Sessions S
        JOIN Tracks TR USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
        JOIN SessionEditHistory SEH USING (sessionid)
    WHERE SS.statusid = 1  ##1=Brainstorm
    ORDER BY trackname, title;
EOD;
if (($result = mysqli_query_exit_on_error($query)) === false) {
    exit(); // Should have exited already.
}
brainstorm_header($title);
echo "<p> If an idea is on this page, there is a good chance we have not yet seen it.   So, please wear your Peril Sensitive Sunglasses while reading. We do.";
echo "This list is sorted by Track and then Title.";
RenderPrecis($result, false);
brainstorm_footer();
exit();
?> 

