<?php
    require_once ('db_functions.php');
    require_once ('RenderPrecis06.php');
    require_once ('StaffRenderError.php');
    $title=CON_NAME . " - Precis '06";
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        StaffRenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
SELECT sessionid, trackname, title, duration, estatten, progguiddesc, persppartinfo
  from Sessions, Tracks, SessionStatuses 
 where Sessions.trackid=Tracks.trackid  
   and SessionStatuses.statusid=Sessions.statusid  
   and SessionStatuses.statusname in ('Brainstorm','Vetted')
   and Sessions.invitedguest=0
 order by trackname, title
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        StaffRenderError($title,$message);
        exit ();
        }
    RenderPrecis06();
    exit();
?> 
