<?php
// This page has two completely different entry points from a user flow standpoint:
//   1) Beginning of send email flow -- start to specify parameters
//   2) After verify -- 'back' can change parameters -- 'send' fire off email sending code
require_once('email_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
global $title, $message, $link;
$conid=$_SESSION['conid'];
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

if (isset($_POST['sendto'])) { // page has been visited before so restore previous values to form
  $email=get_email_from_post();
  } else { // page hasn't just been visited
    $email=set_email_defaults();
  }
$message_warning="";
if ($_POST['navigate']!='send') {
    render_send_email($email,$message_warning);
    exit(0);
    }
// Queue email to be sent into db.  Cron job will actually send it at a pace not to trigger outgoing spam filters. 
$title="Staff Send Email";
$subst_list=array("\$BADGEID\$","\$FIRSTNAME\$","\$LASTNAME\$","\$EMAILADDR\$","\$PUBNAME\$","\$BADGENAME\$","\$SCHEDULE\$");
$email=get_email_from_post();
$query="SELECT emailtoquery FROM EmailTo where emailtoid=".$email['sendto'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailto=mysql_fetch_array($result,MYSQL_ASSOC);
$query=$emailto['emailtoquery'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$i=0;
while ($recipientinfo[$i]=mysql_fetch_array($result,MYSQL_ASSOC)) {
    $i++;
    }
$recipient_count=$i;
for ($i=0; $i<$recipient_count; $i++) {

  // variablized for substitution
  $individual=$recipientinfo[$i]['badgeid'];

  /* This query pulls the schedule information for an individual, and
   then collects it, and stuffs it into a single variable, for
   expansion later. */
  $query = <<<EOD
SELECT 
    DISTINCT CONCAT(S.title, 
        if((moderator=1),' (moderating)',''), 
        if ((aidedecamp=1),' (assisting)',''), 
        if((volunteer=1),' (outside wristband checker)',''), 
        if((introducer=1),' (announcer/inside room attendant)',''),
        ' - ',
        DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),
        ' - ',
        CASE
          WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
          WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
          ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
          END,
        ' in room ',
	roomname) as Title,
    P.pubsname
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN ParticipantOnSession POS USING (sessionid)
    JOIN $ReportDB.Participants P USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
  WHERE
    permrolename in ('Participant','General','Programming') AND
    UHPR.conid=$conid AND
    POS.badgeid='$individual'
  ORDER BY
    starttime

EOD;

  // Retrieve query
  list($rows,$schedule_header,$schedule_array)=queryreport($query,$link,$title,$description,0);
  for ($j=1; $j<=$rows; $j++) {
    $recipientinfo[$i]['schedule'].=$schedule_array[$j]['Title']."
";
  }
 }
$query="SELECT emailfromaddress FROM EmailFrom where emailfromid=".$email['sendfrom'];
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailfrom=mysql_result($result,0);
$x=$email['sendcc'];
$query="SELECT emailccaddress FROM EmailCC where emailccid=$x";
if (!$result=mysql_query($query,$link)) {
    db_error($title,$query,$staff=true); // outputs messages regarding db error
    exit(0);
    }
$emailcc=mysql_result($result,0);
$goodCount=0;
$badCount=0;
unset($arrayOfGood);
unset($arrayOfBad);
for ($i=0; $i<$recipient_count; $i++) {
    $name=(strlen($recipientinfo[$i]['pubsname'])>0)?$recipientinfo[$i]['pubsname']:$recipientinfo[$i]['firstname']." ".$recipientinfo[$i]['lastname'];
    if (!filter_var($recipientinfo[$i]['email'],FILTER_VALIDATE_EMAIL)) {
             // bad email address
             $badCount++;
             $arrayOfBad[]=array('badgeid'=>$recipientinfo[$i]['badgeid'],'name'=>$name,'email'=>$recipientinfo[$i]['email']);
             }
        else {
             $goodCount++;
             $arrayOfGood[]=array('badgeid'=>$recipientinfo[$i]['badgeid'],'name'=>$name,'email'=>$recipientinfo[$i]['email']);
             $repl_list=array($recipientinfo[$i]['badgeid'],$recipientinfo[$i]['firstname'],$recipientinfo[$i]['lastname']);
             $repl_list=array_merge($repl_list,array($recipientinfo[$i]['email'],$recipientinfo[$i]['pubsname']));
	     $repl_list=array_merge($repl_list,array($recipientinfo[$i]['badgename'],$recipientinfo[$i]['schedule']));
             $emailverify['body']=str_replace($subst_list,$repl_list,$email['body']);
             $query="INSERT INTO EmailQueue (emailqueueid, emailto, emailfrom, emailcc, emailsubject, body, status) ";
             // to address
             $query.="values(null, \"".mysql_real_escape_string($recipientinfo[$i]['email'],$link)."\",";
             // from address
             $query.="\"".mysql_real_escape_string($emailfrom,$link)."\",";
             // cc (bcc) address
             $query.="\"".mysql_real_escape_string($emailcc,$link)."\",";
             // subject
             $query.="\"".mysql_real_escape_string($email['subject'],$link)."\",";
             // body
             $query.="\"".mysql_real_escape_string(wordwrap(preg_replace("/(?<!\\r)\\n/","\r\n",$emailverify['body']),70,"\r\n"),$link)."\",";
             // status 1 is unsent (queued)
             $query.="1);";
             if (!$result=mysql_query($query,$link)) {
                 db_error($title,$query,$staff=true); // outputs messages regarding db error
                 exit(0);
                 }
             }
    }   
renderQueueEmail($goodCount,$arrayOfGood,$badCount,$arrayOfBad);
?>
