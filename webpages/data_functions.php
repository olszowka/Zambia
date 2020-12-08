<?php
//	Copyright (c) 2011-2020 The Zambia Group. All rights reserved. See copyright document for more details.
function convertStartTimeToUnits($startTimeHour, $startTimeMin) {
	$startTimeUnits = $startTimeHour * 2;
	if ($startTimeMin >= 30) {
        $startTimeUnits++;
    }
	return $startTimeUnits;
}

function convertEndTimeToUnits($endTimeHour, $endTimeMin) {
    $endTimeUnits = $endTimeHour * 2;
    if ($endTimeMin > 30) {
        $endTimeUnits += 2;
    } elseif ($endTimeMin > 0) {
        $endTimeUnits++;
    }
    return $endTimeUnits;
}

function convertUnitsToTimeStr($timeUnits) {
	return floor($timeUnits/2).":00:00";
}

function convertUnitsToHourMin($timeUnits) {
	$hour = floor($timeUnits/2);
	$min = ($timeUnits%2) * 30;
	return array($hour, $min);
}

function showCustomText($pre, $tag, $post) {
    global $customTextArray;
    if (!empty($customTextArray[$tag])) {
        echo $pre . $customTextArray[$tag] . $post;
    }
}

function fetchCustomText($tag) {
    global $customTextArray;
    if (!empty($customTextArray[$tag])) {
        return $customTextArray[$tag];
    } else {
        return "";
    }
}

function appendCustomTextArrayToXML($xmlDoc) {
    global $customTextArray;
    $customTextNode = $xmlDoc->createElement("customText");
    $docNode = $xmlDoc->getElementsByTagName("doc")->item(0);
    $customTextNode = $docNode->appendChild($customTextNode);
    foreach ($customTextArray as $tag => $customTextValue) {
        $customTextNode->setAttribute($tag, $customTextValue);
    }
    return $xmlDoc;
}

// Function conv_min2hrsmin()
// Input is unchecked form input in minutes
// Output is string in MySql time format
function conv_min2hrsmin($mininput) {
    $min = filter_var($mininput, FILTER_SANITIZE_NUMBER_INT);
    if (($min < 1) or ($min > 3000)) {
        return "00:00:00";
    }
    $hrs = floor($min / 60);
    $minr = $min % 60;
    return (sprintf("%02d:%02d:00", $hrs, $minr));
}

// Function getInt("name", default)
// gets a parameter from $_GET[] or $_POST[] of name
// and confirms it is an integer.
// Safe from referencing nonexisting array index
function getInt($name, $default = false) {
    if (isset($_GET[$name])) {
        $int = $_GET[$name];
    } elseif (isset($_POST[$name])) {
        $int = $_POST[$name];
    } else {
        return $default;
    }
    $t = filter_var($int, FILTER_SANITIZE_NUMBER_INT);
    if (empty($t)) {
        return $default;
    } else {
        return(intval($t));
    }
}

// Function getArrayOfInts("name", default)
// gets a parameter from $_GET[] or $_POST[] of name
// and confirms it is an integer.
// Safe from referencing nonexisting array index
function getArrayOfInts($name, $default = false) {
    if (isset($_GET[$name])) {
        $intArr = $_GET[$name];
    } elseif (isset($_POST[$name])) {
        $intArr = $_POST[$name];
    } else {
        return $default;
    }
    $retVal = array();
    if ($intArr === "null") {
        return $retVal;
    }
    forEach ($intArr as $int) {
        if (!empty($t = filter_var($int, FILTER_SANITIZE_NUMBER_INT))) {
            $retVal[] = $t;
        }
    }
    if (count($retVal) == 0) {
        return $default;
    } else {
        return $retVal;
    }
}


// Function getString("name")
// gets a parameter from $_GET[] or $_POST[] of name
// and strips slashes
// Safe from referencing nonexisting array index
function getString($name) {
    if (isset($_GET[$name])) {
        $string = $_GET[$name];
    } elseif (isset($_POST[$name])) {
        $string = $_POST[$name];
    } else {
        return "";
    }
    return stripslashes($string);
}

