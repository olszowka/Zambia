<?php
function lock_participant($badgeid) {
    global $query, $link;
    $userbadgeid=$_SESSION['badgeid'];
    $query="UPDATE Participants SET biolockedby='$userbadgeid' WHERE biolockedby IS NULL and badgeid='$badgeid'";
    //error_log("Zambia: lock_participant: ".$query);
    $result=mysql_query($query,$link);
    if (!$result) {
        return (-1);
        }
    if (mysql_affected_rows($link)==1) {
            return (0);
            }
        else {
            return (-2);
            }
    }
?>
