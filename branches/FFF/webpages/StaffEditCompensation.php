<?php
require_once('StaffCommonCode.php');
global $link;
$title="Compensation Update";
$conid=$_SESSION['conid'];
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

// Check to see if page can be displayed
if (!may_I("SuperLiaison") AND !may_I("Treasurer")) {
  $message_error ="Alas, you do not have the proper permissions to view this page.";
  $message_error.=" If you think this is in error, please, get in touch with an administrator.";
  RenderError($title,$message_error);
  exit();
}

// Assign the typename and selpartid if it was passed in
$typename='';
if (isset($_POST['typename']) AND ($_POST['typename'] != '')) {$typename=$_POST['typename'];}
elseif (isset($_GET['typename']) AND ($_GET['typename'] != '')) {$typename=$_GET['typename'];}

if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

if (isset($_POST["partid"])) {
  $selpartid=$_POST["partid"];
} elseif (isset($_GET["partid"])) {
  $selpartid=$_GET["partid"];
} else {
  $selpartid=0;
}

// If neither typename nor selpartid was picked, show the standard choose user interface and exit
if (($typename=='') AND ($selpartid==0)) {
  //Choose the individual from the database
  $description ="<P>Choose the appropriate individual to set their compensation, or see the";
  $description.=" <A HREF=\"PresenterCompensation.php\">Presenter Compensation</A> table.</P>\n";
  topofpagereport($title,$description,$additionalinfo);
  select_participant($selpartid, "'Yes'", "StaffEditCompensation.php");
  correct_footer();
  exit();
}

$wherestring="";
// If typename was not picked
if ($typename!='') {
  $wherestring="WHERE\n    comptypename='$typename'";
}
// Get the comptypeid, comptypename, and comptypedescription for the fill-in fields
$query = <<<EOD
SELECT 
    comptypeid,
    comptypename,
    comptypedescription
  FROM
      $ReportDB.CompensationTypes
  $wherestring
  ORDER BY
    comptypeid;
EOD;

// Retrieve query
list($comptypecount,$comptype_header_array,$comptype_array)=queryreport($query,$link,$title,$description,0);

// Do updates, if there were any from the previous visit to this page
if ($_POST['update']=="please") {

  // Check for existing record updates, and if so, do them.
  if (isset($_POST['compids']) and ($_POST['compids']!='')) {
    $checkcompids=explode(",", $_POST['compids']);
    foreach ($checkcompids as $compid) {

      // zeros the values.
      $compamount='';
      $compdescription='';
      $pairedvalue_array=array();

      // Checks to see if there was a value passed in for either.
      if ($_POST[$compid] != $_POST['was_'.$compid]) {
	$compamount="compamount='".mysql_real_escape_string(stripslashes($_POST[$compid]))."'";
      }
      if ($_POST['d_'.$compid] != $_POST['was_d_'.$compid]) {
	$compdescription="compdescription='".mysql_real_escape_string(stripslashes($_POST['d_'.$compid]))."'";
      }

      // Checks to see if either are set above, for the update.
      if (($compamount!='') or ($compdescription!='')) {
	$pairedvalue_array=array($compamount,$compdescription);
      } elseif ($compamount!='') {
	$pairedvalue_array=array($compamount);
      } elseif ($compdescription!='') {
	$pairedvalue_array=array($compdescription);
      }

      // Do the update, if there is anything to update.
      if (!empty($pairedvalue_array)) {
	$message.=update_table_element($link,$title,"$ReportDB.Compensation",$pairedvalue_array,"compid",$compid);
      }
    }
  }

  // Check for new record entries, and, if they exist, insert them.
  for ($i=1; $i<=$comptypecount; $i++) {

    // zeros the values.
    $element_array=array();
    $value_array=array();
    
    // Checks to see if there was a value passed in for either.
    if ((isset($_POST['new_'.$i])) and
	($_POST['new_'.$i]!='') and
	(isset($_POST['d_new_'.$i])) and
	($_POST['d_new_'.$i]!='')) {
      $element_array=array('conid','badgeid','comptypeid','compamount','compdescription');
      $value_array=array($conid, $selpartid, $i,htmlspecialchars_decode($_POST['new_'.$i]),htmlspecialchars_decode($_POST['d_new_'.$i]));
    } elseif ((isset($_POST['new_'.$i])) and ($_POST['new_'.$i]!='')) {
      $element_array=array('conid','badgeid','comptypeid','compamount');
      $value_array=array($conid, $selpartid, $i,htmlspecialchars_decode($_POST['new_'.$i]));
    } elseif ((isset($_POST['d_new_'.$i])) and ($_POST['d_new_'.$i]!='')) {
      $element_array=array('conid','badgeid','comptypeid','compdescription');
      $value_array=array($conid, $selpartid, $i,htmlspecialchars_decode($_POST['d_new_'.$i]));
    }

    // Do the insert if there is anything to insert.
    if (!empty($value_array)) {
      $message.=submit_table_element($link, $title, "$ReportDB.Compensation", $element_array, $value_array);
    }
  }
}

