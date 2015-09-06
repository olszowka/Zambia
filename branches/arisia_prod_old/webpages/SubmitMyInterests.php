<?php
    global $participant,$message_error,$message2,$congoinfo;
    $title="My Interests";
    require ('PartCommonCode.php'); // initialize db; check login;
    require_once('ParticipantHeader.php');
    require_once('renderMyInterests.php');
    if (!may_I('my_gen_int_write')) {
        $message="Currently, you do not have write access to this page.\n";
        RenderError($title,$message);
        exit();
        }
    $rolerows=$_POST["rolerows"];
    $newrow=$_POST["newrow"];
    $yespanels=stripslashes($_POST["yespanels"]);
    $nopanels=stripslashes($_POST["nopanels"]);
    $yespeople=stripslashes($_POST["yespeople"]);
    $nopeople=stripslashes($_POST["nopeople"]);
    $otherroles=stripslashes($_POST["otherroles"]);
    for ($i=0; $i<$rolerows; $i++) {
        if (isset($_POST["willdorole".$i])) {
            $rolearray[$i]["badgeid"]=$badgeid;
            }
        $rolearray[$i]["roleid"]=$_POST["roleid".$i];
        $rolearray[$i]["rolename"]=$_POST["rolename".$i];
        $rolearray[$i]["diddorole"]=$_POST["diddorole".$i];
        }
    if ($newrow) {
            $query="INSERT INTO ParticipantInterests set badgeid=\"".$badgeid;
            $query.="\",yespanels=\"".mysql_real_escape_string($yespanels,$link);
            $query.="\",nopanels=\"".mysql_real_escape_string($nopanels,$link);
            $query.="\",yespeople=\"".mysql_real_escape_string($yespeople,$link);
            $query.="\",nopeople=\"".mysql_real_escape_string($nopeople,$link);
            $query.="\",otherroles=\"".mysql_real_escape_string($otherroles,$link)."\"";
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error inserting into database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            }
        else {
            $query="UPDATE ParticipantInterests set ";
            $query.="yespanels=\"".mysql_real_escape_string($yespanels,$link)."\",";
            $query.="nopanels=\"".mysql_real_escape_string($nopanels,$link)."\",";
            $query.="yespeople=\"".mysql_real_escape_string($yespeople,$link)."\",";
            $query.="nopeople=\"".mysql_real_escape_string($nopeople,$link)."\",";
            $query.="otherroles=\"".mysql_real_escape_string($otherroles,$link)."\" ";
            $query.="WHERE badgeid=\"".$badgeid."\"";
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error updating database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            }
    for ($i=0; $i<$rolerows; $i++) {
        if (isset($rolearray[$i]["badgeid"])&&($rolearray[$i]["diddorole"]==0)) {
            $query="INSERT INTO ParticipantHasRole set badgeid=\"".$badgeid."\", roleid=".$rolearray[$i]["roleid"]."";
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error inserting into database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            }
        if ((!isset($rolearray[$i]["badgeid"]))&&($rolearray[$i]["diddorole"]==1)) {
            $query="DELETE FROM ParticipantHasRole WHERE badgeid=\"".$badgeid."\" and ";
            $query.="roleid=".$rolearray[$i]["roleid"];
            if (!mysql_query($query,$link)) {
                $message=$query."<BR>Error deleting from database.  Database not updated.";
                RenderError($title,$message);
                exit();
                }
            }
        }    
    $message="Database updated successfully."; 
    $newrow=false;
    $error=false;
    renderMyInterests($title, $error, $message);
    participant_footer();
    exit(0);
?>        
