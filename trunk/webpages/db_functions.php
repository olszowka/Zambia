<?php
// Function prepare_db()
// Opens database channel
include ('db_name.php');

function prepare_db() {
    global $link;
    $link = mysql_connect(DBHOSTNAME,DBUSERID,DBPASSWORD);
    if ($link===false) return (false);
    return (mysql_select_db(DBDB,$link));
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
    while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
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

// Function populate_multisource_from_table(...)
// Reads parameters (see below) and a specified table from the db.
// Outputs HTML of the "<OPTION>" values for a Select control associated
// with the *source* of an active update box.
//
function populate_multisource_from_table($table_name, $skipset) {
    // assumes id's in the table start at 1 '
    // skipset is array of integers of values of id from table not to include
    global $link;
    if ($skipset=="") $skipset=array(-1);
    $result=mysql_query("Select * from ".$table_name." order by display_order",$link);
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
    global $link;
    if ($skipset=="") $skipset=array(-1);
    $result=mysql_query("Select * from ".$table_name." order by display_order",$link);
    while (list($option_value,$option_name)= mysql_fetch_array($result, MYSQL_NUM)) {
        if (array_search($option_value,$skipset)!==false) {
            echo "<OPTION value=".$option_value.">".$option_name."</OPTION>\n";
            }
        }
    }
// Function update_session()
// Takes data from global $session array and updates
// the tables Sessions, SessionHasFeature, and SessionHasService.
//
function update_session() {
    global $link, $session, $message2;
    $query="UPDATE Sessions set ";
    $query.="trackid=".$session["track"].", ";
    $query.="typeid=".$session["type"].", ";
    $query.="divisionid=".$session["divisionid"].", ";
    $query.="pubstatusid=".$session["pubstatusid"].", ";
    $query.="pubsno=\"".mysql_real_escape_string($session["pubno"],$link)."\", ";
    $query.="title=\"".mysql_real_escape_string($session["title"],$link)."\", ";
    $query.="pocketprogtext=\"".mysql_real_escape_string($session["pocketprogtext"],$link)."\", ";
    $query.="progguiddesc=\"".mysql_real_escape_string($session["progguiddesc"],$link)."\", ";
    $query.="persppartinfo=\"".mysql_real_escape_string($session["persppartinfo"],$link)."\", ";
    $query.="duration=\"".mysql_real_escape_string($session["duration"],$link)."\", ";
    $query.="estatten=".$session["atten"].", ";
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
    if (!mysql_query($query,$link)) { return false; }
    $query="DELETE from SessionHasFeature where sessionid=".$session["sessionid"];
    $message2=$query;
    if (!mysql_query($query,$link)) { return false; }
    $id=$session["sessionid"];
    if ($session["featdest"]!="") {
        for ($i=0 ; $session["featdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasFeature set sessionid=".$id.", featureid=";
            $query.=$session["featdest"][$i];
            $message2=$query;
            if (!mysql_query($query,$link)) { return false; }
            }
        }
    $query="DELETE from SessionHasService where sessionid=".$session["sessionid"];
    $message2=$query;
    if (!mysql_query($query,$link)) { return false; }
    if ($session["servdest"]!="") {
        for ($i=0 ; $session["servdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasService set sessionid=".$id.", serviceid=";
            $query.=$session["servdest"][$i];
            $message2=$query;
            if (!mysql_query($query,$link)) { return false; }
            }
        }
    $query="DELETE from SessionHasPubChar where sessionid=".$session["sessionid"];
    $message2=$query;
    if (!mysql_query($query,$link)) { return false; }
    if ($session["pubchardest"]!="") {
        for ($i=0 ; $session["pubchardest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasPubChar set sessionid=".$id.", pubcharid=";
            $query.=$session["pubchardest"][$i];
            $message2=$query;
            if (!mysql_query($query,$link)) { return false; }
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
// the tables Sessions, SessionHasFeature, and SessionHasService.
//
function insert_session() {
    global $session, $link, $query;
    $query="INSERT into Sessions set ";
    $query.="trackid=".$session["track"].',';
    $query.="typeid=".$session["type"].',';
    $query.="divisionid=".$session["divisionid"].',';
    $query.="pubstatusid=".$session["pubstatusid"].',';
    $query.="pubsno=\"".mysql_real_escape_string($session["pubno"],$link).'",';
    $query.="title=\"".mysql_real_escape_string($session["title"],$link).'",';
    $query.="pocketprogtext=\"".mysql_real_escape_string($session["pocketprogtext"],$link).'",';
    $query.="progguiddesc=\"".mysql_real_escape_string($session["progguiddesc"],$link).'",';
    $query.="persppartinfo=\"".mysql_real_escape_string($session["persppartinfo"],$link).'",';
    $query.="duration=\"".mysql_real_escape_string($session["duration"],$link).'",';
    $query.="estatten=".$session["atten"].',';
    $query.="kidscatid=".$session["kids"].',';
    $query.="signupreq=";
    if ($session["signup"]) {$query.="1,";} else {$query.="0,";}
    $query.="roomsetid=".$session["roomset"].',';
    $query.="notesforpart=\"".mysql_real_escape_string($session["notesforpart"],$link).'",';
    $query.="servicenotes=\"".mysql_real_escape_string($session["servnotes"],$link).'",';
    $query.="statusid=".$session["status"].',';
    $query.="notesforprog=\"".mysql_real_escape_string($session["notesforprog"],$link).'",';
    $query.="warnings=0,invitedguest="; // warnings db field not editable by form
    if ($session["invguest"]) {$query.="1";} else {$query.="0";}
    $result = mysql_query($query,$link);
    if (!$result)
        return $result;
    $id = mysql_insert_id($link);
    if ($session["featdest"]!="") {
        for ($i=0 ; $session["featdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasFeature set sessionid=".$id.", featureid=";
            $query.=$session["featdest"][$i];
            $result = mysql_query($query,$link);
            }
        }
    if ($session["servdest"]!="") {
        for ($i=0 ; $session["servdest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasService sessionid=".$id.", serviceid=";
            $query.=$session["servdest"][$i];
            $result = mysql_query($query,$link);
            }
        }
    if ($session["pubchardest"]!="") {
        for ($i=0 ; $session["pubchardest"][$i]!="" ; $i++ ) {
            $query="INSERT into SessionHasPubChar sessionid=".$id.", pubcharid=";
            $query.=$session["pubchardest"][$i];
            $result = mysql_query($query,$link);
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
    $result=mysql_query("SELECT * FROM Sessions where sessionid=".$sessionid,$link);
    if (!$result) {
        $message2=mysql_error($link);
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
    $session["pubno"]=$sessionarray["pubsno"];
    $session["title"]=$sessionarray["title"];
    $session["pocketprogtext"]=$sessionarray["pocketprogtext"];
    $session["progguiddesc"]=$sessionarray["progguiddesc"];
    $session["persppartinfo"]=$sessionarray["persppartinfo"];
    $session["duration"]=$sessionarray["duration"];
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

function isLoggedIn($firsttime) {
    global $link,$message2;
    if ($firsttime) {
        session_start();
        $_SESSION['sessionstarted']=1;
        }

    if (!isset($_SESSION['badgeid']) || !isset($_SESSION['password'])) {
        return false;
        }

// remember, $_SESSION['password'] will be encrypted.

    if(!get_magic_quotes_gpc()) { //get global configuration setting
        $_SESSION['badgeid'] = addslashes($_SESSION['badgeid']);
        }
// addslashes to session username before using in a query.

    $result=mysql_query("SELECT password FROM Participants where badgeid='".$_SESSION['badgeid']."'",$link);
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
    $result=mysql_query("SELECT pubsname, password, bestway, interested, bio FROM Participants where badgeid=".$badgeid,$link);
    if (!$result) {
        $message2=mysql_error($link);
        return (-3);
        }
    $rows=mysql_num_rows($result);
    if ($rows!=1) {
        $message2=$rows;
        return (-2);
        }
    $participant=mysql_fetch_array($result, MYSQL_ASSOC);
    return (0);
    }
// Function getCongoData()
// Reads CongoDump table
// from db to populate global array $congoinfo.
//
function getCongoData($badgeid) {
    global $message_error,$message2,$congoinfo,$link;
    $result=mysql_query("Select badgeid,firstname,lastname,badgename,phone,email,postaddress from CongoDump where badgeid='".$badgeid."'",$link);
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
        $message_error=$message2."<BR>No further execution possible.";
        return(-1);
        };
    $participant["password"]="";
    $congoinfo=mysql_fetch_array($result, MYSQL_ASSOC);
    return(0);
    }
// Function retrieve_participantAvailability_from_db()
// Reads ParticipantAvailability and ParticipantAvailabilityTimes tables
// from db to populate global array $partAvail.
//
function retrieve_participantAvailability_from_db($badgeid) {
    global $partAvail;
    global $link,$message2;
    $result=mysql_query("SELECT * FROM ParticipantAvailability where badgeid=\"".$badgeid."\"",$link);
    if (!$result) {
        $message2=mysql_error($link);
        return (-3);
        }
    $rows=mysql_num_rows($result);
    if ($rows!=1) {
        $message2=$rows;
        return (-2);
        }
    $partAvailarray=mysql_fetch_array($result, MYSQL_NUM);
    $partAvail["badgeid"]=$partAvailarray[0];
    $partAvail["fridaymaxprog"]=$partAvailarray[1];
    $partAvail["saturdaymaxprog"]=$partAvailarray[2];
    $partAvail["sundaymaxprog"]=$partAvailarray[3];
    $partAvail["maxprog"]=$partAvailarray[4];
    $partAvail["preventconflict"]=$partAvailarray[5];
    $partAvail["otherconstraints"]=$partAvailarray[6];
    $partAvail["numkidsfasttrack"]=$partAvailarray[7];
    $result=mysql_query("SELECT * FROM ParticipantAvailabilityTimes where badgeid=\"".$badgeid."\" order by starttime",$link);
    if (!$result) {
        $message2=mysql_error($link);
        return (-3);
        }
    unset($partAvail["availtimes"]);
    while ($row=mysql_fetch_array($result, MYSQL_NUM)) {
        $partAvail["availtimes"][]=$row;
        }
    return (0);
    }
// Function set_permission_set($badgeid)
// Performs complicated join to get the set of permission atoms available to the user
// Stores them in global variable $permission_set
//
function set_permission_set($badgeid) {
    global $link;
    
// First do simple permissions
    $_SESSION['permission_set']="";
    $query= <<<EOD
    Select distinct permatomtag from PermissionAtoms as PA, Phases as PH,
    PermissionRoles as PR, UserHasPermissionRole as UHPR, Permissions P where
    ((UHPR.badgeid='$badgeid' and UHPR.permroleid = P.permroleid)
        or P.badgeid='$badgeid' ) and
    (P.phaseid is null or (P.phaseid = PH.phaseid and PH.current = TRUE)) and
    P.permatomid = PA.permatomid
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
        $onerow=mysql_fetch_array($result, MYSQL_BOTH);
        $_SESSION['permission_set'][]=$onerow[0];
        };
// Second, do <<specific>> permissions
    $_SESSION['permission_set_specific']="";
    $query= <<<EOD
    Select distinct permatomtag, elementid from PermissionAtoms as PA, Phases as PH,
    PermissionRoles as PR, UserHasPermissionRole as UHPR, Permissions P where
    ((UHPR.badgeid='$badgeid' and UHPR.permroleid = P.permroleid)
        or P.badgeid='$badgeid' ) and
    (P.phaseid is null or (P.phaseid = PH.phaseid and PH.current = TRUE)) and
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

?>
