<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="My Suggestions";
    require ('PartCommonCode.php');
    require_once('renderMySuggestions.php');
    $newrow=($_POST["newrow"]==1);
    $paneltopics=stripslashes($_POST["paneltopics"]);
    $otherideas=stripslashes($_POST["otherideas"]);
    $suggestedguests=stripslashes($_POST["suggestedguests"]);
    if (!may_I('my_suggestions_write')) {
        $message="Currently, you do not have write access to this page.\n";
         $error=true;
         renderMySuggestions($title, $error, $message);
         exit(0);
         }
    if (!validate_suggestions()) {
         $message.="<BR>Database not updated.\n";
         $error=true;
         renderMySuggestions($title, $error, $message);
         exit(0);
         }
    $message="Database updated successfully."; 
    $error=false;
    if ($newrow) {
            $query="INSERT INTO ParticipantSuggestions set badgeid=\"".$badgeid;
            $query.="\",paneltopics=\"".mysql_real_escape_string($paneltopics,$link);
            $query.="\",otherideas=\"".mysql_real_escape_string($otherideas,$link);
            $query.="\",suggestedguests=\"".mysql_real_escape_string($suggestedguests,$link)."\"";
            if (!mysql_query($query,$link)):
                    $message=$query."<BR>Error inserting into database.  Database not updated.";
                    $error=true;
                else:
                    $newrow=false;
                endif;
            }
        else {
            $query="UPDATE ParticipantSuggestions set ";
            $query.="paneltopics=\"".mysql_real_escape_string($paneltopics,$link)."\",";
            $query.="otherideas=\"".mysql_real_escape_string($otherideas,$link)."\",";
            $query.="suggestedguests=\"".mysql_real_escape_string($suggestedguests,$link)."\" ";
            $query.="WHERE badgeid=\"".$badgeid."\"";
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error updating database.  Database not updated.";
                $error=true;
                }
            }
    renderMySuggestions($title, $error, $message);
    participant_footer();
?>        