// Function getArrayOfStrings("name")
// gets a parameter from $_GET[] or $_POST[] of name
// in form of array and strips slashes from each element
// Safe from referencing nonexisting array index
function getArrayOfStrings($name) {
    if (isset($_GET[$name])) {
        $array = $_GET[$name];
    } elseif (isset($_POST[$name])) {
        $array = $_POST[$name];
    } else {
        return array();
    }
    return array_map(function($str) { return stripslashes($str); }, $array);
}

// Function get_nameemail_from_post($name, $email)
// Reads the data posted by the browser form and populates
// the variables from the arguments.  Also stores them in
// SESSION variables.
//
function get_nameemail_from_post(&$name, &$email) {
    $name = stripslashes($_POST['name']);
    $email = stripslashes($_POST['email']);
    $_SESSION['name'] = $name;
    $_SESSION['email'] = $email;
    return;
}

//
// Function get_participant_availability_from_post()
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.
//
// Notes on variables:
// $_POST["availstarttime_$i"], $_POST["availendtime_$i"] are indexes into Times table, 0 for unset; 
//
function get_participant_availability_from_post() {
    $partAvail = array();
    // for numeric fields in ParticipantAvailability--convert to 0 if blank
    $partAvail["maxprog"] = getInt("maxprog", "NULL");
    for ($i = 1; $i <= CON_NUM_DAYS; $i++) {
        $partAvail["maxprogday$i"] = getInt("maxprogday$i", 0);
    }
    for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
        $partAvail["availstartday_$i"] = getInt("availstartday_$i", 0);
        $partAvail["availstarttime_$i"] = getInt("availstarttime_$i", 0);
        $partAvail["availendday_$i"] = getInt("availendday_$i", 0);
        $partAvail["availendtime_$i"] = getInt("availendtime_$i", 0);
    }
    $partAvail["preventconflict"] = getString("preventconflict");
    $partAvail["numkidsfasttrack"] = getInt("numkidsfasttrack", "NULL");
    $partAvail["otherconstraints"] = getString("otherconstraints");
    return $partAvail;
}

// Function get_session_from_post()
// Reads the data posted by the browser form and populates
// the $session global variable with it.
//
function get_session_from_post() {
    global $session;
    $session["sessionid"] = getInt('sessionid');
    $session["track"] = getInt('track');
    $session["type"] = getInt('type');
    $session["divisionid"] = getInt('divisionid');
    $session["pubstatusid"] = getInt('pubstatusid');
    $session["languagestatusid"] = getInt('languagestatusid');
    $session["pubno"] = getString('pubno');
    $session["title"] = getString('title');
    $session["secondtitle"] = getString('secondtitle');
    $session["pocketprogtext"] = getString('pocketprogtext');
    $session["progguiddesc"] = getString('progguiddesc');
    $session["persppartinfo"] = getString('persppartinfo');
    $session["tagdest"] = getArrayOfStrings("tagdest");
    $session["featdest"] = getArrayOfStrings("featdest");
    $session["servdest"] = getArrayOfStrings("servdest");
    $session["duration"] = getString('duration');
    $session["atten"] = getString('atten');
    $session["kids"] = getInt('kids');
    $session["invguest"] = isset($_POST["invguest"]);
    $session["signup"] = isset($_POST["signup"]);
    $session["roomset"] = getInt('roomset');
    $session["notesforpart"] = getString('notesforpart');
    $session["servnotes"] = getString('servnotes');
    $session["status"] = getInt('status');
    $session["notesforprog"] = getString('notesforprog');
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
    $session["track"] = 0; // prompt with "SELECT"
    $session["type"] = 1; // default to "Panel"
    $session["divisionid"] = 2; // default to "Programming"
    $session["pubstatusid"] = 2; // default to "Public"
    $session["languagestatusid"] = 1; // default to "English"
    $session["pubno"] = "";
    $session["title"] = "";
    $session["secondtitle"] = "";
    $session["pocketprogtext"] = "";
    $session["persppartinfo"] = "";
    $session["progguiddesc"] = "";
    $session["featdest"] = "";
    $session["servdest"] = "";
    $session["tagdest"] = "";
    $session["duration"] = DEFAULT_DURATION; //should be specified corresponding to DURATION_IN_MINUTES preference
    $session["atten"] = "";
    $session["kids"] = 2; // "Kids Welcome"
    $session["signup"] = false; // leave checkbox blank initially
    $session["roomset"] = 0; // prompt with "SELECT"
    $session["notesforpart"] = "";
    $session["servnotes"] = "";
    $session["status"] = 6; // default to "Edit Me"
    $session["notesforprog"] = "";
    $session["invguest"] = false; // leave checkbox blank initially
}
	
