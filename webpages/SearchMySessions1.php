<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Show Search Session Results";
    require_once('ParticipantFooter.php');
    require ('db_functions.php'); //define database functions
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session

    $trackid=$_POST["track"];
    $titlesearch=stripslashes($_POST["title"]);
    $query = <<<EOD
Select S2.sessionid, S2.trackname, S2.title, S2.duration, S2.progguiddesc, S2.persppartinfo, PSI.badgeid from ( Select S.sessionid, T.trackname, S.title, concat( if(left(duration,2)=00, '', if(left(duration,1)=0, concat(right(left(duration,2),1),'hr '), concat(left(duration,2),'hr '))), if(date_format(duration,'%i')=00, '', if(left(date_format(duration,'%i'),1)=0, concat(right(date_format(duration,'%i'),1),'min'), concat(date_format(duration,'%i'),'min')))) as duration, S.progguiddesc, S.persppartinfo from Sessions S, Tracks AS T where S.trackid = T.trackid and T.selfselect=1 and S.invitedguest=0 and (S.statusid=2 or S.statusid=7 or S.statusid=3)
EOD;
    if ($trackid!=0) {
        $query.=" and S.trackid=".$trackid;
        }
    if ($titlesearch!="") {
        $query.=" AND title LIKE \"%".mysql_real_escape_string($titlesearch,$link)."%\" ";
        }
    $query.= ") as S2 left join ( select * from ParticipantSessionInterest where badgeid=\"";
    $query.=$badgeid."\") as PSI on S2.sessionid=PSI.sessionid order by S2.trackname, S2.sessionid";
    if (!$result=mysql_query($query,$link)) {
        $message=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    participant_header($title);
    //echo $query."<BR>\n";
    require ('RenderMySessions1.php');    
    RenderMySessions1($result);
    participant_footer();
    exit();
?>
