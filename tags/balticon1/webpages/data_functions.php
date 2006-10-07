<?php
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
function get_participant_availability_from_post() {
    global $partAvail;
    // for 5 numeric fields--convert to 0 if blank
    $partAvail["fridaymaxprog"]=($_POST["fridaymaxprog"]=="")?0:$_POST["fridaymaxprog"]+0;
    $partAvail["saturdaymaxprog"]=($_POST["saturdaymaxprog"]=="")?0:$_POST["saturdaymaxprog"]+0;
    $partAvail["sundaymaxprog"]=($_POST["sundaymaxprog"]=="")?0:$_POST["sundaymaxprog"]+0;
    $partAvail["maxprog"]=($_POST["maxprog"]=="")?0:$_POST["maxprog"];
    $partAvail["availstartday_1"]=$_POST["availstartday_1"];
    $partAvail["availstartday_2"]=$_POST["availstartday_2"];
    $partAvail["availstartday_3"]=$_POST["availstartday_3"];
    $partAvail["availstartday_4"]=$_POST["availstartday_4"];
    $partAvail["availstartday_5"]=$_POST["availstartday_5"];
    $partAvail["availstartday_6"]=$_POST["availstartday_6"];
    $partAvail["availstarttime_1"]=$_POST["availstarttime_1"];
    $partAvail["availstarttime_2"]=$_POST["availstarttime_2"];
    $partAvail["availstarttime_3"]=$_POST["availstarttime_3"];
    $partAvail["availstarttime_4"]=$_POST["availstarttime_4"];
    $partAvail["availstarttime_5"]=$_POST["availstarttime_5"];
    $partAvail["availstarttime_6"]=$_POST["availstarttime_6"];
    $partAvail["availendday_1"]=$_POST["availendday_1"];
    $partAvail["availendday_2"]=$_POST["availendday_2"];
    $partAvail["availendday_3"]=$_POST["availendday_3"];
    $partAvail["availendday_4"]=$_POST["availendday_4"];
    $partAvail["availendday_5"]=$_POST["availendday_5"];
    $partAvail["availendday_6"]=$_POST["availendday_6"];
    $partAvail["availendtime_1"]=$_POST["availendtime_1"];
    $partAvail["availendtime_2"]=$_POST["availendtime_2"];
    $partAvail["availendtime_3"]=$_POST["availendtime_3"];
    $partAvail["availendtime_4"]=$_POST["availendtime_4"];
    $partAvail["availendtime_5"]=$_POST["availendtime_5"];
    $partAvail["availendtime_6"]=$_POST["availendtime_6"];
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
    $session["pubno"]=stripslashes($_POST["pubno"]);
    $session["title"]=stripslashes($_POST["title"]);
    $session["pocketprogtext"]=stripslashes($_POST["pocketprogtext"]);
    $session["progguiddesc"]=stripslashes($_POST["progguiddesc"]);
    $session["persppartinfo"]=stripslashes($_POST["persppartinfo"]);
    $session["pubchardest"]=$_POST["pubchardest"];
    $session["featdest"]=$_POST["featdest"];
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
    $session["type"]=0; // prompt with "SELECT"
    $session["divisionid"]=0; // prompt with "SELECT"
    $session["pubstatusid"]=0; // prompt with "SELECT"
    $session["pubno"]="";
    $session["title"]="";
    $session["pocketprogtext"]="";
    $session["persppartinfo"]="";
    $session["progguiddesc"]="";
    $session["featdest"]="";
    $session["servdest"]="";
    $session["pubchardest"]="";
    $session["duration"]="1:00 ";
    $session["atten"]=0;
    $session["kids"]=2; // "Kids Welcome"
    $session["signup"]=false; // leave checkbox blank initially
    $session["roomset"]=0; // prompt with "SELECT"
    $session["notesforpart"]="";
    $session["servnotes"]="";
    $session["status"]=6; // default to "Edit Me"
    $session["notesforprog"]="";
    $session["invguest"]=false; // leave checkbox blank initially
    }
// Function validate_session_interests($max_si_row)
// Reads global $sessInts array and performs tests.
// If a test fails, then the global $message is populated
// with the HTML of an error message.
//
function validate_session_interests($max_si_row) {
    global $sessInts, $messages;
    $flag=true;
    $messages="";
    for ($i=0; $i<=$max_si_row; $i++) {
        if (!((is_numeric($sessInts[$i]["rank"]))||($sessInts[$i]["rank"]==""))) {
            $messages="Ranks must be numbers.<BR>\n";
            $flag=false;
            }
        if ($sessInts[$i]["rank"]<0) {    
            $messages="Ranks must be greater than zero.<BR>\n";
            $flag=false;
            }
        if (!$flag) {break;}
        }
    return ($flag);
    }

// Function validate_session()
// Reads global $session array and performs tests.
// If a test fails, then the global $message is populated
// with the HTML of an error message.
//
function validate_session() {
    // may be incomplete!!
    global $session, $messages;
    $flag=true;
    $messages="";
    if (!strlen($session["title"])) {
        $messages.="A title is required.<BR>\n";
        $flag=false;
        }
    if (($i=strlen($session["title"]))>48) {
        $messages.="Title is $i characters long.  Please edit it to <B>48</B> characters or fewer.<BR>\n";
        $flag=false;
        }
    if (($i=strlen($session["pocketprogtext"]))>110) {
        $messages.="Pocket program text is $i characters long.  Please edit it to <B>110</B> characters or fewer.<BR>\n";
        $flag=false;
        }
    if (($i=strlen($session["progguiddesc"]))>500) {
        $messages.="Program guide description is $i characters long.  Please edit it to <B>500</B> characters or fewer.<BR>\n";
        $flag=false;
        }
    if ($session["track"]==0) {
        $messages.="Please select a track.<BR>\n";
        $flag=false;
        }
    if ($session["type"]==0) {
        $messages.="Please select a type.<BR>\n";
        $flag=false;
        }
    if ($session["divisionid"]==0) {
        $messages.="Please select a division.<BR>\n";
        $flag=false;
        }
    if ($session["pubstatusid"]==0) {
        $messages.="Please select a publication status.<BR>\n";
        $flag=false;
        }
    if ($session["kids"]==0) {
        $messages.="Please select a kid category.<BR>\n";
        $flag=false;
        }
    if ($session["roomset"]==0) {
        $messages.="Please select a room set.<BR>\n";
        $flag=false;
        }
    return ($flag);
    }