// Function set_brainstorm_session_defaults	
// Populates the $session global variable with default data
// for use when creating a new session in brainstorm.  Note that if a field is
// an index into a table of options, the default value of "0" signifies
// that "Select" will be displayed in the gui.
//
function set_brainstorm_session_defaults() {
    global $session;
    $session["roomset"] = 99; // "Unspecified"
    if (!may_I('Staff')) {
        $session["status"] = 1; // brainstorm
    }
}

// Function parse_mysql_time($time)
// Takes the string $time in "hhh:mm:ss" and return array of "day" and "hour" and "minute"
//
function parse_mysql_time($time) {
    $result = array();
    $h = 0 + substr($time, 0, strlen($time) - 6);
    $result['hour'] = fmod($h, 24);
    $result['day'] = intval($h / 24);
    $result['minute'] = intval(substr($time, strlen($time) - 5, 2));
    return ($result);
}

//
// Function parse_mysql_time_hours($time)
// Takes the string $time in "hhh:mm:ss" and return array of "hours", "minutes", and "seconds"
//
function parse_mysql_time_hours($time) {
    $result = array();
    $hours = "";
    $minutes = "";
    $seconds = "";
    sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
    $result['hours'] = $hours;
    $result['minutes'] = $minutes;
    $result['seconds'] = $seconds;
    return ($result);
}

//
// Function time_description($time)
// Takes the string $time and return string describing time
// $time is mysql output measured from start of con
// result is like "Fri 1:00 PM"
//
function time_description($time) {
    global $con_start_php_timestamp;
    $atime = parse_mysql_time($time);
    try {
        $interval = new DateInterval(sprintf("P%dDT%dH%dM", $atime["day"], $atime["hour"], $atime["minute"]));
    } catch (Exception $e) {
        return false;
    }
    $netdatetime = date_add (clone $con_start_php_timestamp , $interval );
    if ($netdatetime === false) {
        return false;
    }
    return date_format($netdatetime, "D g:i A");
}

//
// Function timeDescFromUnits($timeUnits)
// Takes the int $timeUnits which is the number of time units (1/2 hours)
// from the start of the con and converts to string like "Fri 1:00 PM"
function timeDescFromUnits($timeUnits) {
    global $con_start_php_timestamp;
    $days = intval($timeUnits / 48);
    $hours = intval(($timeUnits % 48) / 2);
    $minutes = 30 * $timeUnits % 2;
    try {
        $interval = new DateInterval(sprintf("P%dDT%dH%dM", $days, $hours, $minutes));
    } catch (Exception $e) {
        return false;
    }
    $netdatetime = date_add (clone $con_start_php_timestamp , $interval );
    if ($netdatetime === false) {
        return false;
    }
    return date_format($netdatetime, "D g:i A");
}

