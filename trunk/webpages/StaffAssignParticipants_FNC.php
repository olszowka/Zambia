<?php 
function SubmitAssignParticipants() {
    global $link;
//    print_r($_POST);
    $numrows=$_POST["numrows"];
    $moderator=$_POST["moderator"];
    $wasmodid=$_POST["wasmodid"];
    $selsessionid=$_POST["selsess"];
    for ($i=0; $i<$numrows; $i++) {
        $badgeid=$_POST["row$i"];
        $isasgn=isset($_POST["asgn$badgeid"]);
        $ismod=($moderator==$badgeid);
        $wasasgn=($_POST["wasasgn$badgeid"]==1);
        $wasmod=($wasmodid==$badgeid);
//echo "i: $i | isasgn: $isasgn | wasasgn: $wasasgn | ismod: $ismod | wasmod: $wasmod <BR>\n";        
        if (!$isasgn and $wasasgn) {
                $query="DELETE FROM ParticipantOnSession where badgeid=\"$badgeid\" ";
                $query.="and sessionid=$selsessionid";
                }
            elseif (!$wasasgn and $isasgn) {
                $query="INSERT INTO ParticipantOnSession set badgeid=\"$badgeid\", "; 
                $query.="sessionid=$selsessionid, moderator=".($ismod?1:0);
                }
            elseif (($ismod and !$wasmod) or (!$ismod and $wasmod)) {
                $query="UPDATE ParticipantOnSession set moderator=".($ismod?1:0);
                $query.=" WHERE badgeid=\"$badgeid\" and sessionid=\"$selsessionid\"";
                }
            else {
                continue;
                }
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        }
    }
?>    
