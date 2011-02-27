<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="My General Interests";
    require ('PartCommonCode.php'); // initialize db; check login;
    require_once('ParticipantHeader.php');
    require_once('renderMyInterests.php');
    // set $badgeid from session
    $result=mysql_query("SELECT * FROM ParticipantInterests where badgeid=\"".$badgeid."\"",$link);
    if (!$result) {
        $message2=mysql_error($link);
        $message=$query."<BR>".$message2."<BR>Error querying database. Unable to continue.<BR>";
        RenderError($title,$message);
        exit();
        }
    $rows=mysql_num_rows($result);
    if ($rows>1) {
        $message=$query."<br>Multiple rows returned from database where one expected. Unable to continue.";
        RenderError($title,$message);
        exit();
        }
    if ($rows==0) {
            $yespanels="";
            $nopanels=""; 
            $yespeople="";
            $nopeople="";
            $otherroles="";
            $newrow=true;
            }
        else {
            list($foo,$yespanels,$nopanels,$yespeople,$nopeople, $otherroles)=mysql_fetch_array($result, MYSQL_NUM);
            $newrow=false;
            }
    $query="Select PHA.badgeid, PA.activityid, PA.activityname from ParticipantActivities as PA left join (Select badgeid, ";
    $query.="activityid from ParticipantHasActivity where badgeid=\"".$badgeid."\") as PHA ";
    $query.="on PA.activityid=PHA.activityid order by PA.display_order";
    $result=mysql_query($query,$link);
    if (!$result) {
        $message2=mysql_error($link);
        $message=$query."<BR>".message2."<BR>Error querying database. Unable to continue.<BR>";
        RenderError($title,$message);
        exit();
        }
    $rolerows=mysql_num_rows($result);
    for ($i=0; $i<$rolerows; $i++) {
        list($rolearray[$i]["badgeid"],$rolearray[$i]["activityid"],$rolearray[$i]["activityname"])=mysql_fetch_array($result, MYSQL_NUM);
        }
    //print_r($rolearray);
    //exit(0);
    $error=false;
    $message="";
    renderMyInterests($title, $error, $message);
    participant_footer();
?>
