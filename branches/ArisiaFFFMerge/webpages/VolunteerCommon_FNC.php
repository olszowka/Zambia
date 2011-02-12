<?php
// Welcome Message.  Use HTML coding with proper PHP escape characters '\' where needed.
$welcome_msg = "Volunteer Maintinence System Module (VMS) v 0.1 for Zambia by PolloRaro<br><br><br> This system is used to keep up with the various (currently) programming volunteers that are scheduled through out the Con as they come and go.  With some human interaction (currently) it can be determined if the volunteer is cronically late, lacking in hours promissed and other potential issues based on the time they are working.";
/*
=====================================================================================================
======= Do NOT edit below unless you *REALLY* know what you're doing with PHP and MySQL =============
=======           !! There are no user editable variables on this page !!               =============
=====================================================================================================
*/
// Starts the whole shebang. always called on top of page under the title.
function start_vol_time() {
	if (isset($_POST[check])) {
	echo "<p align=\"center\"><a href=\"VolunteerManage.php\">Home</a></p>";
	}
	if (isset($_POST[check_do])) {
	echo "<p align=\"center\"><a href=\"VolunteerManage.php\">Home</a></p>";
	}
?>
<!-- Navigation Buttons -->
<table width="100%">
<tr>
<td><p align="right"><form id="show_button_1" name="show_button_1" method="post" action="VolunteerManage.php">
	<div align="right">
	<input type="hidden" name="check" id="check" value="in" />
	<input type="submit" name="Submit" id="Submit" value="Clock in Volunteer" />
</div></form></p></td>
<td align="right" width="45"> <img src="images/timecard.jpeg"></td>
<td><p align="left"><form id="show_button_2" name="show_button_2" method="post" action="VolunteerManage.php">
	<div align="left">
	<input type="hidden" name="check" id="check" value="out" />
	<input type="submit" name="Submit" id="Submit" value="Clock Out Volunteer" />
</div></form></p></td>
</tr>
</table> 
<?php
}



//==============================================================================
//=====                    Clock In a Volunteer Form                       =====
//==============================================================================




