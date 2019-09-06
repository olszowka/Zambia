<?php
//	Copyright (c) 2007-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('db_functions.php');
require_once('BrainstormCommonCode.php');
require_once('BrainstormHeader.php');
require_once('BrainstormFooter.php');
$title = "New (Unseen) Suggestions";
$query = <<<EOD
SELECT
        S.sessionid, TR.trackname, NULL typename, S.title, 
        concat( if(left(S.duration,2)=00, '', 
                if(left(S.duration,1)=0, concat(right(left(S.duration,2),1),'hr '), concat(left(S.duration,2),'hr '))),
                if(date_format(S.duration,'%i')=00, '', 
                if(left(date_format(S.duration,'%i'),1)=0, concat(right(date_format(S.duration,'%i'),1),'min'), 
                concat(date_format(S.duration,'%i'),'min')))) as Duration,
        S.estatten, S.progguiddesc, S.persppartinfo, null as roomname, null as starttime, SS.statusname
    FROM
             Sessions S
        JOIN Tracks TR USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
    WHERE SS.statusname = 'Brainstorm'
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

