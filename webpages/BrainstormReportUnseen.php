<?php
//	Copyright (c) 2007-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('db_functions.php');
require_once('BrainstormCommonCode.php');
require_once('BrainstormHeader.php');
require_once('BrainstormFooter.php');
$title = "New (Unseen) Suggestions";
$showlinks = $_GET["showlinks"];
$_SESSION['return_to_page'] = "ViewPrecis.php?showlinks=$showlinks";
if ($showlinks == "1") {
    $showlinks = true;
} elseif ($showlinks = "0") {
    $showlinks = false;
}
if (prepare_db() === false) {
    $message = "Error connecting to database.";
    RenderError($message);
    exit ();
}
$query = <<<EOD
SELECT sessionid, trackname, NULL typename, title, 
       concat( if(left(duration,2)=00, '', 
               if(left(duration,1)=0, concat(right(left(duration,2),1),'hr '), concat(left(duration,2),'hr '))),
               if(date_format(duration,'%i')=00, '', 
               if(left(date_format(duration,'%i'),1)=0, concat(right(date_format(duration,'%i'),1),'min'), 
                  concat(date_format(duration,'%i'),'min')))) Duration,
       estatten, progguiddesc, persppartinfo
  FROM Sessions, Tracks, SessionStatuses
 WHERE Sessions.trackid=Tracks.trackid  
   AND SessionStatuses.statusid=Sessions.statusid
   AND SessionStatuses.statusname IN ('Brainstorm')
   AND Sessions.invitedguest=0
 ORDER BY trackname, title
EOD;
if (($result = mysqli_query_exit_on_error($query)) === false) {
    exit(); // Should have exited already.
}
brainstorm_header($title);
echo "<p> If an idea is on this page, there is a good chance we have not yet seen it.   So, please wear your Peril Sensitive Sunglasses while reading. We do.";
echo "This list is sorted by Track and then Title.";
RenderPrecis($result, $showlinks);
brainstorm_footer();
exit();
?> 

