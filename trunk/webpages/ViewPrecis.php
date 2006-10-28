<?php
    require_once ('db_functions.php');
    require_once('StaffCommonCode.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once ('render_functions.php');
    $title=CON_NAME . " - Precis";
    $showlinks=$_GET["showlinks"];
    if ($showlinks=="1") {
            $showlinks=true;
            }
       else {
            $showlinks=false;
            }
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        StaffRenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
SELECT sessionid, trackname, title, 
       concat( if(left(duration,2)=00, '', 
               if(left(duration,1)=0, concat(right(left(duration,2),1),'hr '), concat(left(duration,2),'hr '))),
               if(date_format(duration,'%i')=00, '', 
               if(left(date_format(duration,'%i'),1)=0, concat(right(date_format(duration,'%i'),1),'min'), 
                  concat(date_format(duration,'%i'),'min')))) Duration,
       estatten, progguiddesc, persppartinfo
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
    staff_header($title);
    echo "<p> If you have any questions please contact ";
    echo "<a href=\"mailto:".BRAINSTORM_EMAIL."\">".BRAINSTORM_EMAIL."</a> </p>\n";
    RenderPrecis($result,$showlinks);
    staff_footer();
    exit();
?> 

