<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="My Panel Interests";
    require ('db_functions.php'); //define database functions
    require_once('ParticipantFooter.php');
    require_once('renderMySessions2.php');
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
    // set $badgeid from session
    if (isset($_POST["add"])) {  //  This page reached with "Add" Button
      $error=false;
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
        $query.=$sessionid." and invitedguest=0 and statusid=2) AS S join ";
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
         $query="INSERT INTO ParticipantSessionInterest VALUES(\"";
         $query.=$badgeid."\",".$sessionid.",null,null,null)";
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
