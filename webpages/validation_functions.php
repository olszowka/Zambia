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
// Function validate_session()
// Reads global $session array and performs tests.
// If a test fails, then the global $message is populated
// with the HTML of an error message.
//
function validate_session() {
    // may be incomplete!!
    global $session, $messages;
    $flag=true;
    if ($session["status"]==4||$session["status"]==5) {  //don't validate "dropped" or "cancelled"
        return ($flag);
        }
    $brainstorm=($session["status"]==1 || $session["status"]==6); //less stringent criteria if brainstorm (editme)
/*    if (!strlen($session["title"])) {
        $messages.="A valid title is required.<BR>\n";
        $flag=false;
        } */
    $i=strlen($session["title"]);
    if ($i<10||$i>48) {
        $messages.="Title is $i characters long.  Please edit it to between <B>10</B> and <B>48</B> characters.<BR>\n";
        $flag=false;
        }
/*    if (!strlen($session["progguiddesc"])) {
        $messages.="A title is required.<BR>\n";
        $flag=false;
        } */
    if (($i=strlen($session["pocketprogtext"]))>110) {
        $messages.="Pocket program text is $i characters long.  Please edit it to <B>110</B> characters or fewer.<B
R>\n";
        $flag=false;
        }
    $i=strlen($session["progguiddesc"]);
    if ($i<10||$i>500) {
        $messages.="Program guide description is $i characters long.  Please edit it to between";
        $messages.=" <B>10</B> and <B>500</B> characters long.<BR>\n";
        $flag=false;
        }
    if ($session["track"]==0) {
        $messages.="Please select a track.<BR>\n";
        $flag=false;
        }
    if ($brainstorm) { // less stringent criteria if brainstorm or editme
                       // set some defaults if brainstorm or editme
            if ($session["pubstatusid"]==0) { // default to "public" if not set
                $session["pubstatusid"]=2;
                }
            if ($session["divisionid"]==0) { // default to "unspecified" if not set
                $session["divisionid"]=6;
                }
            if ($session["type"]==0) { // default to "I do not know" if not set
                $session["type"]=17;
                }
            if ($session["roomset"]==0) { // default to "Unspecified" if not set
                $session["roomset"]=4;
                }
            }
        else { // more stringent criteria if not brainstorm or editme
            if ($session["pubstatusid"]==0) {
                $messages.="Please select a publication status.<BR>\n";
                $flag=false;
                }
            if ($session["type"]==0 || $session["type"]==17) { // don't allow "I do not know"
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
            if ($session["roomset"]==0 || $session["roomset"]==4) { // don't allow "Unspecified"
                $messages.="Please select a room set.<BR>\n";
                $flag=false;
                }
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
    if (!($partAvail["mondaymaxprog"]>=0 and $partAvail["mondaymaxprog"]<=10)) {
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
?>