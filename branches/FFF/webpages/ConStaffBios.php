<?php
require_once('PostingCommonCode.php');
global $link;
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$ConNumDays=CON_NUM_DAYS; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// LOCALIZATIONS
$_SESSION['return_to_page']="ConStaffBios.php";
$title="Organizational Chart";
$description="<P>List of all Organizers and their Roles, reports, and who they report to.</P>\n";

// Start page
topofpagereport($title,$description,$additionalinfo);
if (file_exists("../Local/Verbiage/ConStaffBios_0")) {
  echo file_get_contents("../Local/Verbiage/ConStaffBios_0");
} else {
  echo "<P>Unfortunately this has yet to be implemented.  Please create this page and put it in:<br>\n";
  echo "<B>Local/Verbiage/ConStaffBios_0</B><br>\n";
  echo "So it will show up here.\n";
}

correct_footer(); 
?>