<?php
// Function conv_min2hrsmin()
// Input is unchecked form input in minutes
// Output is string in MySql time format
function conv_min2hrsmin($mininput) {
    $min=filter_var($mininput,FILTER_SANITIZE_NUMBER_INT);
    if (($min<1) or ($min>3000)) return "00:00:00";
    $hrs = floor($min/60);
    $minr= $min % 60;
    return (sprintf("%02d:%02d:00",$hrs,$minr));
    }
//
// Function stripfancy()
// returns a string with many non-7-bit ASCII characters
// removed from input string and replaced with similar
// 7-bit ones
//
// set constants stripfancy_from and stripfancy_to in
// file db_name.php to configure
//
function stripfancy($input) {
    return(strtr($input,stripfancy_from,stripfancy_to));
    }
//
// Function get_nameemail_from_post($name, $email)
// Reads the data posted by the browser form and populates
// the variables from the arguments.  Also stores them in
// SESSION variables.
//
function get_nameemail_from_post(&$name, &$email) {
    $name=stripslashes($_POST['name']);
    $email=stripslashes($_POST['email']);
    $_SESSION['name']=$name;
    $_SESSION['email']=$email;
    return;
    }
//
// Function get_session_interests_from_post()
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.  Returns
// the maximum index value.
//
function get_session_interests_from_post() {
    global $sessInts;
    // for 5 numeric fields--convert to 0 if blank
    $i=0;
    while (isset($_POST["sessionid".$i])) {
        $sessInts[$i]["sessionid"]=$_POST["sessionid".$i];
        $sessInts[$i]["rank"]=$_POST["rank".$i];
        $sessInts[$i]["delete"]=(isset($_POST["delete".$i]))?true:false;
        $sessInts[$i]["comments"]=stripslashes($_POST["comments".$i]);
        $sessInts[$i]["mod"]=(isset($_POST["mod".$i]))?true:false;
        $i++;
        }
    $i--;
    return($i);
    }

// Function get_participant_availability_from_post()
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.
//
// Notes on variables:
// $_POST["availstarttime_$i"], $_POST["availendtime_$i"] are just 1-24 whole hours, 0 for unset; 1 is midnight start of day 
//
function get_participant_availability_from_post() {
    global $partAvail;
    // for numeric fields in ParticipantAvailability--convert to 0 if blank
    $partAvail["maxprog"]=($_POST["maxprog"]=="")?0:$_POST["maxprog"];
    for ($i=1; $i<=CON_NUM_DAYS; $i++) {
        $partAvail["maxprogday$i"]=($_POST["maxprogday$i"]!="")?$_POST["maxprogday$i"]:0;
        }
    for ($i=1; $i<=AVAILABILITY_ROWS; $i++) {
        $x1=$partAvail["availstartday_$i"]=$_POST["availstartday_$i"];
        $x2=$partAvail["availstarttime_$i"]=$_POST["availstarttime_$i"];
        $x3=$partAvail["availendday_$i"]=$_POST["availendday_$i"];
        $x4=$partAvail["availendtime_$i"]=$_POST["availendtime_$i"];
        //error_log("Zambia, get: $i, $x1, $x2, $x3, $x4");
        }
    $partAvail["preventconflict"]=stripslashes($_POST["preventconflict"]);
    $partAvail["numkidsfasttrack"]=($_POST["numkidsfasttrack"]=="")?0:$_POST["numkidsfasttrack"]+0;
    $partAvail["otherconstraints"]=stripslashes($_POST["otherconstraints"]);
    }

// Function get_session_from_post()
// Reads the data posted by the browser form and populates
// the $session global variable with it.
//
function get_session_from_post() {
    global $session;
    $session["sessionid"]=$_POST["sessionid"];
    $session["track"]=$_POST["track"];
    $session["type"]=$_POST["type"];
    $session["divisionid"]=$_POST["divisionid"];
    $session["pubstatusid"]=$_POST["pubstatusid"];
    $session["languagestatusid"]=$_POST["languagestatusid"];
    $session["pubno"]=stripslashes($_POST["pubno"]);
    if (isset($_POST["title"])) {
            $session["title"]=stripslashes($_POST["title"]);
            }
        else {
            $session["title"]="";
            }
    $session["secondtitle"]=stripslashes($_POST["secondtitle"]);
    $session["pocketprogtext"]=stripslashes($_POST["pocketprogtext"]);
    $session["progguiddesc"]=stripslashes($_POST["progguiddesc"]);
    $session["persppartinfo"]=stripslashes($_POST["persppartinfo"]);
    //error_log("Zambia->get_session_from_post->\$_POST[\"pubchardest\"]: ".print_r($_POST["pubchardest"],TRUE)); // for debugging only
    $session["pubchardest"]=$_POST["pubchardest"];
    //error_log("Zambia->get_session_from_post->\$session[\"pubchardest\"]: ".print_r($session["pubchardest"],TRUE)); // for debugging only
    $session["featdest"]=$_POST["featdest"];
    //error_log("Zambia->get_session_from_post->\$session[\"featdest\"]: ".print_r($session["featdest"],TRUE)); // for debugging only
    $session["servdest"]=$_POST["servdest"];
    $session["duration"]=stripslashes($_POST["duration"]);
    $session["atten"]=$_POST["atten"];
    $session["kids"]=$_POST["kids"];
    $session["invguest"]=isset($_POST["invguest"]);
    $session["signup"]=isset($_POST["signup"]);
    $session["roomset"]=$_POST["roomset"];
    $session["notesforpart"]=stripslashes($_POST["notesforpart"]);
    $session["servnotes"]=stripslashes($_POST["servnotes"]);
    $session["status"]=$_POST["status"];
    $session["notesforprog"]=stripslashes($_POST["notesforprog"]);
    }

