<?php
// Function prepare_db()
// Opens database channel
include ('../Local/db_name.php');
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

function prepare_db() {
    global $link;
    $link = mysql_connect(DBHOSTNAME,DBUSERID,DBPASSWORD);
    if ($link===false) return (false);
    return (mysql_select_db(DBDB,$link));
    }
// The table SessionEditHistory has a timestamp column which is automatically set to the
// current timestamp by MySQL. 
function record_session_history($sessionid, $badgeid, $name, $email, $editcode, $statusid) {
    global $link, $message_error;
	$name=mysql_real_escape_string($name,$link);
	$email=mysql_real_escape_string($email,$link);
    $query='';
    $query.="INSERT INTO SessionEditHistory SET ";
    $query.="sessionid=$sessionid, ";
    $query.="badgeid='$badgeid', ";
    $query.="name='$name', ";
    $query.="email_address='$email', ";
    $query.="sessioneditcode=$editcode, ";
    $query.="statusid=$statusid";
    $result = mysql_query($query,$link);
    if (!$result) {
        $message_error=$query."<BR>\n".mysql_error($link);
        return $result;
        }
    return(true);
    }
// Function get_name_and_email(&$name, &$email)
// Gets name and email from db if they are available and not already set
// returns FALSE if error condition encountered.  Error message in global $message_error
function get_name_and_email(&$name, &$email) {
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

    global $link, $message_error, $badgeid;
    if (isset($name) && $name!='') {
        //$name="foo"; //for debugging only
	return(TRUE);
        }
    if (isset($_SESSION['name'])) {
        $name=$_SESSION['name'];
        $email=$_SESSION['email'];
        //error_log("get_name_and_email found a name in the session variables.");
        return(TRUE);
        }
    if (may_I('Staff') || may_I('Participant')) { //name and email should be found in db if either set
        $query="SELECT pubsname from $ReportDB.Participants where badgeid='$badgeid'";
        //error_log($query); //for debugging only
        $result=mysql_query($query,$link);
        if (!$result) {
            $message_error=$query."<BR> ";
            $message_error.=mysql_error($link)."<BR> ";
            $message_error.="Error reading from database. No further execution possible.<BR> ";
            error_log($message_error);
            return(FALSE);
            }
        $name=mysql_result($result, 0);
        if ($name=='') {
            $name=' '; //if name is null or '' in db, set to ' ' so it won't appear unpopulated in query above
            }
        $query="SELECT badgename,email from $ReportDB.CongoDump where badgeid='$badgeid'";
        $result=mysql_query($query,$link);
        if (!$result) {
            $message_error=$query."<BR> ";
            $message_error.=mysql_error($link)."<BR> ";
            $message_error.="Error reading from database. No further execution possible.<BR> ";
            error_log($message_error);
            return(FALSE);
            }
        if ($name==' ') {
            $name=mysql_result($result, 0, 0);
            } // name will be ' ' if pubsname is null.  In that case use badgename.
        $email=mysql_result($result, 0, 1);
        }
    return(TRUE); //return TRUE even if didn't retrieve from db because there's nothing to be done
    }

// Function populate_select_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control.
//
function populate_select_from_table($table_name, $default_value, $option_0_text, $default_flag) {
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
    $result=mysql_query("Select * from ".$table_name." order by display_order",$link);
    while ($arow = mysql_fetch_array($result, MYSQL_NUM)) {
        $option_value=$arow[0];
        $option_name=$arow[1];
        echo "<OPTION value=".$option_value." ";
        if ($option_value==$default_value)
            echo "selected";
        echo ">".$option_name."</OPTION>\n";
        }
    }

// Function populate_select_from_query(...)
// Reads parameters (see below) and a specified query for the db.
// Outputs HTML of the "<OPTION>" values for a Select control.
//
function populate_select_from_query($query, $default_value, $option_0_text, $default_flag) {
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
    $result=mysql_query($query,$link);
    while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
        echo "<OPTION value=".$option_value." ";
        if ($option_value==$default_value)
            echo "selected";
        echo ">".$option_name."</OPTION>\n";
        }
    }

// Function populate_multiselect_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control with
// multiple enabled.
//
function populate_multiselect_from_table($table_name, $skipset) {
    // assumes id's in the table start at 1 '
    // skipset is array of integers of values of id from table to preselect
    // assumes mulit-year element in all useage
    global $link;
    // error_log("Zambia->populate_multiselect_from_table->\$skipset: ".print_r($skipset,TRUE)."\n"); // only for debugging
    if ($skipset=="") $skipset=array(-1);
    $result=mysql_query("SELECT * from ".$table_name." WHERE conid=".$_SESSION['conid']." ORDER BY display_order",$link);
    while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
        echo "<OPTION value=\"".$option_value."\"";
        if (array_search($option_value,$skipset)!==FALSE) {
        echo " selected";
            }
        echo">$option_name</OPTION>\n";    
        }
    }

