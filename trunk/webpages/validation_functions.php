<?php

function validate_suggestions() {  // just stub for now
    return(true);                 // return true means "passed"
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
function is_email($email){
    $x = '\d\w!\#\$%&\'*+\-/=?\^_`{|}~';    //just for clarity

    return count($email = explode('@', $email, 3)) == 2
        && strlen($email[0]) < 65
        && strlen($email[1]) < 256
        && preg_match("#^[$x]+(\.?([$x]+\.)*[$x]+)?$#", $email[0])
        && preg_match('#^(([a-z0-9]+-*)?[a-z0-9]+\.)+[a-z]{2,6}.?$#', $email[1]);
}
function validate_name_email($name, $email) {
    global $messages;
    $status=true;
// only perform test for brainstorm user
    if (may_I("Staff") || may_I("Participant")) {
        return ($status);
        }
    if (strlen($name)<3) {
        $status=false;
        $messages.="Please enter a name of at least 3 characters.<BR>\n";
        }
    if (!(is_email($email))) {
        $status=false;
        $messages.="Please enter a valid email address.<BR>\n";
        }
    return ($status);
    }
// Function validate_integer($input,$min,$max)
// Return true if input is integer within range (inclusive)
// otherwise false
//
function validate_integer($input,$min,$max) {
    return filter_var(
        $input,
        FILTER_VALIDATE_INT,
        array('options' => array('min_range' => $min, 'max_range' => $max))
        );
    }
// Function validate_session()
// Reads global $session array and performs tests.
// If a test fails, then the global $message is populated
// with the HTML of an error message.
//
function validate_session() {
    // may be incomplete!!
    global $sstatus, $session, $messages, $debug;
    $flag=true;
    get_sstatus();
    if (!(validate_integer($session['atten'],1,99999))) { //must require integer atten to be able to write to db
        $messages.="Estimated attendance must be a positive integer.<BR>\n";
        $flag=false;
        }
    if ($session["track"]==0) {
        $messages.="Please select a track.<BR>\n";
        $flag=false;
        }
    if ($session['roomset']==0) { 
        $messages.="Please select a room set.<BR>\n";
        $flag=false;
        }
    if (!($sstatus[$session["status"]]['validate'])) {
//don't validate further those not marked with 'validate' such as "dropped" or "cancelled"
        return ($flag);
        }
    $i=strlen($session["title"]);
    if ($i<10||$i>48) {
        $messages.="Title is $i characters long.  Please edit it to between <B>10</B> and <B>48</B> characters.<BR>\n";
        $flag=false;
        }
    $i=strlen($session["progguiddesc"]);
    if ($i<10||$i>500) {
        $messages.="Description is $i characters long.  Please edit it to between";
        $messages.=" <B>10</B> and <B>500</B> characters long.<BR>\n";
        $flag=false;
        }
    if (!($sstatus[$session["status"]]['may_be_scheduled'])) {
//don't validate further those not marked with 'may_be_scheduled'.
        return($flag);
        }
//most stringent validation for those which may be scheduled.
    if ($session["pubstatusid"]==0) {
        $messages.="Please select a publication status.<BR>\n";
        $flag=false;
        }
    if ($session["type"]==0 || $session["type"]==99) { // don't allow "I do not know"
        $messages.="Please select a type.<BR>\n";
        $flag=false;
        }
    if ($session["divisionid"]==0 || $session["divisionid"]==6) { // don't allow "Unspecified"
        $messages.="Please select a division.<BR>\n";
        $flag=false;
        }
    if ($session["kids"]==0) {
        $messages.="Please select a kid category.<BR>\n";
        $flag=false;
        }
    if ($session["roomset"]==0 || $session["roomset"]==99) { // don't allow "Unspecified"
        $messages.="Please select a room set.<BR>\n";
        $flag=false;
        }
    if ($session["track"]==0 || $session["track"]==99) { // don't allow "Unspecified"
        $messages.="Please select a track.<BR>\n";
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
    global $partAvail, $messages;
    $flag=true;
    $messages="";
    if (!($partAvail["maxprog"]>=0 and $partAvail["maxprog"]<=PREF_TTL_SESNS_LMT)) {
        $x=PREF_TTL_SESNS_LMT;
        $messages="For the overall maximum number of panels, enter a number between 0 and $x.<BR>\n";
        $flag=false;
        }
    if (CON_NUM_DAYS>1) {
        for ($i=1; $i<=CON_NUM_DAYS; $i++) {
            if (!($partAvail["maxprogday$i"]>=0 and $partAvail["maxprogday$i"]<=10)) {
                $x=PREF_DLY_SESNS_LMT;
                $messages.="For each daily maximum number of panels, enter a number between 0 and $x.<BR>\n";
                $flag=false;
                break;
                }
            }
        } 
    if (!($partAvail["numkidsfasttrack"]>=0 and $partAvail["numkidsfasttrack"]<=8)) {
        $messages.="For the number of kids for fastrack, enter a number between 0 and 8.<BR>\n";
        $flag=false;
        }
    for ($i=1; $i<= AVAILABILITY_ROWS; $i++) {
        if (CON_NUM_DAYS>1) {
                // Day fields will be populated
                $x1=$partAvail["availstartday_$i"];
                $x2=$partAvail["availstarttime_$i"];
                $x3=$partAvail["availendday_$i"];
                $x4=$partAvail["availendtime_$i"];
                //error_log("zambia: $i, $x1, $x2, $x3, $x4"); //for debugging only
                if (($x1>0 || $x2>0 || $x3>0 || $x4>0) && ($x1==0 || $x2==0 || $x3==0 || $x4==0 )) {
                    $messages.="To define an available slot, set all 4 items.  To delete a slot, clear all 4 items.<BR>\n";
                    $flag=false;
                    break;
                    }
                }
            else {
                // Day fields will not be populated.
                $x2=$partAvail["availstarttime_$i"];
                $x4=$partAvail["availendtime_$i"];
                if (($x2>0 || $x4>0) && ($x2==0 || $x4==0)) {
                    $messages.="To define an available slot, set both items.  To delete a slot, clear both items.<BR>\n";
                    $flag=false;
                    break;
                    }
                }
        }
    for ($i=1; $i<= AVAILABILITY_ROWS; $i++) {
        if (CON_NUM_DAYS>1) {
                // Day fields will be populated
                $x1=$partAvail["availstartday_$i"];
                $x2=$partAvail["availstarttime_$i"];
                $x3=$partAvail["availendday_$i"];
                $x4=$partAvail["availendtime_$i"];
                if ($x1!=0 && (($x3<$x1) || ($x1==$x3 && $x4<=$x2))) {
                    $messages.="End time and day must be after start time and day.<BR>\n";
                    $flag=false;
                    break;
                    }
                }
            else {
                // Day fields will not be populated.
                $x2=$partAvail["availstarttime_$i"];
                $x4=$partAvail["availendtime_$i"];
                if (($x4<=$x2) && ($x2!=0)) {
                    $messages.="End time must be after start time.<BR>\n";
                    $flag=false;
                    break;
                    }
                }

        }
    return ($flag);
    }
?>
