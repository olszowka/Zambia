<?php
/*  
Volunteer Maintinence System Module (VMS) v 0.1


This system is used to keep up with the various (currently) programming volunteers that are scheduled
through out the Con.  With some human interaction (currently) it can be determined if the volunteer
is cronically late, lacking in hours promissed and other potential issues based on the time they are working.

This page, specifically, deals with signing in and out volunteers for the shifts they work during the Con.
*/

// Zambia code
$title="Volunteer Time Card Tool";
require_once('StaffCommonCode.php');
require_once('SubmitAdminParticipants.php');

// VMS Code
require_once('VolunteerCommon_FNC.php');

//Zambia Code
staff_header($title);


// start everything off from a standardized starting point. This page currently spawned from the genindex.php page.
// Volunteer stuff needs it's own tab.
start_vol_time();

// Clicked in from the front page. This starts the check in or check out pages.
if (isset($_POST[check])) {
	if ($_POST[check] == 'in') {
		start_checkin_proc();
		exit;		
	} elseif ($_POST[check] == 'out') {
		start_checkout_proc();
		exit;
	} elseif ($_POST[check] == 'report') {
	echo "Debug code: Remove me!...<br />String POST[check] returned: ";
	echo $_POST[check];
	echo ". <br />";
	start_report();
	exit;
	}
}

// Set up for INSERT and UPDATE to the TimeCard Database.
if (isset($_POST[check_do])) {
//Setup for function do_clockin()
	if ($_POST[check_do] == 'doin') {
		$volbadgeid = $_POST[volbadgeid];
		$date = $_POST[date];
		$voltimein = $_POST[voltimein];
		$volcheckinbyid = $_POST[volcheckinbyid];
		$notes = $_POST[notes];
		do_clockin($volbadgeid, $date, $voltimein, $volcheckinbyid, $notes);
		exit;
	}
//Setup for function do_clockout()
	if ($_POST[check_do] == 'doout') {
		$volbadgeid = $_POST[volbadgeid];
		$date = $_POST[date];
		$voltimeout = $_POST[voltimeout];
		$volcheckoutbyid = $_POST[volcheckoutbyid];
		$notes = $_POST[notes];
		do_clockout($volbadgeid, $date, $voltimeout, $volcheckoutbyid, $notes);
		exit;
	} else {
		echo "Error: check_do string not as expected";
		staff_footer();
		exit;
	}
}

if (!isset($_POST[check]) || !isset($_POST[check_do])) {
	echo "<p align=\"center\">";
	echo $welcome_msg;
	echo "</p>";
}
?>
