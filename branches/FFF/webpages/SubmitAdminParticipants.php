<?php
function SubmitAdminParticipants () {
    global $link;
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    $interested = $_POST["interested"];
    $wasinterested = $_POST["wasinterested"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $partid = $_POST["partid"];
    $pubsname = stripslashes($_POST["pubsname"]);
    if ($password=="" and $cpassword=="") {
            $update_password=false;
            }
        elseif ($password==$cpassword) {
            $update_password=true;
            }
        else {
            $message="Passwords do not match each other.  Database not updated.";
            echo "<P class=\"errmsg\">".$message."\n";
            return;
            }
    $query = "UPDATE $ReportDB.Participants SET ";
    if ($update_password==true) {
        $query=$query."password=\"".md5($password)."\", ";
        }
    $query.="interested=".$interested.", ";
    $query.="pubsname=\"".mysql_real_escape_string($pubsname)."\" ";
    $query.="WHERE badgeid=\"$partid\"";                               //"
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        echo "<P class=\"errmsg\">".$message."\n";
        return;
        }
    $message="Database updated successfully.<BR>";
    if ($wasinterested==1 and $interested==2) {
        $query="DELETE FROM ParticipantOnSession where badgeid = \"$partid\"";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.  Database not updated.";
            echo "<P class=\"errmsg\">".$message."\n";
            return;
            }
        $message.="Participant removed from ".mysql_affected_rows($link)." session(s).";
        }
    echo "<P class=\"regmsg\">".$message."\n";
    }
?>
