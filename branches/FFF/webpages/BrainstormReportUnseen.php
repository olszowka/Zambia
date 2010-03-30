<?php
    require_once ('db_functions.php');
    require_once('BrainstormCommonCode.php');
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    $title="New (Unseen) Suggestions";
    $showlinks=$_GET["showlinks"];
    $_SESSION['return_to_page']="ViewPrecis.php?showlinks=$showlinks";
    if ($showlinks=="1") {
            $showlinks=true;
            }
    elseif ($showlinks="0") {
            $showlinks=false;
            }
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        RenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
SELECT sessionid, trackname, null typename, title, 
       CASE
         WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
         WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
         ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
         END
         AS Duration,
       estatten, progguiddesc, persppartinfo
  from Sessions, Tracks, SessionStatuses
 where Sessions.trackid=Tracks.trackid  
   and SessionStatuses.statusid=Sessions.statusid
   and SessionStatuses.statusname in ('Brainstorm')
   and Sessions.invitedguest=0
 order by trackname, title
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        RenderError($title,$message);
        exit ();
        }
    brainstorm_header($title);
    echo "<p> If an idea is on this page, there is a good chance we have not yet seen it.   So, please wear your Peril Sensitive Sunglasses while reading. We do.";
    echo "This list is sorted by Track and then Title." ;
    RenderPrecis($result,$showlinks);
    brainstorm_footer();
    exit();
?> 

