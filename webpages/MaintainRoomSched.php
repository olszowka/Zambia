<?php
define ("newroomslots",5); // number of rows at bottom of page for new schedule entries
$title="Maintain Room Schedule";
require_once('db_functions.php');
require_once('data_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');

staff_header($title);
$topsectiononly=true; // no room selected -- flag indicates to display only the top section of the page

if (isset($_POST["numrows"])) {
    SubmitMaintainRoom();
    }

if (isset($_POST["selroom"])) { // room was selected by this form
        $selroomid=$_POST["selroom"];
        $topsectiononly=false;
        //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }
    elseif (isset($_GET["selroom"])) { // room was select by external page such as a report
        $selroomid=$_GET["selroom"];
        $topsectiononly=false;
        }
    else {
        $selroomid=0; // room was not yet selected.
        unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }

$query="SELECT roomid, roomname, function FROM Rooms ORDER BY display_order";
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
while (list($roomid,$roomname, $rmfunct)= mysql_fetch_array($Rresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$roomid."\" ".(($selroomid==$roomid)?"selected":"");
    echo ">".htmlspecialchars($roomname)." (".htmlspecialchars($rmfunct).")</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\">";
if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
    }
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Submit</BUTTON></DIV>\n";
echo "</FORM>\n";
echo "<HR>\n";
if ($topsectiononly) {
    staff_footer();
    exit();
    }
$query = <<<EOD
SELECT roomid, roomname, opentime1, closetime1, opentime2, closetime2, opentime3, closetime3,
function, floor, height, dimensions, area, notes FROM Rooms WHERE roomid=$selroomid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<H2>$selroomid - ".htmlspecialchars(mysql_result($result,0,"roomname"))."</H2>";
//echo "|".mysql_result($result,0,"opentime1")."|<BR>\n";
echo "<H4>Open Times</H4>\n";
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
echo "<H4>Characteristics</H4>\n";
echo "   <TABLE class=\"border1111=\">\n";
echo "      <TR>\n";
echo "         <TH class=\"lrpad border1111\">Function</TH>\n";
echo "         <TH class=\"lrpad border1111\">Floor</TH>\n";
echo "         <TH class=\"lrpad border1111\">Dimensions</TH>\n";
echo "         <TH class=\"lrpad border1111\">Area</TH>\n";
echo "         <TH class=\"lrpad border1111\">Height</TH>\n";
echo "         </TR>\n";
echo "      <TR>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"function"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"floor"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"dimensions"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"area"))."</TD>\n";
echo "         <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"height"))."</TD>\n";
echo "         </TR>\n";
echo "      <TR>\n";
echo "         <TD colspan=5 class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"notes"))."</TD>\n";
echo "         </TR>\n";
echo "      </TABLE>\n";
echo "<H4>Room Sets</H4>\n";
$query = <<<EOD
SELECT RS.roomsetname, RHS.capacity FROM RoomSets RS, RoomHasSet RHS WHERE
RS.roomsetid=RHS.roomsetid AND RHS.roomid=$selroomid
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
echo "   <TABLE class=\"border1111=\">\n";
echo "      <TR>\n";
echo "         <TH class=\"lrpad border1111\">Room Set</TH>\n";
echo "         <TH class=\"lrpad border1111\">Capacity</TH>\n";
echo "         </TR>\n";
for ($i=0;$i<$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"vatop lrpad border1111\">".$bigarray[$i]["roomsetname"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border1111\">".$bigarray[$i]["capacity"]."</TD>\n";
    echo "      </TR>\n";
    }
echo "      </TABLE>\n";
$query = <<<EOD
SELECT SC.scheduleid, SC.starttime, S.duration, SC.sessionid, T.trackname, S.title, ST.roomsetname 
FROM Schedule SC, Sessions S, Tracks T, RoomSets ST WHERE
SC.sessionid = S.sessionid AND S.trackid = T.trackid AND S.roomsetid=ST.roomsetid
AND SC.roomid=$selroomid ORDER BY SC.starttime
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

echo "<HR>\n";
echo "<FORM name=\"rmschdform\" method=POST action=\"MaintainRoomSched.php\">\n";
echo "<H4>Current Room Schedule</H4>\n";
echo "<TABLE>\n";
echo "   <TR>\n";
echo "      <TH>Delete</TH>\n";
echo "      <TH>Start Time</TH>\n";
echo "      <TH>Duration</TH>\n";
echo "      <TH>Track</TH>\n";
echo "      <TH>Session ID</TH>\n";
echo "      <TH>Title</TH>\n";
echo "      <TH>Room Set</TH>\n";
echo "      </TR>\n";
for ($i=0;$i<$numrows;$i++) {
    echo "   <TR>\n";
    echo "      <TD class=\"border0010\"><INPUT type=\"checkbox\" name=\"del$i\" value=\"1\"></TD>\n";
    echo "<INPUT type=\"hidden\" name=\"row$i\" value=\"".$bigarray[$i]["scheduleid"]."\"></TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".time_description($bigarray[$i]["starttime"])."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["duration"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["trackname"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\"> <a href=EditSession.php?id=".$bigarray[$i]["sessionid"].">".$bigarray[$i]["sessionid"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["title"]."</TD>\n";
    echo "      <TD class=\"vatop lrpad border0010\">".$bigarray[$i]["roomsetname"]."</TD>\n";
    echo "      </TR>\n";
    }
echo "   </TABLE>\n";
echo "<H4>Add To Room Schedule</H4>\n";
echo "<TABLE>\n";
$query = <<<EOD
SELECT S.sessionid, T.trackname, S.title, SC.roomid FROM Tracks AS T join Sessions AS S ON T.trackid = S.trackid
LEFT JOIN Schedule AS SC ON S.sessionid = SC.sessionid where S.statusid = 7 or S.statusid = 3 or S.statusid=2 HAVING SC.roomid IS NULL ORDER BY T.trackname,
S.sessionid
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
for ($i=1;$i<=newroomslots;$i++) {
    echo "   <TR>\n";
    echo "      <TD><Select name=\"day$i\"><Option value=0 selected>Day&nbsp;</Option><Option value=1>Fri</Option>";
    echo "<Option value=2>Sat</Option><Option value=3>Sun</Option><Option value=4>Mon</Option></Select>&nbsp;\n";
    echo "          <Select name=\"hour$i\"><Option value=\"unset\" selected>Hour&nbsp;</Option><Option value=0>12</Option>";
    for ($j=1;$j<=11;$j++) {
        echo "<Option value=$j>$j</Option>";
        }
    echo "</select>\n";
    echo "          <Select name=\"min$i\"><Option value=\"unset\" selected>Min&nbsp;</Option><Option value=0>00</Option>";
    echo "<Option value=5>05</Option>";
    for ($j=10;$j<=55;$j+=5) {
        echo "<Option value=$j>$j</Option>";
        }
    echo "</select>\n";
    echo "          <Select name=\"ampm$i\"><Option value=0 selected>AM&nbsp;</Option><Option value=1>PM</Option>";
    echo "</select>\n";
    echo "          </TD>";
    echo "      <TD><Select name=\"sess$i\"><Option value=\"unset\" selected>Select Session</Option>\n";
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


