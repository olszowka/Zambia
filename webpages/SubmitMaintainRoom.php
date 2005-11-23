<?php
function SubmitMaintainRoom() {
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
		echo $schedentries."<BR>\n";
		$query="UPDATE Sessions AS S, Schedule as SC SET S.statusid=1 WHERE S.sessionid=SC.sessionid AND ";
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
        }
	exit();
	}
?>
