<?php
function SubmitCommentOnProgramming () {
    global $link;
    $commenter = $_POST["commenter"];
    $comment = $_POST["comment"];
    $query = "INSERT INTO CommentsOnProgramming (rbadgeid,commenter,comment) VALUES ('";
    $query.=$_SESSION['badgeid']."','";
    $query.=mysql_real_escape_string($commenter)."','";
    $query.=mysql_real_escape_string($comment)."')";
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        echo "<P class=\"errmsg\">".$message."\n";
        return;
        }
    $message="Database updated successfully.<BR>";
    echo "<P class=\"regmsg\">".$message."\n";
    }

function SubmitCommentOnParticipants () {
    global $link;
    $commenter = $_POST["commenter"];
    $comment = $_POST["comment"];
    $partid = $_POST["partid"];
    $pubsname = stripslashes($_POST["pubsname"]);
    $query = "INSERT INTO CommentsOnParticipants (badgeid,rbadgeid,commenter,comment) VALUES ('";
    $query.=$partid."','";
    $query.=$_SESSION['badgeid']."','";
    $query.=mysql_real_escape_string($commenter)."','";
    $query.=mysql_real_escape_string($comment)."')";
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        echo "<P class=\"errmsg\">".$message."\n";
        return;
        }
    $message="Database updated successfully.<BR>";
    echo "<P class=\"regmsg\">".$message."\n";
    }

function SubmitCommentOnSessions () {
    global $link;
    $commenter = $_POST["commenter"];
    $comment = $_POST["comment"];
    $sessionid = $_POST["sessionid"];
    $query = "INSERT INTO CommentsOnSessions (sessionid,rbadgeid,commenter,comment) VALUES ('";
    $query.=$sessionid."','";
    $query.=$_SESSION['badgeid']."','";
    $query.=mysql_real_escape_string($commenter)."','";
    $query.=mysql_real_escape_string($comment)."')";
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        echo "<P class=\"errmsg\">".$message."\n";
        return;
        }
    $message="Database updated successfully.<BR>";
    echo "<P class=\"regmsg\">".$message."\n";
    }
?>