//
// Function longDayNameFromInt($daynum)
// Take the int $daynum which represents day of the con (starting at 1)
// and returns the string with the full day of the week, e.g. "Friday", "Saturday"
// for that day taking into account the configured start of the con CON_START_DATIM
function longDayNameFromInt($daynum) {
    global $con_start_php_timestamp;
    if ($daynum == 1) {
        return date_format($con_start_php_timestamp, "l");
    }
    try {
        $interval = new DateInterval(sprintf("P%dD", $daynum - 1));
    } catch (Exception $e) {
        return false;
    }
    $netdatetime = date_add (clone $con_start_php_timestamp , $interval );
    if ($netdatetime === false) {
        return false;
    }
    return date_format($netdatetime, "l");
}

//
// Function fix_slashes($arg)
// Takes the string $arg and removes multiple slashes, 
// slash-quote and slash-double quote.
function fix_slashes($arg) {
    while (($pos = strpos($arg, "\\\\")) !== false) {
        if ($pos == 0) {
            $arg = substr($arg, 1);
        } else {
            $arg = substr($arg, 0, $pos) . substr($arg, $pos + 1);
        }
    }
    while (($pos = strpos($arg, "\\'")) !== false) {
        if ($pos == 0) {
            $arg = substr($arg, 1);
        } else {
            $arg = substr($arg, 0, $pos) . substr($arg, $pos + 1);
        }
    }
    while (($pos = strpos($arg, "\\\"")) !== false) {
        if ($pos == 0) {
            $arg = substr($arg, 1);
        } else {
            $arg = substr($arg, 0, $pos) . substr($arg, $pos + 1);
        }
    }
    return $arg;
}

// Function may_I($permatomtag)
// $permatomtag is a string which designates a permission atom
// returns TRUE if user has this permission in the current phase(s)
//
function may_I($permatomtag) {
    if (!isset($_SESSION['permission_set'])) {
        return false;
    }
    if (!is_array($_SESSION['permission_set'])) {
        return false;
    }
    return (in_array($permatomtag, $_SESSION['permission_set']));
}

// Function GeneratePermissionSetXML()
// returns an XMLDoc as if from a query with the permission set from the Session.
//
function GeneratePermissionSetXML() {
    $permissionSetXML = new DomDocument("1.0", "UTF-8");
    $doc = $permissionSetXML -> createElement("doc");
    $doc = $permissionSetXML -> appendChild($doc);
    $queryNode = $permissionSetXML -> createElement("query");
    $queryNode = $doc -> appendChild($queryNode);
    $queryNode->setAttribute("queryname", "permission_set");
    foreach($_SESSION['permission_set'] as $permAtomTag) {
        $rowNode = $permissionSetXML->createElement("row");
        $rowNode = $queryNode->appendChild($rowNode);
        $rowNode->setAttribute("permatomtag", $permAtomTag);
    }
    // echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $permissionSetXML->saveXML(), "i"));
    return $permissionSetXML;
}

// Function generateControlString()
// $paramArray is an associative array of info to be hidden in a form
// $controliv is optional initialization vector; it will be created for you if not specified
// returns an associate array of:
// "control" => encrypted representation of $paramArray
// "controliv" => initialization vector
// the set of which can be reconstructed by interpretControlString()
function generateControlString($paramArray, $controliv = '') {
    if ($controliv == '') {
        $ivlen = openssl_cipher_iv_length('aes-128-cbc');
        $controliv = openssl_random_pseudo_bytes($ivlen);
    }
    $control = openssl_encrypt(json_encode($paramArray), 'aes-128-cbc', ENCRYPT_KEY, 0, $controliv);
    return array("controliv" => base64_encode($controliv), "control" => $control);
}

// Function interpretControlString()
// $control and $controliv were returned from generateControlString() and passed through a form.
// returns the original associative array sent to generateControlString()
function interpretControlString($control, $controliv) {
    return json_decode(openssl_decrypt($control, 'aes-128-cbc', ENCRYPT_KEY, 0, base64_decode($controliv)), true);
}
?>