<?php
function SubmitMaintainRoom() {
//
//  This is hardcoded to follow the workflow of editme -> vetted -> scheduled -> assigned
//  We need to find a way to make it more configurable and flexible
//
    global $link;
//    print_r($_POST);
    $numrows=$_POST["numrows"];
    $selroomid=$_POST["selroom"];
    $schedentries="";
    for ($i=0; $i<$numrows; $i++) {
        if(!($_POST["del$i"]==1)) {
            continue;
            };
        $schedentries.=$_POST["row$i"].",";
        }
    if (!$schedentries=="") {
        $schedentries=substr($schedentries,0,strlen($schedentries)-1); // remove trailing comma
//        echo $schedentries."<BR>\n";
//  Set status of deleted entries back to vetted.
        $vs=get_idlist_from_db('SessionStatuses','statusid','statusname',"'vetted'");
        $query="UPDATE Sessions AS S, Schedule as SC SET S.statusid=$vs WHERE S.sessionid=SC.sessionid AND ";
        $query.="SC.scheduleid IN ($schedentries)";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        $query="DELETE FROM Schedule WHERE scheduleid in ($schedentries)";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        $rows=mysql_affected_rows($link);
        echo "<P class=\"regmsg\">$rows session".($rows>1?"s":"")." removed from schedule.\n";
        }
    $rows=0;
    $warn=0;
    for ($i=1;$i<=newroomslots;$i++) {
        if ($_POST["sess$i"]=="unset") {
            continue;
            }
        if (CON_NUM_DAYS==1) {
                $day=1;
                }
            else {
                $day=$_POST["day$i"];
                }
        if ($day==0 or $_POST["hour$i"]=="unset" or  $_POST["min$i"]=="unset") {
            $warn++;
            continue;
            }
        $time=(($day-1)*24+$_POST["ampm$i"]*12+$_POST["hour$i"]).":".$_POST["min$i"];
        $sessionid=$_POST["sess$i"];
        $query="INSERT INTO Schedule SET sessionid=$sessionid, roomid=$selroomid, starttime=\"$time\"";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
// Set status of scheduled entries to Scheduled.
        $vs=get_idlist_from_db('SessionStatuses','statusid','statusname',"'scheduled'");
        $query="UPDATE Sessions SET statusid=$vs WHERE sessionid=$sessionid";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        $rows++;    
        }
    if ($rows) {
        echo "<P class=\"regmsg\">$rows new schedule entr".($rows>1?"ies":"y")." written to database.\n";
        }
    if ($warn) {
        echo "<P class=\"errmsg\">$warn row".($warn>1?"s":"")." not entered due to incomplete data.\n";
        }
        
    }
?>