// Function validate_participant_availability()
// Reads global $partAvail and performs tests.
// If a test fails, then the global $message is populated
// with the HTML of an error message.
//
function validate_participant_availability() {
    // may be incomplete!!
    global $partAvail, $messages;
    $flag=true;
    $messages="";
    $message2="";
    $message3="";
    if (!($partAvail["fridaymaxprog"]>=0 and $partAvail["fridaymaxprog"]<=10)) {
        $messages="Numbers must be between 0 and 10.<BR>\n";
        $flag=false;
        }
    if (!($partAvail["saturdaymaxprog"]>=0 and $partAvail["saturdaymaxprog"]<=10)) {
        $messages="Numbers must be between 0 and 10.<BR>\n";
        $flag=false;
        }
    if (!($partAvail["sundaymaxprog"]>=0 and $partAvail["sundaymaxprog"]<=10)) {
        $messages="Numbers must be between 0 and 10.<BR>\n";
        $flag=false;
        }
    if (!($partAvail["maxprog"]>=0 and $partAvail["maxprog"]<=10)) {
        $messages="Numbers must be between 0 and 10.<BR>\n";
        $flag=false;
        }
    if (!($partAvail["numkidsfasttrack"]>=0 and $partAvail["numkidsfasttrack"]<=10)) {
        $messages="Numbers must be between 0 and 10.<BR>\n";
        $flag=false;
        }
    $x1=$partAvail["availstartday_1"];
    $x2=$partAvail["availstarttime_1"];
    $x3=$partAvail["availendday_1"];
    $x4=$partAvail["availendtime_1"];
    if (($x1>0 || $x2>0 || $x3>0 || $x4>0) && ($x1==0 || $x2==0 || $x3==0 || $x4==0 )) {
        $message2="To define an available slot set all 4 items.  To delete a slot, clear all 4 items.<BR>";
        $flag=false;
        }
    if (($x3<$x1) || ($x1==$x3 && $x2>$x4)) {
        $message3="End time and day must be after start time and day.<BR>";
        $flag=false;
        }
    $x1=$partAvail["availstartday_2"];
    $x2=$partAvail["availstarttime_2"];
    $x3=$partAvail["availendday_2"];
    $x4=$partAvail["availendtime_2"];
    if (($x1>0 || $x2>0 || $x3>0 || $x4>0) && ($x1==0 || $x2==0 || $x3==0 || $x4==0 )) {
        $message2="To define an available slot set all 4 items.  To delete a slot, clear all 4 items.<BR>";
        $flag=false;
        }
    if (($x3<$x1) || ($x1==$x3 && $x2>$x4)) {
        $message3="End time and day must be after start time and day.<BR>";
        $flag=false;
        }
    $x1=$partAvail["availstartday_3"];
    $x2=$partAvail["availstarttime_3"];
    $x3=$partAvail["availendday_3"];
    $x4=$partAvail["availendtime_3"];
    if (($x1>0 || $x2>0 || $x3>0 || $x4>0) && ($x1==0 || $x2==0 || $x3==0 || $x4==0 )) {
        $message2="To define an available slot set all 4 items.  To delete a slot, clear all 4 items.<BR>";
        $flag=false;
        }
    if (($x3<$x1) || ($x1==$x3 && $x2>$x4)) {
        $message3="End time and day must be after start time and day.<BR>";
        $flag=false;
        }
    $x1=$partAvail["availstartday_4"];
    $x2=$partAvail["availstarttime_4"];
    $x3=$partAvail["availendday_4"];
    $x4=$partAvail["availendtime_4"];
    if (($x1>0 || $x2>0 || $x3>0 || $x4>0) && ($x1==0 || $x2==0 || $x3==0 || $x4==0 )) {
        $message2="To define an available slot set all 4 items.  To delete a slot, clear all 4 items.<BR>";
        $flag=false;
        }
    if (($x3<$x1) || ($x1==$x3 && $x2>$x4)) {
        $message3="End time and day must be after start time and day.<BR>";
        $flag=false;
        }
    $messages.=$message2.$message3;    
    return ($flag);
    }

// Function parse_mysql_time($time)
// Takes the string $time and return array of "day" and "hour" and "minute"
//
function parse_mysql_time($time) {
    $h=0+substr($time,0,2);
    $result['hour']=fmod($h,24);
    $result['day']=intval($h/24);
    $result['minute']=substr($time,3,2);
    return($result);
    }

// Function time_description($time)
// Takes the string $time and return string describing time
//
function time_description($time) {
    $days=array("Fri","Sat","Sun");
    $atime=parse_mysql_time($time);
    $result="";
    $result.=$days[$atime["day"]]." ";
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
    global $permission_set;
//    error_log("Zambia: ".print_r($permission_set,TRUE));
    return (in_array("Staff",$permission_set));
    }

// Function may_I($permatomtag)
// $permatomtag is a string which designates a permission atom
// returns TRUE if user has this permission in the current phase(s)
//
function may_I($permatomtag) {
    global $permission_set;
    return (in_array($permatomtag,$permission_set));
    }    
?>
