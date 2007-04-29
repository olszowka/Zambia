<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="Select Interested Sessions";
    require ('db_functions.php'); //define database functions
    require_once('ParticipantFooter.php');
    require_once('renderMySessions2.php');
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    $maxrow=$_POST["maxrow"];
    $delcount=0;
    $dellist="";
    for ($i=0;$i<=$maxrow;$i++) {
        if (($_POST["checked".$i]==1)&&(!isset($_POST["int".$i]))) {
            $dellist.=(($delcount==0)?"":",").$_POST["sessionid".$i];
            $delcount++;
            }
        }
    if ($delcount>0) {
        $query="DELETE FROM ParticipantSessionInterest WHERE badgeid=\"".$badgeid."\" and sessionid in (";
        $query.=$dellist.")";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.  Database not updated.";
            RenderError($title,$message);
            exit();
            }
        }
    $inscount=0;
    for ($i=0;$i<=$maxrow;$i++) {
        if (($_POST["checked".$i]==0)&&(isset($_POST["int".$i]))) {
            $query="INSERT INTO ParticipantSessionInterest set badgeid=".$badgeid.", sessionid=".$_POST["sessionid".$i];
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error updating database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            $inscount++;
            }
        }
    $message=""; 
    $error=false;
    if (($delcount==0)&&($inscount==0)) {
        $message="No changes to database requested.";
        }
    if ($delcount>0) {
        $message=$delcount." session(s) removed from interest list.<BR>";
        }
    if ($inscount>0) {
        $message.=$inscount." session(s) added to interest list.";
        }
    renderMySessions2($title, $error, $message, $badgeid);
    participant_footer();
    exit(0);
?>        
