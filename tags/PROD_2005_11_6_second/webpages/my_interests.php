<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="My General Interests";
    require ('db_functions.php'); //define database functions
    require_once('ParticipantFooter.php');
    require_once('renderMyInterests.php');
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
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
    $query="Select PHR.badgeid, R.roleid, R.rolename from Roles as R left join (Select badgeid, ";
    $query.="roleid from ParticipantHasRole where badgeid=\"".$badgeid."\") as PHR ";
    $query.="on R.roleid=PHR.roleid order by R.display_order";
    $result=mysql_query($query,$link);
    if (!$result) {
        $message2=mysql_error($link);
        $message=$query."<BR>".message2."<BR>Error querying database. Unable to continue.<BR>";
        RenderError($title,$message);
        exit();
        }
    $rolerows=mysql_num_rows($result);
    for ($i=0; $i<$rolerows; $i++) {
        list($rolearray[$i]["badgeid"],$rolearray[$i]["roleid"],$rolearray[$i]["rolename"])=mysql_fetch_array($result, MYSQL_NUM);
        }
    //print_r($rolearray);
    //exit(0);
    $error=false;
    $message="";
    renderMyInterests($title, $error, $message);
    participant_footer();
?>