// Get the pubsname, to make things more readable
$query="SELECT pubsname from Participants where badgeid=$selpartid";
if (!$result=mysql_query($query,$link)) {
  $message="Badgeid does not exist in Participants, please try again:".$query;
  RenderError($title,$message);
  exit();
}
$pubsname=mysql_result($result,0);

// Pull all the compensation entries, if any exist for the participant for this event.
$query=<<<EOD
SELECT
    compid,
    comptypeid,
    compamount,
    compdescription
  FROM
      $ReportDB.Compensation
  WHERE
    conid=$conid AND
    badgeid=$selpartid
EOD;

if (($result=mysql_query($query,$link))===false) {
  $message="<P>Error reading Compensation table.</P>\n<P>";
  $message.=$query;
  RenderError($title,$message);
  exit ();
}

// build the appropriate informational arrays to be presented in the HTML
$comprows=mysql_num_rows($result);
$max_length=70;
for ($i=1; $i<=$comprows; $i++) {
  $tmp_comp_array=mysql_fetch_assoc($result);
  $comp_array[$tmp_comp_array['comptypeid']]['compid']=$tmp_comp_array['compid'];
  $compid_array[]=$tmp_comp_array['compid'];
  $comp_array[$tmp_comp_array['comptypeid']]['amount']=$tmp_comp_array['compamount'];
  $comp_array[$tmp_comp_array['comptypeid']]['notes']=$tmp_comp_array['compdescription'];
  if (strlen($tmp_comp_array['compdescription']) > $max_length) {
    $max_length=strlen($tmp_comp_array['compdescription']);
  }
}

if ($comprows > 0) {
  $compid_string=implode(",",$compid_array);
} else {
  $compid_string='';
}

$description ="<P>Enter the appropriate compensation for $pubsname or see the";
$description.=" <A HREF=\"PresenterCompensation.php\">Presenter Compensation</A> table.</P>\n";

/* From here, it is just the page information.
   The begin page starts it.
   The hidden inputs are the participant's id, so you aren't dropped
   back into the selection table, the compid_string, so we know which
   IDs to check to update, and the value that says, yes, the update
   button was selected.  Once the table is started, the values are
   first a row of description, then subsequent rows of each of the
   types of compensation, their values (if there are any, both echoed
   to the field, and passed as a hidden to be compaired against) and
   the descriptions (done the same way) I tried to pull things out so
   they were more readable. */
topofpagereport($title,$description,$additionalinfo);
echo "<P>$message</P>\n";
echo "<FORM name=\"updatecomp\" method=POST action=\"StaffEditCompensation.php\">\n";
echo "<INPUT type=submit name=submit class=SubmitButton value=Update>\n";
echo "<INPUT type=hidden name=partid value=$selpartid>\n";
echo "<INPUT type=hidden name=compids value=$compid_string>\n";
echo "<INPUT type=hidden name=update value=please>\n";
echo "<TABLE border=1>\n";
echo "  <TR>\n    <TH>Type</TH>\n    <TH>";
echo "Notes</TH>\n  </TR>\n";
for ($i=1; $i<=$comptypecount; $i++) {
  $compid=$comp_array[$comptype_array[$i]['comptypeid']]['compid'];
  echo "  <TR>    <TD colspan=2>";
  echo $comptype_array[$i]['comptypedescription'];
  echo "    </TD>  </TR>  <TR>\n    <TD><B>";
  echo $comptype_array[$i]['comptypename'];
  echo "</B>: <INPUT type=text name=\"";
  if (isset($compid) AND ($compid!='')) {
    echo $compid;
  } else {
    echo "new_";
    echo $comptype_array[$i]['comptypeid'];
  }
  echo "\" size=10 value=\"";
  echo $comp_array[$comptype_array[$i]['comptypeid']]['amount'];
  echo "\">";
  if (isset($compid) AND ($compid!='')) {
    echo "\n      <INPUT type=hidden name=\"was_$compid\" value=\"";
    echo $comp_array[$comptype_array[$i]['comptypeid']]['amount'];
    echo "\">";
  }
  echo "</TD>\n    <TD>";
  echo "<INPUT type=text name=\"d_";
  if (isset($compid) AND ($compid!='')) {
    echo $compid;
  } else {
    echo "new_";
    echo $comptype_array[$i]['comptypeid'];
  }
  echo "\" size=$max_length value=\"";
  echo $comp_array[$comptype_array[$i]['comptypeid']]['notes'];
  echo "\">";
  if (isset($compid) AND ($compid!='')) {
    echo "\n      <INPUT type=hidden name=\"was_d_$compid\" value=\"";
    echo $comp_array[$comptype_array[$i]['comptypeid']]['notes'];
    echo "\">";
  }
  echo "</TD>\n  </TR>\n";
}
echo "</TABLE>\n";
echo "<INPUT type=submit name=submit class=SubmitButton value=Update>\n";
correct_footer();
exit();
?>