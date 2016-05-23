<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="My Suggestions";
    require ('PartCommonCode.php');
    require_once('renderMySuggestions.php');
    // set $badgeid from session
    $result=mysql_query("SELECT * FROM ParticipantSuggestions where badgeid=\"".$badgeid."\"",$link);
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
            $paneltopics="";
            $otherideas=""; 
            $suggestedguests="";
            $newrow=true;
            }
        else {
            list($foo,$paneltopics,$otherideas,$suggestedguests)=mysql_fetch_array($result, MYSQL_NUM);
            $newrow=false;
            }
    $error=false;
    $message="";
    renderMySuggestions($title, $error, $message);
    participant_footer();
?>
