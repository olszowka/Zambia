<?php
    require_once ('db_functions.php');
    require_once ('RenderViewSessions.php');
    require_once ('StaffRenderError.php');
    if ($session_started!=true) {
         session_start();
         }
    $_SESSION['return_to_page']='ViewAllSessions.php';
    $title="Query Session Results";
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        RenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
SELECT sessionid, trackname, title, concat( if(left(duration,2)=00, '', if(left(duration,1)=0, concat(right(left(duration,2),1),'hr '), concat(left(duration,2),'hr '))), if(date_format(duration,'%i')=00, '', if(left(date_format(duration,'%i'),1)=0, concat(right(date_format(duration,'%i'),1),'min'), concat(date_format(duration,'%i'),'min')))), estatten, statusname from Sessions, Tracks, SessionStatuses where Sessions.trackid=Tracks.trackid and Sessions.statusid=SessionStatuses.statusid and SessionStatuses.statusname not in ('A06scheduled')
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        RenderError($title,$message);
        exit ();
        }
    RenderViewSessions();
    exit();
?> 
