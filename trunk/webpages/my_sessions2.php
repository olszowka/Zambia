<?php
    global $participant,$message,$message_error,$message2,$congoinfo;
    $title="My Panel Interests";
    $error=false;
    require ('PartCommonCode.php'); // initialize db; check login;
    require_once ('ParticipantHeader.php'); // initialize db; check login;
    require_once('renderMySessions2.php');
    if (!may_I('my_panel_interests')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError("Error occurred",$message_error);
        exit();
        }
    // set $badgeid from session
    if (isset($_POST["add"])) {  //  This page reached with "Add" Button
      $message="";
      $sessionid=$_POST["sessionid"];
      if (!is_numeric($sessionid)) {
        $message="For Session ID, please enter an integer.<BR>";
        $error=true;
        }
      elseif (($sessionid=intval($sessionid))<0) {
        $message="For Session ID, please enter a positive integer.<BR>";
        $error=true;
        }
      else {    
        $query="SELECT sessionid FROM (SELECT sessionid, trackid FROM Sessions where sessionid=";
        $query.=$sessionid." and invitedguest=0 and statusid in (2,3,7)) AS S join ";
        $query.="(SELECT trackid FROM Tracks where selfselect=1) AS T on S.trackid = T.trackid";
        if (!$result=mysql_query($query,$link)) {
          $message=$query."<BR>Error querying database.<BR>";
          RenderError($title,$message);
          exit();
         }
         if (mysql_num_rows($result)==0) {
           $message.="That Session ID is not valid or is not eligible for sign up.<BR>";
           $error=true;
         }
         $query="SELECT sessionid FROM ParticipantSessionInterest where sessionid=";
         $query.=$sessionid." and badgeid=\"".$badgeid."\"";
         if (!$result=mysql_query($query,$link)) {
           $message=$query."<BR>Error querying database.<BR>";
           RenderError($title,$message);
           exit();
         }
         if (mysql_num_rows($result)!=0) {
           $message.="Please do not enter a Session ID for which you have already signed up.<BR>";
           $error=true;
         }
       }
       if (!$error) {

         $query="INSERT INTO ParticipantSessionInterest set badgeid=\"".$badgeid."\", sessionid=".$sessionid;

         if (!$result=mysql_query($query,$link)) {
           $message=$query."<BR>Error inserting into database.<BR>";
           RenderError($title,$message);
           exit();

         }
       }
     }
    renderMySessions2($title, $error, $message, $badgeid);
    participant_footer();
?>