// Function set_session_defaults() 
// Populates the $session global variable with default data
// for use when creating a new session.  Note that if a field is
// an index into a table of options, the default value of "0" signifies
// that "Select" will be displayed in the gui.
//
function set_session_defaults() {
    global $session;
    //$session["sessionid"] set elsewhere
    $session["track"]=0; // prompt with "SELECT"
    $session["type"]=1; // default to "Panel"
    $session["divisionid"]=1; // default to "Programming"
    $session["pubstatusid"]=2; // default to "Public"
    $session["languagestatusid"]=1; // default to "English"
    $session["pubno"]="";
    $session["title"]="";
    $session["secondtitle"]="";
    $session["pocketprogtext"]="";
    $session["persppartinfo"]="";
    $session["progguiddesc"]="";
    $session["featdest"]="";
    $session["servdest"]="";
    $session["pubchardest"]="";
    if (DURATION_IN_MINUTES=="TRUE") {
            $session["duration"]=" 60";
            }
        else {
            $session["duration"]=" 1:00";
            } 
    $session["atten"]="";
    $session["kids"]=2; // "Kids Welcome"
    $session["signup"]=false; // leave checkbox blank initially
    $session["roomset"]=0; // prompt with "SELECT"
    $session["notesforpart"]="";
    $session["servnotes"]="";
    $session["status"]=2; // default to "Edit Me"
    $session["notesforprog"]="";
    $session["invguest"]=false; // leave checkbox blank initially
    }
// Function parse_mysql_time($time)
// Takes the string $time in "hhh:mm:ss" and return array of "day" and "hour" and "minute"
//
function parse_mysql_time($time) {
    $h=0+substr($time,0,strlen($time)-6);
    $result['hour']=fmod($h,24);
    $result['day']=intval($h/24);
    $result['minute']=substr($time,strlen($time)-5,2);
    return($result);
    }
//
// Function parse_mysql_time_hours($time)
// Takes the string $time in "hhh:mm:ss" and return array of "hours", "minutes", and "seconds"
//
function parse_mysql_time_hours($time) {
    sscanf($time,"%d:%d:%d",$hours,$minutes,$seconds);
    $result['hours']=$hours;
    $result['minutes']=$minutes;
    $result['seconds']=$seconds;
    return($result);
    }
//
// Function time_description($time)
// Takes the string $time and return string describing time
// $time is mysql output measured from start of con
// result is like "Fri 1:00 PM"
//
function time_description($time) {
    global $daymap;
    $atime=parse_mysql_time($time);
    $result="";
    $result.=$daymap['short'][$atime["day"]+1]." ";
    $hour=fmod($atime["hour"],12);
    $result.=(($hour==0)?12:$hour).":".$atime["minute"]." ";
    $result.=($atime["hour"]>=12)?"PM":"AM";
    return($result);
    }

// Function fix_slashes($arg)
// Takes the string $arg and removes multiple slashes, 
// slash-quote and slash-double quote.
function fix_slashes($arg) {    
    while (($pos=strpos($arg,"\\\\"))!==false) {
        if ($pos==0) {
                $arg=substr($arg,1);
                }
            else {
                $arg=substr($arg,0,$pos).substr($arg,$pos+1);
                }
        }
    while (($pos=strpos($arg,"\\'"))!==false) {
        if ($pos==0) {
                $arg=substr($arg,1);
                }
            else {
                $arg=substr($arg,0,$pos).substr($arg,$pos+1);
                }
        }
    while (($pos=strpos($arg,"\\\""))!==false) {
        if ($pos==0) {
                $arg=substr($arg,1);
                }
            else {
                $arg=substr($arg,0,$pos).substr($arg,$pos+1);
                }
        }
    return $arg;
    }

// Function isStaff($badgeid)
// $badgeid is vestigial
// returns true if user has staff permissions

function isStaff($badgeid) {
//    error_log("Zambia: ".print_r($permission_set,TRUE));
    return (in_array("Staff",$_SESSION['permission_set']));
    }

// Function may_I($permatomtag)
// $permatomtag is a string which designates a permission atom
// returns TRUE if user has this permission in the current phase(s)
//
function may_I($permatomtag) {
    return (in_array($permatomtag,$_SESSION['permission_set']));
    }    
?>