// Function populate_multisource_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control associated
// with the *source* of an active update box.
//
function populate_multisource_from_table($table_name, $skipset) {
    // assumes id's in the table start at 1 '
    // skipset is array of integers of values of id from table not to include
    // assumes mulit-year element in all useage
    global $link;
    if ($skipset=="") $skipset=array(-1);
    $result=mysql_query("SELECT * from ".$table_name." WHERE conid=".$_SESSION['conid']." ORDER BY display_order",$link);
    while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
        if (array_search($option_value,$skipset)===false) {
        echo "<OPTION value=".$option_value.">".$option_name."</OPTION>\n";
            }
        }
    }

// Function populate_multidest_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control associated
// with the *destination* of an active update box.
//
function populate_multidest_from_table($table_name, $skipset) {
    // assumes id's in the table start at 1                        '
    // skipset is array of integers of values of id from table to include
    // in "dest" because they were skipped from "source"
    // assumes mulit-year element in all useage
    global $link;
    if ($skipset=="") $skipset=array(-1);
    $result=mysql_query("SELECT * from ".$table_name." WHERE conid=".$_SESSION['conid']." ORDER BY display_order",$link);
    while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
        if (array_search($option_value,$skipset)!==false) {
            echo "<OPTION value=".$option_value.">".$option_name."</OPTION>\n";
            }
        }
    }
