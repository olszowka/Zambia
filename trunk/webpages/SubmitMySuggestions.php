<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="My Suggestions";
    require ('db_functions.php'); //define database functions
    require ('validation_functions.php'); //define functions to validate data entry
    require_once('ParticipantFooter.php');
    require_once('renderMySuggestions.php');
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    $newrow=($_POST["newrow"]==1);
    $paneltopics=stripslashes($_POST["paneltopics"]);
    $otherideas=stripslashes($_POST["otherideas"]);
    $suggestedguests=stripslashes($_POST["suggestedguests"]);
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
