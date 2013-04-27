<?php 
header("Content-Type: text/Calendar");
require_once ("CommonCode.php");
require_once ("CommonIcal.php");
global $link;

// Fixed, or setup variables
$ConStartDatim=CON_START_DATIM;
$ConName=CON_NAME;
$ProgramEmail=PROGRAM_EMAIL;
$DBHostname=DBHOSTNAME;
$url=CON_URL;
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$dtstamp=date('Ymd').'T'.date('His');
if (isset($_GET['badgeid'])) {
  $badgeid=$_GET['badgeid'];
} elseif (isset($_POST['badgeid'])) {
  $badgeid=$_POST['badgeid'];
} else {
  $badgeid=$_SESSION['badgeid'];
}

// First query, to establish the schedarray, from which we need 
$query= <<<EOD
SELECT
    POS.sessionid,
    trackname,
    title,
    roomname,
    progguiddesc,
    DATE_FORMAT(ADDTIME('$ConStartDatim', starttime),'%Y%m%dT%H%i%s') AS dtstart,
    DATE_FORMAT(ADDTIME(ADDTIME('$ConStartDatim', starttime), duration), '%Y%m%dT%H%i%s') AS dtend
  FROM
      ParticipantOnSession POS,
      Sessions S,
      Rooms R,
      Schedule SCH,
      $ReportDB.Tracks T
  WHERE
    badgeid="$badgeid" and
    POS.sessionid = S.sessionid and
    R.roomid = SCH.roomid and
    S.sessionid = SCH.sessionid and
    S.trackid = T.trackid
  ORDER BY
    starttime
EOD;

if (($result=mysql_query($query,$link))===false) {
  echo "An Error occured:\n";
  echo "$result\n$link\n$query\n Error retrieving data from database.\n";
  exit();
}

$schdrows=mysql_num_rows($result);
for ($i=1; $i<=$schdrows; $i++) {
  list($schdarray[$i]["sessionid"],$schdarray[$i]["trackname"],
       $schdarray[$i]["title"],$schdarray[$i]["roomname"],$schdarray[$i]["progguiddesc"],
       $schdarray[$i]["dtstart"],$schdarray[$i]["dtend"])=mysql_fetch_array($result, MYSQL_NUM);
}

$query= <<<EOD
SELECT
    POS.sessionid,
    CD.badgename,
    P.pubsname,
    POS.moderator,
    POS.volunteer,
    POS.introducer,
    POS.aidedecamp
  FROM
      ParticipantOnSession POS
    JOIN $ReportDB.CongoDump CD USING(badgeid)
    JOIN $ReportDB.Participants P USING(badgeid)
  WHERE
    POS.sessionid in (SELECT
                          sessionid 
                        FROM
                            ParticipantOnSession
                        WHERE badgeid='$badgeid')
  ORDER BY
    sessionid,
    moderator DESC
EOD;

if (!$result=mysql_query($query,$link)) {
  echo "An Error occured:\n";
  echo "$result\n$link\n$query\n Error retrieving data from database.\n";
  exit();
}
$partrows=mysql_num_rows($result);
for ($i=1; $i<=$partrows; $i++) {
  list($partarray[$i]["sessionid"],$partarray[$i]["badgename"],$partarray[$i]["pubsname"],
       $partarray[$i]["moderator"],$partarray[$i]["volunteer"],$partarray[$i]["introducer"],
       $partarray[$i]["aidedecamp"])=mysql_fetch_array($result, MYSQL_NUM);
}

$query=<<<EOD
SELECT
    pubsname
  FROM
      $ReportDB.Participants
  WHERE
    badgeid='$badgeid'
EOD;

if (!$result=mysql_query($query,$link)) {
  echo "An Error occured:\n";
  echo "$result\n$link\n$query\n Error retrieving data from database.\n";
  exit();
}
$partnamerow=mysql_num_rows($result);
for ($i=1; $i<=$partnamerow; $i++) {
  list($partname[$i]["pubsname"])=mysql_fetch_array($result, MYSQL_NUM);
}

$filename=str_replace(" ","_",$partname[1]["pubsname"]);

// Send the correct header type
header("Content-Disposition: inline; filename=$filename-calendar.ics");

// Standard intro stuff at the beginning of the file
echo add_ical_header();

// This should loop for every element in the produced array
for ($i=1; $i<=$schdrows; $i++) {

  /*
  UID should be DTSTAMP, followed by DTSTART, followed by DTEND followed by SEQUENCE followed by $DBHostname
  DTSTAMP should be generated from whenever this file is clicked on
  DTSTAMP:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
  LAST-MODIFIED should be DTSTAMP
  CREATED should be DTSTAMP
  SEQUENCE should be the loop counter
  PRIORITY is set to 5, arbitrarily
  CATEGORY should be "$ConName Event Calendar"
  SUMMARY should be title -- trackname, possibly add sessionid?
  LOCATION should be roomname
  DTSTART should be garnered from the $ConStartDatim + starttime
  DTSTART:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
  DTEND is chosen over DURATION because it's easier to just do $ConStartDatim + starttime + duration
  DTEND:YYYYMMDDTHHmmSS Y=year M=month D=day T=marker H=hour m=minute S=second
  DESCRIPTION should include the progguiddesc, and all the presneter information ... this needs to be tweaked
  ORGANIZER should be set to the $ConName and the MAILTO: set to the $ProgramEmail
  TRANSP is set to OPAQUE
  CLASS is set to PUBLIC
  */

  echo "BEGIN:VEVENT\n";  
  echo "UID:$dtstamp-".$schdarray[$i]["dtstart"]."-".$schdarray[$i]["dtend"]."-$i-$DBHostname\n";
  echo "DTSTAMP:$dtstamp\n";
  echo "LAST-MODIFIED:$dtstamp\n";
  echo "CREATED:$dtstamp\n";
  echo "SEQUENCE:$i\n";
  echo "PRIORITY:5\n";
  echo "CATEGORY:$ConName Event Calendar\n";
  echo "SUMMARY:".$schdarray[$i]["title"]." -- ".$schdarray[$i]["trackname"]."\n";
  echo "LOCATION:".$schdarray[$i]["roomname"]."\n";
  echo "DTSTART;TZID=America/New_York:".$schdarray[$i]["dtstart"]."\n"; 
  echo "DTEND;TZID=America/New_York:".$schdarray[$i]["dtend"]."\n"; 
  echo "DESCRIPTION:".$schdarray[$i]["progguiddesc"]."\\n\\n ";
  for ($j=1; $j<=$partrows; $j++) {
    if ($partarray[$j]["sessionid"]!=$schdarray[$i]["sessionid"]) {
      continue;
    }
    echo $partarray[$j]["pubsname"];
    if ($partarray[$j]["pubsname"]!=$partarray[$j]["badgename"]) {
      echo " (".$partarray[$j]["badgename"].")";
    }
    if ($partarray[$j]["moderator"]) {
      echo " - moderator";
    }
    if ($partarray[$j]["volunteer"]) {
      echo " - volunteer";
    }
    if ($partarray[$j]["introducer"]) {
      echo " - introducer";
    }
    if ($partarray[$j]["aidedecamp"]) {
      echo " - assistant";
    }
    echo "\\n\\n ";
  }
  echo "\n";
  echo "ORGANIZER;CN=$ConName:MAILTO:$ProgramEmail\n";
  echo "TRANSP:OPAQUE\n";
  echo "CLASS:PUBLIC\n";
  echo "END:VEVENT\n";
}

// At the end of the file
echo "END:VCALENDAR\n";

?>