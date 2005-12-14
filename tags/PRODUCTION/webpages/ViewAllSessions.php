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
SELECT sessionid, trackname, title, LEFT(duration,5), estatten, statusname
    from Sessions, Tracks, SessionStatuses where
    Sessions.trackid=Tracks.trackid and
    Sessions.statusid=SessionStatuses.statusid
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        RenderError($title,$message);
        exit ();
        }
    RenderViewSessions();
    exit();
?> 