// Function update_session()
// Takes data from global $session array and updates
// the tables Sessions, SessionHasFeature, SessionHasPubChar, 
// SessionHasService, SessionHasVendorFeature, SessionHasVendorSpace
//
function update_session() {
    global $link, $session, $message2;
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    $query="UPDATE Sessions set ";
    //$query.="conid=".$_SESSION['conid'].", ";
    $query.="trackid=".$session["track"].", ";
    $query.="typeid=".$session["type"].", ";
    $query.="divisionid=".$session["divisionid"].", ";
    $query.="pubstatusid=".$session["pubstatusid"].", ";
    $query.="languagestatusid=".$session["languagestatusid"].", ";
    $query.="pubsno=\"".mysql_real_escape_string($session["pubno"],$link)."\", ";
    $query.="title=\"".mysql_real_escape_string($session["title"],$link)."\", ";
    $query.="secondtitle=\"".mysql_real_escape_string($session["secondtitle"],$link)."\", ";
    $query.="pocketprogtext=\"".mysql_real_escape_string($session["pocketprogtext"],$link)."\", ";
    $query.="progguiddesc=\"".mysql_real_escape_string($session["progguiddesc"],$link)."\", ";
    $query.="persppartinfo=\"".mysql_real_escape_string($session["persppartinfo"],$link)."\", ";
    if (DURATION_IN_MINUTES=="TRUE") {
            $query.="duration=\"".conv_min2hrsmin($session["duration"],$link)."\", ";
            }
        else {
            $query.="duration=\"".mysql_real_escape_string($session["duration"],$link)."\", ";
            }
    $query.="estatten=".($session["atten"]!=""?$session["atten"]:"null").", ";
    $query.="kidscatid=".$session["kids"].", ";
    $query.="signupreq=".($session["signup"]?"1":"0").", ";
    $query.="invitedguest=".($session["invguest"]?"1":"0").", ";
    $query.="roomsetid=".$session["roomset"].", ";
    $query.="notesforpart=\"".mysql_real_escape_string($session["notesforpart"],$link)."\", ";
    $query.="servicenotes=\"".mysql_real_escape_string($session["servnotes"],$link)."\", ";
    $query.="statusid=".$session["status"].", ";
    $query.="notesforprog=\"".mysql_real_escape_string($session["notesforprog"],$link)."\" ";
    $query.=" WHERE sessionid=".$session["sessionid"];
    $message2=$query;
    if (!mysql_query($query,$link)) {
        $message_error.=mysql_error($link);
        $message_error.=" query=$query";
        return $message_error;
        }
    $query="DELETE from SessionHasFeature where sessionid=".$session["sessionid"];
    //$query.=" AND conid=".$_SESSION['conid'];
    $message2=$query;
    if (!mysql_query($query,$link)) {
        $message_error.=mysql_error($link);
	$message_error.=" query=$query";
	return $message_error;
        }
    $id=$session["sessionid"];
    if ($session["featdest"]!="") {
        for ($i=0 ; $session["featdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasFeature set sessionid=".$id.", featureid=";
            $query.=$session["featdest"][$i];
	    //$query.=", conid=".$_SESSION['conid'];
            $message2=$query;
            if (!mysql_query($query,$link)) {
	        $message_error.=mysql_error($link);
	        $message_error.=" query=$query";
	        return $message_error;
                }
            }
        }
    $query="DELETE from SessionHasService where sessionid=".$session["sessionid"];
    //$query.=" AND conid=".$_SESSION['conid'];
    $message2=$query;
    if (!mysql_query($query,$link)) {
        $message_error.=mysql_error($link);
	$message_error.=" query=$query";
	return $message_error;
        }
    if ($session["servdest"]!="") {
        for ($i=0 ; $session["servdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasService set sessionid=".$id.", serviceid=";
            $query.=$session["servdest"][$i];
	    //$query.=", conid=".$_SESSION['conid'];
            $message2=$query;
            if (!mysql_query($query,$link)) {
	        $message_error.=mysql_error($link);
	        $message_error.=" query=$query";
	        return $message_error;
                }
            }
        }
    $query="DELETE from SessionHasPubChar where sessionid=".$session["sessionid"];
    //$query.=" AND conid=".$_SESSION['conid'];
    $message2=$query;
    if (!mysql_query($query,$link)) {
        $message_error.=mysql_error($link);
	$message_error.=" query=$query";
	return $message_error;
        }
    if ($session["pubchardest"]!="") {
        //error_log("Zamiba->update_session->\$session[\"pubchardest\"]: ".print_r($session["pubchardest"],TRUE)); // for debugging only
        for ($i=0 ; $session["pubchardest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasPubChar set sessionid=".$id.", pubcharid=";
            $query.=$session["pubchardest"][$i];
	    //$query.=", conid=".$_SESSION['conid'];
            $message2=$query;
            if (!mysql_query($query,$link)) {
	        $message_error.=mysql_error($link);
	        $message_error.=" query=$query";
	        return $message_error;
                }
            }
        }
    $query="DELETE from $ReportDB.SessionHasVendorFeature where sessionid=".$session["sessionid"];
    $query.=" AND conid=".$_SESSION['conid'];
    $message2=$query;
    if (!mysql_query($query,$link)) {
        $message_error.=mysql_error($link);
	$message_error.=" query=$query";
	return $message_error;
        }
    $id=$session["sessionid"];
    if ($session["vendfeatdest"]!="") {
        for ($i=0 ; $session["vendfeatdest"][$i]!="" ; $i++ ) {
            $query="INSERT into $ReportDB.SessionHasVendorFeature set sessionid=".$id.", vendorfeatureid=";
            $query.=$session["vendfeatdest"][$i];
	    $query.=", conid=".$_SESSION['conid'];
            $message2=$query;
            if (!mysql_query($query,$link)) {
	        $message_error.=mysql_error($link);
	        $message_error.=" query=$query";
	        return $message_error;
                }
            }
        }
    $query="DELETE from $ReportDB.SessionHasVendorSpace where sessionid=".$session["sessionid"];
    $query.=" AND conid=".$_SESSION['conid'];
    $message2=$query;
    if (!mysql_query($query,$link)) {
        $message_error.=mysql_error($link);
	$message_error.=" query=$query";
	return $message_error;
        }
    $id=$session["sessionid"];
    if ($session["vendorspace"]!=0) {
        $query="INSERT into $ReportDB.SessionHasVendorSpace set sessionid=".$id.", vendorspaceid=";
	$query.=$session["vendorspace"];
	$query.=", conid=".$_SESSION['conid'];
        $message2=$query;
        if (!mysql_query($query,$link)) {
	    $message_error.=mysql_error($link);
	    $message_error.=" query=$query";
	    return $message_error;
            }
        }
    $query="DELETE from $ReportDB.SessionHasVendorAdjust where sessionid=".$session["sessionid"];
    $query.=" AND conid=".$_SESSION['conid'];
    $message2=$query;
    if (($session["vendoradjustvalue"]!="") or ($session["vendoradjustnote"]!="")) {
        $query="INSERT into $ReportDB.SessionHasVendorAdjust set sessionid=".$id." ";
        if ($session["vendoradjustvalue"]!="") {
	    $query.=", vendoradjustvalue=";
	    $query.=$session["vendoradjustvalue"]." ";
  	    }
        if ($session["vendoradjustnote"]!="") {
	    $query.=", vendoradjustnote='";
	    $query.=$session["vendoradjustnote"]."' ";
	    }
        $query.=", conid=".$_SESSION['conid'];
        $result = mysql_query($query,$link);
	if (!$result) {
	    $message_error.=mysql_error($link);
	    $message_error.=" query=$query";
	    return $message_error;
	    }
        }
    return true;
    }

// Function get_next_session_id()
// Reads Session table from db to determine next unused value
// of sessionid.
//
function get_next_session_id() {
    global $link;
    $result=mysql_query("SELECT MAX(sessionid) FROM Sessions",$link);
    if (!$result) {return "";}
    list($maxid)=mysql_fetch_array($result, MYSQL_NUM);
    if (!$maxid) {return "1";}
    return $maxid+1;
    }

// Function insert_session()
// Takes data from global $session array and creates new rows in
// the tables Sessions, SessionHasFeature, SessionHasService,
// SessionHasPubChar, SessionHasVendorFeature, and SessionHasVendorSpace
//
function insert_session() {
    global $session, $link, $query, $message_error;
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    $query="INSERT into Sessions set ";
    //$query.="conid=".$_SESSION['conid'].", ";
    $query.="trackid=".$session["track"].',';
    $temp=$session["type"];
    $query.="typeid=".(($temp==0)?"null":$temp).", ";
    $temp=$session["divisionid"];
    $query.="divisionid=".(($temp==0)?"null":$temp).", ";
    $query.="pubstatusid=".$session["pubstatusid"].',';
    $query.="languagestatusid=".$session["languagestatusid"].',';
    $query.="pubsno=\"".mysql_real_escape_string($session["pubno"],$link).'",';
    $query.="title=\"".mysql_real_escape_string($session["title"],$link).'",';
    $query.="secondtitle=\"".mysql_real_escape_string($session["secondtitle"],$link).'",';
    $query.="pocketprogtext=\"".mysql_real_escape_string($session["pocketprogtext"],$link).'",';
    $query.="progguiddesc=\"".mysql_real_escape_string($session["progguiddesc"],$link).'",';
    $query.="persppartinfo=\"".mysql_real_escape_string($session["persppartinfo"],$link).'",';
    if (DURATION_IN_MINUTES=="TRUE") {
            $query.="duration=\"".conv_min2hrsmin($session["duration"],$link)."\", ";
            }
        else {
            $query.="duration=\"".mysql_real_escape_string($session["duration"],$link)."\", ";
            }
    $query.="estatten=".($session["atten"]!=""?$session["atten"]:"null").',';
    $query.="kidscatid=".$session["kids"].',';
    $query.="signupreq=";
    if ($session["signup"]) {$query.="1,";} else {$query.="0,";}
    $temp=$session["roomset"];
    $query.="roomsetid=".(($temp==0)?"null":$temp).", ";
    $query.="notesforpart=\"".mysql_real_escape_string($session["notesforpart"],$link).'",';
    $query.="servicenotes=\"".mysql_real_escape_string($session["servnotes"],$link).'",';
    $query.="statusid=".$session["status"].',';
    $query.="notesforprog=\"".mysql_real_escape_string($session["notesforprog"],$link).'",';
    $query.="warnings=0,invitedguest="; // warnings db field not editable by form
    if ($session["invguest"]) {$query.="1";} else {$query.="0";}
    $result = mysql_query($query,$link);
    if (!$result) {
        $message_error.=mysql_error($link);
        $message_error.=" query=$query";
        return $message_error;
        }
    $id = mysql_insert_id($link);
    if ($session["featdest"]!="") {
        for ($i=0 ; $session["featdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasFeature set sessionid=".$id.", featureid=";
            $query.=$session["featdest"][$i];
	    //$query.=", conid=".$_SESSION['conid'];
            $result = mysql_query($query,$link);
	    if (!$result) {
	        $message_error.=mysql_error($link);
		$message_error.=" query=$query";
	        return $message_error;
	        }
            }
        }
    if ($session["servdest"]!="") {
        for ($i=0 ; $session["servdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasService sessionid=".$id.", serviceid=";
            $query.=$session["servdest"][$i];
	    //$query.=", conid=".$_SESSION['conid'];
            $result = mysql_query($query,$link);
	    if (!$result) {
	        $message_error.=mysql_error($link);
		$message_error.=" query=$query";
	        return $message_error;
	        }
            }
        }
    if ($session["pubchardest"]!="") {
        for ($i=0 ; $session["pubchardest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasPubChar sessionid=".$id.", pubcharid=";
            $query.=$session["pubchardest"][$i];
	    //$query.=", conid=".$_SESSION['conid'];
            $result = mysql_query($query,$link);
	    if (!$result) {
	        $message_error.=mysql_error($link);
		$message_error.=" query=$query";
	        return $message_error;
	        }
            }
        }
    if ($session["vendfeatdest"]!="") {
        for ($i=0 ; $session["vendfeatdest"][$i]!="" ; $i++ ) {
            $query="INSERT into $ReportDB.SessionHasVendorFeature set sessionid=".$id.", vendorfeatureid=";
            $query.=$session["vendfeatdest"][$i];
	    $query.=", conid=".$_SESSION['conid'];
            $result = mysql_query($query,$link);
	    if (!$result) {
	        $message_error.=mysql_error($link);
		$message_error.=" query=$query";
	        return $message_error;
	        }
            }
        }
    if ($session["vendorspace"]!=0) {
        $query="INSERT into $ReportDB.SessionHasVendorSpace set sessionid=".$id.", vendorspaceid=";
        $query.=$session["vendorspace"];
        $query.=", conid=".$_SESSION['conid'];
        $result = mysql_query($query,$link);
	if (!$result) {
	    $message_error.=mysql_error($link);
	    $message_error.=" query=$query";
	    return $message_error;
	    }
        }
    if (($session["vendoradjustvalue"]!="") or ($session["vendoradjustnote"]!="")) {
        $query="INSERT into $ReportDB.SessionHasVendorAdjust set sessionid=".$id." ";
        if ($session["vendoradjustvalue"]!="") {
	    $query.=", vendoradjustvalue=";
	    $query.=$session["vendoradjustvalue"]." ";
  	    }
        if ($session["vendoradjustnote"]!="") {
	    $query.=", vendoradjustnote='";
	    $query.=$session["vendoradjustnote"]."' ";
	    }
        $query.=", conid=".$_SESSION['conid'];
        $result = mysql_query($query,$link);
	if (!$result) {
	    $message_error.=mysql_error($link);
	    $message_error.=" query=$query";
	    return $message_error;
	    }
        }
    return $id;
    }

// Function retrieve_session_from_db()
// Reads Sessions, SessionHasFeature, and SessionHasService tables
// from db to populate global array $session.
//
function retrieve_session_from_db($sessionid) {
    global $session;
    global $link,$message2;
    $query= <<<EOD
select
        sessionid, trackid, typeid, divisionid, pubstatusid, languagestatusid, pubsno,
        title, secondtitle, pocketprogtext, progguiddesc, persppartinfo, duration,
        estatten, kidscatid, signupreq, roomsetid, notesforpart, servicenotes,
        statusid, notesforprog, warnings, invitedguest, ts
    from
        Sessions
    where
        sessionid=
EOD;
    $query.=$sessionid;
    $result=mysql_query($query,$link);
    if (!$result) {
        $message2=$query."<BR>\n".mysql_error($link);
        return (-3);
        }
    $rows=mysql_num_rows($result);
    if ($rows!=1) {
        $message2=$rows;
        return (-2);
        }
    $sessionarray=mysql_fetch_array($result, MYSQL_ASSOC);
    $session["sessionid"]=$sessionarray["sessionid"];
    $session["track"]=$sessionarray["trackid"];
    $session["type"]=$sessionarray["typeid"];
    $session["divisionid"]=$sessionarray["divisionid"];
    $session["pubstatusid"]=$sessionarray["pubstatusid"];
    $session["languagestatusid"]=$sessionarray["languagestatusid"];
    $session["pubno"]=$sessionarray["pubsno"];
    $session["title"]=$sessionarray["title"];
    $session["secondtitle"]=$sessionarray["secondtitle"];
    $session["pocketprogtext"]=$sessionarray["pocketprogtext"];
    $session["progguiddesc"]=$sessionarray["progguiddesc"];
    $session["persppartinfo"]=$sessionarray["persppartinfo"];
    $timearray=parse_mysql_time_hours($sessionarray["duration"]);
    if (DURATION_IN_MINUTES=="TRUE") {
            $session["duration"]=" ".strval(60*$timearray["hours"]+$timearray["minutes"]);
            }
        else {
            $session["duration"]=" ".$timearray["hours"].":".sprintf("%02d",$timearray["minutes"]);
            }
    $session["atten"]=$sessionarray["estatten"];
    $session["kids"]=$sessionarray["kidscatid"];
    $session["signup"]=$sessionarray["signupreq"];
    $session["roomset"]=$sessionarray["roomsetid"];
    $session["notesforpart"]=$sessionarray["notesforpart"];
    $session["servnotes"]=$sessionarray["servicenotes"];
    $session["status"]=$sessionarray["statusid"];
    $session["notesforprog"]=$sessionarray["notesforprog"];
    $session["invguest"]=$sessionarray["invitedguest"];
    $result=mysql_query("SELECT featureid FROM SessionHasFeature where sessionid=".$sessionid,$link);
    if (!$result) {
        $message2=mysql_error($link);
        return (-3);
        }
    unset($session["featdest"]);
    while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
        $session["featdest"][]=$row[0];
        }
    $result=mysql_query("SELECT serviceid FROM SessionHasService where sessionid=".$sessionid,$link);
    if (!$result) {
        $message2=mysql_error($link);
        return (-3);
        }
    unset($session["servdest"]);
    while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
        $session["servdest"][]=$row[0];
        }
    $result=mysql_query("SELECT pubcharid FROM SessionHasPubChar where sessionid=".$sessionid,$link);
    if (!$result) {
        $message2=mysql_error($link);
        return (-3);
        }
    unset($session["pubchardest"]);
    while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
        $session["pubchardest"][]=$row[0];
        }
    return (37);
    }

// Function isLoggedIn()
// Reads the session variables and checks password in db to see if user is
// logged in.  Returns true if logged in or false if not.  Assumes db already
// connected on $link.

/* The script will check login status.  If user is logged in
   it will pass control to script (???) to implement edit my contact info.
   If user not logged in, it will pass control to script (???) to
   log user in. */
/* check login script, included in db_connect.php. */

function isLoggedIn() {
    global $link,$message2;
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    if (!isset($_SESSION['badgeid']) || !isset($_SESSION['password'])) {
        return false;
        }

// remember, $_SESSION['password'] will be encrypted.

    if(!get_magic_quotes_gpc()) { //get global configuration setting
        $_SESSION['badgeid'] = addslashes($_SESSION['badgeid']);
        }
// addslashes to session username before using in a query.

    $result=mysql_query("SELECT password FROM $ReportDB.Participants where badgeid='".$_SESSION['badgeid']."'",$link);
    if (!$result) {
        $message2=mysql_error($link);
        unset($_SESSION['badgeid']);
        unset($_SESSION['password']);
// kill incorrect session variables.
        return (-3);
        }

    if (mysql_num_rows($result)!=1) {
        unset($_SESSION['badgeid']);
        unset($_SESSION['password']);
// kill incorrect session variables.
        $message2="Incorrect number of rows returned when fetching password from db.";
        return (-1);
        }

    $row=mysql_fetch_array($result, MYSQL_NUM);
    $db_pass = $row[0];

// now we have encrypted pass from DB in
//$db_pass['password'], stripslashes() just incase:

    $db_pass = stripslashes($db_pass);
    $_SESSION['password'] = stripslashes($_SESSION['password']);

    //echo $db_pass."<BR>";
    //echo $_SESSION['password']."<BR>";

//compare:

    if($_SESSION['password'] != $db_pass) {
// kill incorrect session variables.
            unset($_SESSION['badgeid']);
            unset($_SESSION['password']);
            $message2="Incorrect userid or password.";
            return (false);
            }
// valid password for username
        else {
//          $i=set_permission_set($_SESSION['badgeid']);
//          should now be part of session variables
//            if ($i!=0) {
//                error_log("Zambia: permission_set error $i\n");
//                }
            return(true); // they have correct info
            }           // in session variables.
    }


// Function retrieve_participant_from_db()
// Reads Particpants tables
// from db to populate global array $participant.
//
function retrieve_participant_from_db($badgeid) {
    global $participant;
    global $link,$message2;
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    $result=mysql_query("SELECT pubsname, password FROM $ReportDB.Participants where badgeid='$badgeid'",$link);
    if (!$result) {
        $message2=mysql_error($link);
        return (-3);
        }
    $rows=mysql_num_rows($result);
    if ($rows!=1) {
        $message2="Participant rows retrieved: $rows ";
        return (-2);
        }
    $participant=mysql_fetch_array($result, MYSQL_ASSOC);
    return (0);
    }
// Function getCongoData()
// Reads $ReportDB.CongoDump table
// from db to populate global array $congoinfo.
//
function getCongoData($badgeid) {
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

    global $message_error,$message2,$congoinfo,$link;
    $query= <<<EOD
SELECT
        badgeid,
	firstname,
	lastname,
	badgename,
	phone,
	email,
	postaddress1,
	postaddress2,
	postcity,
	poststate,
	postzip,
	postcountry
    FROM
        $ReportDB.CongoDump
    WHERE
        badgeid="$badgeid"
EOD;
    $result=mysql_query($query,$link);
    if (!$result) {
        $message_error=mysql_error($link)."\n<BR>Database Error.<BR>No further execution possible.";
        return(-1);
        };
    $rows=mysql_num_rows($result);
    if ($rows!=1) {
        $message_error=$rows." rows returned for badgeid when 1 expected.<BR>Database Error.<BR>No further execution possible.";
        return(-1);
        };
    if (retrieve_participant_from_db($badgeid)!=0) {
        $message_error=$message2."<BR>In Congo but not in Participants, no further execution possible.";
        return(-1);
        };
    $participant["password"]="";
    $congoinfo=mysql_fetch_array($result, MYSQL_ASSOC);
    return(0);
    }
// Function retrieve_participantAvailability_from_db()
// Reads ParticipantAvailability and ParticipantAvailabilityTimes tables
// from db to populate global array $partAvail.
// Returns 0: success; -1: badgeid not found; -2: badgeid matches >1 row;
//         -3: other error ($message_error populated)
//
function retrieve_participantAvailability_from_db($badgeid) {
    global $partAvail;
    global $link,$message2,$message_error;
    $query= <<<EOD
Select badgeid, maxprog, preventconflict, otherconstraints, numkidsfasttrack FROM ParticipantAvailability
EOD;
    $query.=" where badgeid=\"$badgeid\"";
    $result=mysql_query($query,$link);
    if (!$result) {
        $message_error=$query."<BR>\n".mysql_error($link);
        return (-3);
        }
    $rows=mysql_num_rows($result);
    if ($rows==0) {
        return (-1);
        }
    if ($rows!=1) {
        $message_error=$query."<BR>\n returned $rows rows.";
        return (-2);
        }
    $partAvailarray=mysql_fetch_array($result, MYSQL_NUM);
    $partAvail["badgeid"]=$partAvailarray[0];
    $partAvail["maxprog"]=$partAvailarray[1];
    $partAvail["preventconflict"]=$partAvailarray[2];
    $partAvail["otherconstraints"]=$partAvailarray[3];
    $partAvail["numkidsfasttrack"]=$partAvailarray[4];

    if (CON_NUM_DAYS>1) {
        $query="SELECT badgeid, day, maxprog FROM ParticipantAvailabilityDays where badgeid=\"$badgeid\"";
        $result=mysql_query($query,$link);
        if (!$result) {
            $message_error=$query."<BR>\n".mysql_error($link);
            return (-3);
            }
        for ($i=1; $i<=CON_NUM_DAYS; $i++) {
            unset($partAvail["maxprogday$i"]);
            }
        if (mysql_num_rows($result)>0) {
            while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
                $i=$row[1];
                $partAvail["maxprogday$i"]=$row[2];
                }
            }
        }
    $query="SELECT badgeid, availabilitynum, starttime, endtime FROM ParticipantAvailabilityTimes ";
    $query.="where badgeid=\"$badgeid\" order by starttime";
    $result=mysql_query($query,$link);
    if (!$result) {
        $message_error=$query."<BR>\n".mysql_error($link);
        return (-3);
        }
    for ($i=1; $i<=AVAILABILITY_ROWS; $i++) {
        unset($partAvail["starttimestamp_$i"]);
        unset($partAvail["endtimestamp_$i"]);
        }
    $i=1;
    while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
        $partAvail["starttimestamp_$i"]=$row[2];
        $partAvail["endtimestamp_$i"]=$row[3];
        $i++;
        }
    return (0);
    }
//
// Function set_permission_set($badgeid)
// Performs complicated join to get the set of permission atoms available to the user
// Stores them in global variable $permission_set
//
function set_permission_set($badgeid) {
    global $link;
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    
// First do simple permissions
    $_SESSION['permission_set']="";
    $conid=$_SESSION['conid'];
    $query= <<<EOD
SELECT
    DISTINCT permatomtag
  FROM 
      $ReportDB.Phase as PH,
      Permissions P
    JOIN $ReportDB.PermissionAtoms USING (permatomid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (permroleid)
  WHERE
    ((UHPR.badgeid='$badgeid' AND UHPR.conid=$conid) OR P.badgeid='$badgeid' ) AND
    (P.phaseid is null OR (P.phaseid = PH.phasetypeid AND PH.phasestate = TRUE and PH.conid=$conid))
EOD;
    $result=mysql_query($query,$link);
//    error_log("set_permission_set query:  ".$query);
    if (!$result) {
        $message_error=$query." \n ".mysql_error($link)." \n <BR>Database Error.<BR>No further execution possible.";
        error_log("Zambia: ".$message_error);
        return(-1);
        };
    $rows=mysql_num_rows($result);
    if ($rows==0) {
        return(0);
        };
    for ($i=0; $i<$rows; $i++) {
        $onerow=mysql_fetch_array($result, MYSQL_BOTH);
        $_SESSION['permission_set'][]=$onerow[0];
        };
// Second, do <<specific>> permissions
    $_SESSION['permission_set_specific']="";
    $query= <<<EOD
    Select distinct permatomtag, elementid from $ReportDB.PermissionAtoms as PA, $ReportDB.Phase as PH,
    $ReportDB.PermissionRoles as PR, $ReportDB.UserHasPermissionRole as UHPR, Permissions P where
    ((UHPR.badgeid='$badgeid' and UHPR.permroleid = P.permroleid and UHPR.conid=$conid)
        or P.badgeid='$badgeid' ) and
    (P.phaseid is null or (P.phaseid = PH.phasetypeid and PH.phasestate = TRUE and PH.conid=$conid)) and
    P.permatomid = PA.permatomid and
    PA.elementid is not null
EOD;
    $result=mysql_query($query,$link);
    if (!$result) {
        $message_error=$query." \n ".mysql_error($link)." \n <BR>Database Error.<BR>No further execution possible.";
        error_log("Zambia: ".$message_error);
        return(-1);
        };
    $rows=mysql_num_rows($result);
    if ($rows==0) {
        return(0);
        };
    for ($i=0; $i<$rows; $i++) {
        $_SESSION['permission_set_specific'][]=mysql_fetch_array($result, MYSQL_ASSOC);
        };

    return(0);
    }

//function db_error($title,$query,$staff)
//Populates a bunch of messages to help diagnose a db error

function db_error($title,$query,$staff) {
    global $link;
    $message="Database error.<BR>\n";
    $message.=mysql_error($link)."<BR>\n";
    $message.=$query."<BR>\n";
    RenderError($title,$message);
    }

//function get_idlist_from_db($table_name,$id_col_name,$desc_col_name,$desc_col_match);
// Returns a string with a list of id's from a configuration table

function get_idlist_from_db($table_name,$id_col_name,$desc_col_name,$desc_col_match) {
    global $link;
//    error_log("zambia - get_idlist_from_db: desc_col_match: $desc_col_match");
    $query = "SELECT GROUP_CONCAT($id_col_name) from $table_name where ";
    $query.= "$desc_col_name in ($desc_col_match)";
//    error_log("zambia - get_idlist_from_db: query: $query");
    $result=mysql_query($query,$link);
    return(mysql_result($result,0));
    }

//function unlock_participant($badgeid);
//Removes all locks from participant table for participant in parameter
//and all locks held by the user known from the session
//call with $badgeid='' to unlock based on user only

function unlock_participant($badgeid) {
  global $query,$link;
  $query="UPDATE $BioDB.Bios SET biolockedby=NULL WHERE ";
  if (isset($_SESSION['badgeid'])) {
    $query.="biolockedby='".$_SESSION['badgeid']."'";
    if ($badgeid!='') {
      $query.=" and badgeid='$badgeid'";
    }
  } else {
    if ($badgeid!='') {
      $query.="badgeid='$badgeid'";
    } else {
      return($query.": Nothing to unlock"); //can't find anything to unlock
    }
  }
  //error_log("Zambia: unlock_participants: ".$query);
  $result=mysql_query($query,$link);
  if (!$result) {
    return ($query.": -1");
  } else {
    return ($query.": 0");
  }
}

function lock_participant($badgeid) {
  global $query, $link;
  //error_log("Zambia: lock_participant: ".$query);
  $userbadgeid=$_SESSION['badgeid'];
  $query="UPDATE $BioDB.Bios SET biolockedby='$userbadgeid' WHERE biolockedby IS NULL and badgeid='$badgeid'";

  $result=mysql_query($query,$link);
  if (!$result) {
    return (-1);
  }
  if (mysql_affected_rows($link) > 0) {
    return (0);
  } else {
    return (-2);
  }
}

// Function get_sstatus()
// Populates the global sstatus array from the database

function get_sstatus() {
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  global $link, $sstatus;
  $query = "SELECT statusid, may_be_scheduled, validate from $ReportDB.SessionStatuses";
  $result=mysql_query($query,$link);
  while ($arow = mysql_fetch_array($result, MYSQL_ASSOC)) {
    $statusid=$arow['statusid'];
    $may_be_scheduled=($arow['may_be_scheduled']==1?1:0);
    $validate=($arow['validate']==1?1:0);
    $sstatus[$statusid]=array('may_be_scheduled'=>$may_be_scheduled, 'validate'=>$validate);
  }
}
?>
