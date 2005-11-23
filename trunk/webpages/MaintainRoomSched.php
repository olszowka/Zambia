<?php
$title="Maintain Room Schedule";
require_once('db_functions.php');
require_once('data_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');

staff_header($title);

if (isset($_POST["numrows"])) {
    SubmitMaintainRoom();
    }

if (isset($_POST["selroom"])) {
        $selroomid=intval($_POST["selroom"]);
        }
    else {
        $selroomid=0;
        }
$query="SELECT roomid, roomname FROM Rooms ORDER BY display_order";
if (!$Rresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<FORM name=\"selroomform\" method=POST action=\"MaintainRoomSched.php\">\n";
echo "<DIV><LABEL for=\"selroom\">Select Room</LABEL>\n";
echo "<SELECT name=\"selroom\">\n";
echo "     <OPTION value=0 ".(($selroomid==0)?"selected":"").">Select Room</OPTION>\n";
while (list($roomid,$roomname)= mysql_fetch_array($Rresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$roomid."\" ".(($selroomid==$roomid)?"selected":"");
    echo ">".htmlspecialchars($roomname)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
echo "</FORM>\n";
echo "<HR>\n";
if ($selroomid==0) {
    staff_footer();
    exit();
    }
$query = <<<EOD
SELECT roomid, roomname, opentime1, closetime1, opentime2, closetime2, opentime3, closetime3,
height, dimensions, area, notes FROM Rooms WHERE roomid=$selroomid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
$query = <<<EOD
SELECT capacity FROM RoomHasSet WHERE roomsetid=5 AND roomid=$selroomid
EOD;
if (!$Cresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<H2>$selroomid - ".htmlspecialchars(mysql_result($result,0,"roomname"))."</H2>";
//echo "|".mysql_result($result,0,"opentime1")."|<BR>\n";
echo "<P>Open Times\n";
echo "<DIV class=\"border1111 lrpad lrmargin\"><P class=\"lrmargin\">";
if (mysql_result($result,0,"opentime1")!="") {
    echo time_description(mysql_result($result,0,"opentime1"))." through ".time_description(mysql_result($result,0,"closetime1"))."<BR>\n";
    }
if (mysql_result($result,0,"opentime2")!="") {
    echo time_description(mysql_result($result,0,"opentime2"))." through ".time_description(mysql_result($result,0,"closetime2"))."<BR>\n";
    }
if (mysql_result($result,0,"opentime3")!="") {
    echo time_description(mysql_result($result,0,"opentime3"))." through ".time_description(mysql_result($result,0,"closetime3"))."<BR>\n";
    }
echo "</DIV>\n";
echo "<P>Characteristics\n";
echo "   <TABLE class=\"border1111=\">\n";
echo "      <TR>\n";
echo "         <TH class=\"lrpad border1111\">Dimensions</TH>\n";
echo "         <TH class=\"lrpad border1111\">Area</TH>\n";
echo "         <TH class=\"lrpad border1111\">Height</TH>\n";
echo "         <TH class=\"lrpad border1111\">Theater<BR>Seating</TH>\n";
echo "         </TR>\n";
echo "      <TR>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"dimensions"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"area"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"height"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($Cresult,0,"capacity"))."</TD>\n";
echo "         </TR>\n";
echo "      <TR>\n";
echo "         <TD colspan=4 class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"notes"))."</TD>\n";
echo "         </TR>\n";
echo "      </TABLE>\n";

echo "<HR>\n";
$query = <<<EOD
SELECT SC.scheduleid, SC.starttime, SC.sessionid, T.trackname, S.title FROM Schedule SC join Sessions S on SC.sessionid = S.sessionid
join Tracks T on S.trackid = T.trackid WHERE SC.roomid=$selroomid ORDER BY SC.starttime
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
$i=0;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $i++;
    }
$numrows=$i;
echo "<FORM name=\"rmschdform\" method=POST action=\"MaintainRoomSched.php\">\n";
echo "<TABLE>\n";
for ($i=0;$i<$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"vatop\"><INPUT type=\"checkbox\" name=\"del$i\" value=\"1\"></TD>\n";
    echo "      <TD class=\"vatop lrpad\">Delete";
    echo "<INPUT type=\"hidden\" name=\"row$i\" value=\"".$bigarray[$i]["scheduleid"]."\"></TD>\n";
    echo "      <TD class=\"vatop lrpad\">".time_description($bigarray[$i]["starttime"])."</TD>\n";
    echo "      <TD class=\"vatop lrpad\">".$bigarray[$i]["trackname"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad\">".$bigarray[$i]["sessionid"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad\">".$bigarray[$i]["title"]."</TD>\n";
    echo "      </TR>\n";
    }
$query = <<<EOD
SELECT S.sessionid, T.trackname, S.title, SC.roomid FROM Tracks AS T join Sessions AS S ON T.trackid = S.trackid
LEFT JOIN Schedule AS SC ON S.sessionid = SC.sessionid where S.statusid = 2 HAVING SC.roomid IS NULL ORDER BY T.trackname,
S.title
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
$i=0;
while ($bigarray[$i] = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $i++;
    }
$numsessions=$i;
for ($i=1;$i<=5;$i++) {
    echo "   <TR>\n";
	echo "      <TD>&nbsp;</TD>\n";
	echo "      <TD>&nbsp;</TD>\n";
	echo "      <TD><Select name=\"day$i\"><Option value=0 selected>Day</Option><Option value=1>Fri</Option>";
	echo "<Option value=2>Sat</Option><Option value=3>Sun</Option></Select>&nbsp;\n";
	echo "          <Select name=\"hour$i\"><Option value=\"unset\" selected>Hour</Option><Option value=0>12</Option>";
	for ($j=1;$j<=11;$j++) {
		echo "<Option value=$j>$j</Option>";
		}
	echo "</select>\n";
	echo "          <Select name=\"min$i\"><Option value=\"unset\" selected>Min</Option><Option value=0>00</Option>";
	echo "<Option value=5>05</Option>";
	for ($j=10;$j<=55;$j+=5) {
		echo "<Option value=$j>$j</Option>";
		}
	echo "</select>\n";
	echo "          <Select name=\"ampm$i\"><Option value=0 selected>AM</Option><Option value=1>PM</Option>";
	echo "</select>\n";
	echo "          </TD>";
	echo "      <TD colspan=3><Select name=\"sess$i\"><Option value=\"unset\" selected>Select Session</Option>\n";
	for ($j=0;$j<$numsessions;$j++) {
		echo "          <Option value=\"".$bigarray[$j]["sessionid"]."\">".$bigarray[$j]["trackname"]." - ";
		echo $bigarray[$j]["sessionid"]." - ".$bigarray[$j]["title"]."</option>\n";
		}
	echo "</select>\n";
	echo "          </TD>\n";
	echo "       </TR>\n";
	}
echo "</TABLE>";
echo "<INPUT type=\"hidden\" name=\"selroom\" value=\"$selroomid\">\n";
echo "<INPUT type=\"hidden\" name=\"numrows\" value=\"$numrows\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BUTTON></DIV>\n";
echo "</FORM>\n";
staff_footer();
?>


