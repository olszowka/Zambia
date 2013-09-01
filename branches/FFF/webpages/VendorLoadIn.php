<?php
define ("newroomslots",5); // number of rows at bottom of page for new schedule entries (should go in coninfo)
$title="Vendor Load In Schedule";
require_once('StaffCommonCode.php');
require_once('SubmitMaintainRoom.php');
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

global $daymap;

staff_header($title);

if (isset($_POST["numrows"])) {
	$ignore_conflicts=(isset($_POST['override']))?true:false;
	if(!SubmitMaintainRoom($ignore_conflicts)) $conflict=true;
    }

if (isset($_POST["selroom"])) { // room was selected by this form
        $selroomid=$_POST["selroom"];
        //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }
    elseif (isset($_GET["selroom"])) { // room was select by external page such as a report
        $selroomid=$_GET["selroom"];
        }
    else {
        $selroomid=17; // room was not yet selected, so we, by default, select the Loading Dock
        unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }

if ($conflict!=true) {
  echo "<br><P>For any session where you are rescheduling, please read the Notes for Programming Committee. \n";
    if (isset($_SESSION['return_to_page'])) {
      echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
    }
    echo "<HR>\n";
    // unset all stuff from posts so input fields get reset to blank
    for ($i=1;$i<=newroomslots;$i++) {
      unset($_POST["day$i"]);
      unset($_POST["hour$i"]);
      unset($_POST["min$i"]);
      unset($_POST["ampm$i"]);
      unset($_POST["sess$i"]);
    }
} else {
  //
}

echo "<FORM name=\"rmschdform\" method=POST action=\"VendorLoadIn.php\">\n";
if ($conflict==true) {
	echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"override\" class=\"SubmitButton\">Save Anyway!</BUTTON></DIV>\n";
	echo "<BR><HR>\n";
	}
$query = <<<EOD
SELECT 
    roomid,
    roomname,
    function,
    floor,
    notes
  FROM
      Rooms
  WHERE
    roomid=$selroomid
EOD;
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<H2>$selroomid - ".htmlspecialchars(mysql_result($result,0,"roomname"))."</H2>";
echo "<CENTER>\n";
echo "  <H4>Characteristics</H4>\n";
echo "  <TABLE class=\"border1111=\">\n";
echo "    <TR>\n";
echo "      <TH class=\"lrpad border1111\">Function</TH>\n";
echo "      <TH class=\"lrpad border1111\">Floor</TH>\n";
echo "    </TR>\n";
echo "    <TR>\n";
echo "      <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"function"))."</TD>\n";
echo "     <TD class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"floor"))."</TD>\n";
echo "     </TR>\n";
echo "    <TR>\n";
echo "      <TD colspan=5 class=\"lrpad border1111\">".htmlspecialchars(mysql_result($result,0,"notes"))."</TD>\n";
echo "    </TR>\n";
echo "  </TABLE>\n";
echo "</CENTER>\n";

$query = <<<EOD
SELECT 
    concat("<INPUT type=\"checkbox\" name=\"del",@rownum:=@rownum+1,"\" value=\"1\">\n<INPUT type=\"hidden\" name=\"row",@rownum,"\" value=\"",scheduleid,"\"><INPUT type=\"hidden\" name=\"rowsession",@rownum,"\" value=\"",sessionid,"\">") AS "Delete",
    DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') as 'Start Time',
    concat("<A HREF=StaffAssignParticipants.php?selsess=",sessionid,">",sessionid,"</A>") AS SessionID,
    concat("<A HREF=EditSession.php?id=",sessionid,">",title,"</A>") AS Vendor,
    secondtitle AS Location,
    statusname AS Status
  FROM
      Schedule
    JOIN Sessions USING (sessionid)
    JOIN $ReportDB.Tracks USING (trackid)
    JOIN $ReportDB.RoomSets USING (roomsetid)
    JOIN $ReportDB.SessionStatuses USING (statusid),
    (SELECT @rownum:=0) AS R
  WHERE
    roomid=$selroomid AND
    roomsetname in ("Vendor")
  ORDER BY
    starttime
EOD;

// Retrieve query
list($rows,$scheduledheader_array,$scheduled_array)=queryreport($query,$link,$title,$description,0);
echo renderhtmlreport(1,$rows,$scheduledheader_array,$scheduled_array);
$numrows=$rows;

echo "<H4>Add To Room Schedule</H4>\n";
echo "<TABLE>\n";

$query = <<<EOD
SELECT
    sessionid,
    trackname,
    title
  FROM
      Sessions
    JOIN $ReportDB.Tracks USING (trackid)
    JOIN $ReportDB.SessionStatuses USING (statusid)
    LEFT JOIN Schedule USING (sessionid)
  WHERE
    statusname IN ("Vendor Pending", "Vendor Approved", "Vendor Paid", "Vendor Declined")
EOD;

if (strtoupper(DOUBLE_SCHEDULE)=="TRUE") {
  //
} else {
  $query .= "    AND roomid is null
";
}
$query .= "  ORDER BY
    title,
    sessionid
";

list($addrows,$addheader_array,$add_array)=queryreport($query,$link,$title,$description,0);

for ($i=1;$i<=newroomslots;$i++) {
    echo "   <TR>\n";
    echo "      <TD>";
    // ****DAY****
    if (CON_NUM_DAYS>1) {
        echo "<Select name=day$i><Option value=0 ";
        if ((!isset($_POST["day$i"])) or $_POST["day$i"]==0) echo "selected";
        echo ">Day&nbsp;</Option>";
        for ($j=1; $j<=CON_NUM_DAYS; $j++) {
            $x=$daymap["long"]["$j"];
            echo"         <OPTION value=$j ";
            if ($_POST["day$i"]==$j) echo "selected";
            echo ">$x</OPTION>\n";
            }
        echo "</Select>&nbsp;\n";
        }
	// ****HOUR****
    echo "          <Select name=\"hour$i\"><Option value=\"-1\" ";
    if (!isset($_POST["hour$i"])) $_POST["hour$i"]=-1;
    if ($_POST["hour$i"]==-1) echo "selected";
    echo ">Hour&nbsp;</Option><Option value=0 ";
	if ($_POST["hour$i"]==0) echo "selected";
	echo ">12</Option>";
    for ($j=1;$j<=11;$j++) {
        echo "<Option value=$j ";
        if ($_POST["hour$i"]==$j) echo "selected";
        echo ">$j</Option>";
        }
    echo "</select>\n";
	// ****MIN****
    echo "          <Select name=\"min$i\"><Option value=\"-1\" ";
	if (!isset($_POST["min$i"])) $_POST["min$i"]=-1;
    if ($_POST["min$i"]==-1) echo "selected";
	echo">Min&nbsp;</Option>";
    for ($j=0;$j<=55;$j+=5) {
        echo "<Option value=$j ";
        if ($_POST["min$i"]==$j) echo "selected";
		echo ">".($j<10?"0":"").$j."</Option>";
        }
    echo "</select>\n";
	// ****AM/PM****
    echo "          <Select name=\"ampm$i\"><Option value=0 ";
    if ((!isset($_POST["ampm$i"])) or $_POST["ampm$i"]==0) echo "selected";
    echo ">AM&nbsp;</Option><Option value=1 ";
    if ($_POST["ampm$i"]==1) echo "selected";
	echo ">PM</Option>";
    echo "</select>\n";
    echo "          </TD>";
    // ****Session****
    echo "      <TD><Select name=\"sess$i\"><Option value=\"unset\" ";
	if ((!isset($_POST["sess$i"])) or $_POST["sess$i"]=="unset") echo "selected";
    echo ">Select Session</Option>\n";
    for ($j=1;$j<=$addrows;$j++) {
        echo "          <Option value=\"".$add_array[$j]["sessionid"]."\" ";
        if ($_POST["sess$i"]==$add_array[$j]["sessionid"]) echo "selected";
		echo ">{$add_array[$j]['title']} - {$add_array[$j]['sessionid']} - {$add_array[$j]['trackname']}</option>\n";
        }
    echo "</select>\n";
    echo "          </TD>\n";
    echo "       </TR>\n";
    }
echo "</TABLE>";
echo "<INPUT type=\"hidden\" name=\"selroom\" value=\"$selroomid\">\n";
echo "<INPUT type=\"hidden\" name=\"numrows\" value=\"$numrows\">\n";
echo "<INPUT type=\"hidden\" name=\"nostatchange\" value=\"True\">\n";
echo "<DIV class=\"SubmitDiv\"><BUTTON type=\"submit\" name=\"update\" class=\"SubmitButton\">Update</BUTTON></DIV>\n";
echo "</FORM>\n";
staff_footer();
?>