function start_checkin_proc() {
global $link;
global $daymap;

// get's the staff user info that's logged in
list($staffbadgeid,$staffbadgename,$stafffirstname,$stafflastname)= get_session_user(); 

// Get ppl that are not logged in from the database
$query1 = <<<PICKPPLNOTLOGGEDIN
SELECT DISTINCT CD.badgeid, CD.badgename, CD.firstname, CD.lastname 
FROM CongoDump CD
JOIN UserHasPermissionRole UP USING (badgeid)
WHERE UP.permroleid=5 AND CD.badgeid NOT IN (SELECT badgeid from TimeCard WHERE voltimein like '%00' AND voltimeout IS NULL)
ORDER BY CD.badgename
PICKPPLNOTLOGGEDIN;
 	if (!$result1=mysql_query($query1,$link)) {
		$message="::: ERROR :::: Unable to continue. -- SQL QUERY --> \"PICKPPLNOTLOGGEDIN\" <-- FAULT  <BR>";
		handle_db_error($message);
	}
?>
<br />
<hr /><p align=\"center\"> This form only allows you to clock in a volunteer that is not currently clocked in. If you do not see the name you are looking for in the list, you will either need to <a href="StaffEditCreateParticipant.php?action=create">add them to Zambia</a>, check their 'volunteer' box under <a href="AdminParticipants.php">Administer Participant</a> or <form  method="post" action="VolunteerManage.php">
	<input type="hidden" name="check" id="check" value="out" />
	<input type="submit" name="Submit" id="Submit" value="Clock Them Out" />
</form></p>
<p><form method="post" action="VolunteerManage.php">
	<input name="check_do" type="hidden" id="check_do" value="doin" />
	<input type="hidden" name="volcheckinbyid" id="volcheckinbyid" value="<?php echo $_SESSION['badgeid']; ?>" />
  <h2>Volunteer Clock In</h2>
  <p>
    <label for="volbadgeid">Volunteer's Name and Badge ID</label>
    <select name="volbadgeid" id="volbadgeid">
      <option selected="selected">Select</option>
<?php
// Lists people that are not logged in. 
  while (list($volbadgeid,$badgename,$firstname,$lastname)= mysql_fetch_array($result1, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$volbadgeid."\">".htmlspecialchars($badgename)." (".htmlspecialchars($firstname);
    echo " ".htmlspecialchars($lastname).") - ".htmlspecialchars($volbadgeid)."</OPTION>\n";}
?>
</select>
   </p>
  <p>
    <label for="date">Today's Date</label>
    <select name="date" id="date">
<?php 
// Pulls days of the con from Zambia(Local/db_name.php). If there is only one day, only "today" is listed, otherwise, each day is listed.
if (CON_NUM_DAYS>1) {
	for ($i=1; $i<=min(4,CON_NUM_DAYS); $i++) {
                  $D=$daymap["long"][$i]; 
		echo "<option value=\"".$i."\">".$D."</option>\n";
	}
} else {
	echo "<option value=\"1\" selected=\"selected\">Today</option>\n";
} 

?>
    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <label for="TimeIn">Time Volunteer In</label>
     <select name="voltimein" id="voltimein">

<?php        
// Lists times that are available to use from the database using another function in Zambia
	populate_select_from_table_alt("Times", $timeindex, "Select", true); 
?>
     </select>
  </p>
  <p>Clocked in by: 
<?php
     echo $staffbadgeid. " - " .htmlspecialchars($staffbadgename). " (" .htmlspecialchars($stafffirstname);
     echo " " .htmlspecialchars($stafflastname). ")"; 
	echo $StaffName;
?></p>
<p align="center">Notes:<br /><textarea name="notes" id="notes" cols="100" rows="5">Volunteer Clocked In</textarea></p>
  <p align="right">
    <input type="reset" name="clear" id="clear" value="Reset Form" />
    &nbsp;&nbsp; 
    <input type="submit" name="submit" id="submit" value="Submit Form" />
  </p>
</form></p>
<?php		
}



//==============================================================================
//=====                    Clock Out a Volunteer Form                      =====
//==============================================================================



function start_checkout_proc() {
  
global $link;
global $daymap;

// get's the staff user info that's logged in 
list($staffbadgeid,$staffbadgename,$stafffirstname,$stafflastname)= get_session_user(); 


// Picks list of people that are currently logged in.
$query1 = <<<GETLOGGEDIN
SELECT CD.badgeid, CD.badgename, CD.firstname, CD.lastname 
FROM CongoDump CD, TimeCard TC 
WHERE CD.badgeid = TC.badgeid AND TC.voltimeout IS NULL 
ORDER BY CD.badgename
GETLOGGEDIN;

	if (!$result1=mysql_query($query1,$link)) {
		$message="::: ERROR :::: Unable to continue. -- SQL QUERY --> \"GETLOGGEDIN\" <-- FAULT  <BR>";
		handle_db_error($message);
	}

?>
<br/><hr /><p align=\"center\"> This form only allows you to clock out a volunteer that is currently clocked in. If you do not see the name you are looking for in the list, They are not clocked in.<br /><br /><strong>Important Note:</strong> If you are forcing a clock out with this form, do NOT use the current time and date instead, confer with the volunteer and enter the time and date that they ended their last shift.</p>
<form id="vol_check_out" name="vol_check_out" method="post" action="VolunteerManage.php">
	<input name="check_do" type="hidden" id="check_do" value="doout" />
	<input type="hidden" name="volcheckoutbyid" id="volcheckoutbyid" value="<?php echo $_SESSION['badgeid']; ?>" />
  <h2>Volunteer Clock Out</h2>
  <p>
    <label for="volbadgeid">Volunteer's Name and Badge ID</label>
    <select name="volbadgeid" id="volbadgeid">
      <option selected="selected">Select</option>
<?php
// Lists only those that are clocked in 
  while (list($volbadgeid,$badgename,$firstname,$lastname)= mysql_fetch_array($result1, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$volbadgeid."\">".htmlspecialchars($badgename)." (".htmlspecialchars($firstname);
    echo " ".htmlspecialchars($lastname).") - ".htmlspecialchars($volbadgeid)."</OPTION>\n";}
?>
</select>
  </p>
  <p>
    <label for="date">Today's Date</label>
    <select name="date" id="date">
<?php 
// Pulls days of the con from Zambia. If there is only one day, only "today" is listed, otherwise, each day is listed.
if (CON_NUM_DAYS>1) {
	for ($i=1; $i<=min(4,CON_NUM_DAYS); $i++) {
                  $D=$daymap["long"][$i]; 
		echo "<option value=\"".$i."\">".$D."</option>\n";
	}
} else {
	echo "<option value=\"1\" selected=\"selected\">Today</option>\n";
} 

?>
    </select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
     <label for="voltimeout">Time Volunteer Out</label>
     <select name="voltimeout" id="voltimeout">
<?php        
// pulls times from the database (db_functions.php)
populate_select_from_table_alt("Times", actualtime, "Select", true); ?>     </select>
  </p>
  <p>Clocked out by: 
<?php
     echo $staffbadgeid. " - " .htmlspecialchars($staffbadgename). " (" .htmlspecialchars($stafffirstname);
     echo " " .htmlspecialchars($stafflastname). ")"; 
?></p>
<p align="center">Notes:<br /><textarea name="notes" id="notes" cols="100" rows="5">Volunteer Clocked Out</textarea></p>
  <p align="right">
    <input type="reset" name="clear" id="clear" value="Reset Form" />
    &nbsp;&nbsp; 
    <input type="submit" name="submit" id="submit" value="Submit Form" />
  </p>
</form>
<?php
}



//==============================================================================
//=====                    Clock In a Volunteer                            =====
//==============================================================================




function do_clockin($volbadgeid, $date, $voltimein, $volcheckinbyid, $notes) {
global $link;   // Import $link so we can connect to db

global $daymap; //Import global $daymap

$D=$daymap["long"][$date]; // Pull the correct day out of the $daymap array that was entered on the form.

// Work out the math for this $ConStartDatim thingy n-1*24 so we can add it to the time to make it right.
$date_multiplier = ($date-1)*24;
$date_add=$date_multiplier.":00:00";

// get's the staff user info that's logged in 
list($staffbadgeid,$staffbadgename,$stafffirstname,$stafflastname)= get_session_user();

// backwards compatability with Zambia code
$partid=$volbadgeid;
$note=$notes;
// Get Name from the badgeid for human readable content.
$query1 = <<<GETVOLNAME
SELECT CD.firstname, CD.lastname, CD.badgename 
FROM CongoDump CD
WHERE CD.badgeid = '$volbadgeid'
LIMIT 1
GETVOLNAME;

// Pull the available times out of the Times db
$timedisplayq = <<<TIMEDISPLAY
SELECT timetext, DATE_FORMAT(actualtime,'%l:%i %p')
FROM Times
WHERE timeindex = '$voltimein'
TIMEDISPLAY;

// Begin Error messages for above SQL statements
  if (!$result1=mysql_query($query1,$link)) {
    $message=$query1."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
} 
  if (!$result3=mysql_query($timedisplayq,$link)) {
    $message=$timedisplayq."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
// End error messages for SQL statements above. (If no errors encountered continue)
}  else {
// pulls arrays so we can use them
	list($volfirstname,$vollastname,$volbadgename)= mysql_fetch_array($result1, MYSQL_NUM);
	list($timetext,$actualtime)= mysql_fetch_array($result3, MYSQL_NUM);
// Submits the data to the MySQL Server
$submitdata = <<<SUBMITDATA
INSERT INTO TimeCard (badgeid,voltimein,volcheckinbyid)
VALUES ('$volbadgeid',ADDTIME('$date_add','$actualtime'),'$_SESSION[badgeid]')
SUBMITDATA;
	if (!$result4=mysql_query($submitdata,$link)) {
	    $message=$submitdata."<BR>Error querying database. Unable to continue.<BR>";
	    echo "<P class\"errmsg\">".$message."\n";
	    staff_footer();
	    exit();
	}
// begin echo to the user 
	echo "<p align=\"center\"><br /><br />Volunteer: ".$volbadgeid. " - ";
// Prints first, last and badge name from CongoDump for Volunteer being checked in
    	echo htmlspecialchars($volbadgename)." (".htmlspecialchars($volfirstname);
    	echo " ".htmlspecialchars($vollastname).") was clocked in on";
// this prints what day the volunteer was clocked in
	echo " ".$D. " at ";
// Prints what time the volunteer was clocked in
	echo $actualtime;
	echo "<br /><br />Notes:". $notes."<br /><br /><br /></p>";
	submit_participant_note ($note, $partid);
	staff_footer();

// end echo to the user		

	}
}


//==============================================================================
//=====                    Clock Out a Volunteer                           =====
//==============================================================================



function do_clockout($volbadgeid, $date, $voltimeout, $volcheckoutbyid, $notes) {
global $link;   // Import $link so we can connect to db

global $daymap; //Import global $daymap

$D=$daymap["long"][$date]; // Pull the correct day out of the $daymap array that was entered on the form.

// Work out the math for this $ConStartDatim thingy n-1*24 so we can add it to the time to make it right.
$date_multiplier = ($date-1)*24;
$date_add=$date_multiplier.":00:00";

// backwards compatability with Zambia code
$partid=$volbadgeid;
$note=$notes;

// get's the staff user info that's logged in 
list($staffbadgeid,$staffbadgename,$stafffirstname,$stafflastname)= get_session_user();

// Get Name from the badgeid for human readable content.
$query1 = <<<GETVOLNAME
SELECT TC.voltimecheckinkey, CD.badgename, CD.firstname, CD.lastname 
FROM CongoDump CD, TimeCard TC 
WHERE CD.badgeid = TC.badgeid AND TC.voltimeout IS NULL
LIMIT 1
GETVOLNAME;

// Pull the available times out of the Times db
$timedisplayq = <<<TIMEDISPLAY
SELECT timetext, actualtime
FROM Times
WHERE timeindex = '$voltimeout'
TIMEDISPLAY;

// Begin Error messages for above SQL statements
  if (!$result1=mysql_query($query1,$link)) {
    $message=$query1."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
} 

  if (!$result3=mysql_query($timedisplayq,$link)) {
    $message=$timedisplayq."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
// End error messages for SQL statements above. (If no errors encountered continue)
}  else {
// pulls arrays so we can use them
	list($voltimecheckinkey,$volbadgename,$volfirstname,$vollastname)= mysql_fetch_array($result1, MYSQL_NUM);
	list($timetext,$actualtime)= mysql_fetch_array($result3, MYSQL_NUM);
// Submits the data to the MySQL Server

// begin echo to the user 
	echo "<p align=\"center\"><br /><br />Volunteer: ".$volbadgeid. " - ";
// Prints first, last and badge name from CongoDump for Volunteer being checked in
    	echo htmlspecialchars($volbadgename)." (".htmlspecialchars($volfirstname);
    	echo " ".htmlspecialchars($vollastname).") was clocked out on";
// this prints what day the volunteer was clocked in
	echo " ".$D. " at ";
// Prints what time the volunteer was clocked in
	echo $actualtime;
    	echo "<br /><br />";
	echo "<a href=\"VolunteerManage.php\"> Continue ---></a><br /><br />";
	echo "Notes: ".$notes. "<br /><br /><br /></p>";
	submit_participant_note ($note, $partid);
	staff_footer();

// end echo to the user		
$submitdata = <<<SUBMITDATA
UPDATE TimeCard 
SET voltimeout = ADDTIME('$date_add','$actualtime'),volcheckoutbyid = $_SESSION[badgeid]
WHERE voltimecheckinkey = '$voltimecheckinkey'
SUBMITDATA;
 
	if (!$result4=mysql_query($submitdata,$link)) {
	    $message=$submitdata."<BR>Error querying database. Unable to continue.<BR>";
	    echo "<P class\"errmsg\">".$message."\n";
	    staff_footer();
	    exit();
	}
	}
}

// Move to reports to their own page????
function start_report() {
	echo "<br /><br />I'll show the report list now";
}

// sql query for clocking list.
// SELECT CD.badgeid, CD.badgename, CD.firstname, CD.lastname FROM CongoDump CD, TimeCard TC WHERE CD.badgeid <> TC.badgeid AND TC.voltimeout IS NULL ORDER BY CD.badgename;

//$StaffUser = $_SESSION['badgeid'];
// Picks list of people that are NOT currently logged in.
/* Clock out query
SELECT 
   DISTINCT CD.badgeid, 
   CD.badgename, 
   CD.firstname, 
   CD.lastname 
 FROM
   CongoDump CD 
   JOIN UserHasPermissionRole UP USING (badgeid) 
   JOIN TimeCard TC USING (badgeid)
 WHERE
   TC.voltimein like '%00' AND TC.voltimeout IS NULL
 ORDER BY
   CD.badgename */
function handle_db_error($message) {
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
}

function get_session_user() {
global $link;

// Get Staff user name from $_SESSION[]
$query = <<<SESSIONID
SELECT CD.badgeid, CD.badgename, CD.firstname, CD.lastname
FROM CongoDump CD
WHERE CD.badgeid = '$_SESSION[badgeid]'
LIMIT 1
SESSIONID;

	if (!$result=mysql_query($query,$link)) {
		$message="::: ERROR :::: Unable to continue. -- SQL QUERY --> \"SESSIONID_FUNCTION\" <-- FAULT  <BR>";
		handle_db_error($message);
	} else {
		
		list($staffbadgeid,$staffbadgename,$stafffirstname,$stafflastname)= mysql_fetch_array($result, MYSQL_NUM);
		return array($staffbadgeid,$staffbadgename,$stafffirstname,$stafflastname);
	}
}

// Fix retarded populate_select_from_table() function that just doesn't do what I need AND make
// it more flexible by using assoc arrays names is on the list too.
function populate_select_from_table_alt($table_name, $default_value, $option_0_text, $default_flag) {
    // set $default_value=-1 for no default value (note not really supported by HTML)
    // set $default_value=0 for initial value to be set as $option_0_text
    // otherwise the initial value will be equal to the row whose id == $default_value
    // assumes id's in the table start at 1
    // if $default_flag is true, the option 0 will always appear.
    // if $default_flag is false, the option 0 will only appear when $default_value is 0.
    global $link;
    if ($default_value==0) {
            echo "<OPTION value=0 selected>".$option_0_text."</OPTION>\n";
            }
        elseif ($default_flag) {
            echo "<OPTION value=0>".$option_0_text."</OPTION>\n";
            }            
    $result=mysql_query("Select timeindex, DATE_FORMAT(actualtime,'%l:%i %p') from ".$table_name." order by display_order",$link);
    while ($arow = mysql_fetch_array($result, MYSQL_NUM)) {
        $option_value=$arow[0];
        $option_name=$arow[1];
        echo "<OPTION value=".$option_value." ";
        if ($option_value==$default_value)
            echo "selected";
        echo ">".$option_name."</OPTION>\n";
        }
    }
?>
