<?php
    require_once ('db_functions.php');
    require_once('BrainstormCommonCode.php');
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    $title="In Progress Suggestions";
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
   and SessionStatuses.statusname in ('Brainstorm','Vetted','Assigned','Scheduled')
   and Sessions.invitedguest=0
 order by trackname, title
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        RenderError($title,$message);
        exit ();
        }
    brainstorm_header($title);
    echo "<p> We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces; checking general feasability; finding needed people to present; looking for an appropiate time and location; rewritting for clarity and proper english; and hoping to find a time machine so we can do it all. ";
    echo "<p> If you want to help, email us at "; 
    echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> </p>\n";
    RenderPrecis($result,$showlinks);
    brainstorm_footer();
    exit();
?> 

