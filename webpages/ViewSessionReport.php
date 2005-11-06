<?php
    require_once ('db_functions.php');
    require_once ('RenderSessionReport.php');
    require_once ('RenderError.php');
    if ($session_started!=true) {
        session_start();
        }
    $_SESSION['return_to_page']='ViewSessionReport.php';
    $title="View Session Report";
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        RenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
SELECT sessionid, trackname, title, duration, estatten, pocketprogtext, persppartinfo
    from Sessions, Tracks where
    Sessions.trackid=Tracks.trackid 
    order by trackname, title
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        RenderError($title,$message);
        exit ();
        }
    RenderSessionReport();
    exit();
?> 
