<?php
require_once('VendorCommonCode.php');
$_SESSION['return_to_page']='VendorApply.php';
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$title="Vendor Application";
$badgeid=$_SESSION['badgeid'];

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}

if (may_I('SuperVendor')) {
  // Collaps the three choices into one
  if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
  if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
  if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

  if (isset($_POST["partid"])) {
    $badgeid=$_POST["partid"];
  } elseif (isset($_GET["partid"])) {
    $badgeid=$_GET["partid"];
  }
}
  
/* Get the pubsname from the badgeid */
$namequery="SELECT pubsname FROM $ReportDB.Participants WHERE badgeid=$badgeid";
if (($result=mysql_query($namequery,$link)) === false) {
  $message_error.="<BR>".$namequery."Cannot find the name to go with the badgeid.";
}
list($tmp_pubsname)=mysql_fetch_array($result, MYSQL_NUM);
$pubsname=mysql_real_escape_string(htmlspecialchars($tmp_pubsname));

/* Submit goes here */
get_session_from_post();
if ($_POST['update']=="New") {
  $id=insert_session();
  if (!$id OR $id=="") {
    $message_error.="<BR>".$query."\nUnknown error creating record.  Database not updated successfully.";
  }
  $message.="Session record created with Session ID of $id.  Database updated successfully.";
  // 1 is brainstorm; 2 is normal create
  record_session_history($id, $_SESSION['badgeid'], $session['title'], '', 3, $session['status']);
} elseif ($_POST['update']=="Update") {
  $status=update_session();
  if (!$status) {
    $message_error.=$message2; // warning message
    $message_error.="<BR>Unknown error updating record.  Database not updated successfully.";
  } else {
    // 3 is code for unknown edit
    if (!record_session_history($session['sessionid'], $_SESSION['badgeid'], $session['title'], '', 3, $session['status'])) {
      error_log("Error recording session history. ".$message_error);
    } else {
      $message.="Database updated successfully.";
    }
  }
}

vendor_header($title);
    
if (strlen($message)>0) {
  echo "<P id=\"message\"><font color=green>".$message."</font></P>\n";
}
if (strlen($message_error)>0) {
  echo "<P id=\"message2\"><font color=red>".$message_error."</font></P>\n";
  exit(); // If there is a message2, then there is a fatal error.
}

if (may_I('SuperVendor')) {
  //Choose the individual from the database
  select_participant($badgeid, '', "VendorApply.php");
  echo "\n<hr>\n";
  echo "<P>Update for: ($badgeid) $pubsname</P>\n";
}

$query= <<<EOD
SELECT
    sessionid,
    title,
    statusname,
    secondtitle,
    vendorspaceprice,
    if (vendorfeaturetotal,vendorfeaturetotal,0) AS vendorfeaturetotal,
    vendorspaceprice + if (vendorfeaturetotal,vendorfeaturetotal,0) AS "total",
    vendorspaceid,
    vendorspacename,
    vendfeaturename,
    vendfeatureid,
    servicenotes AS servnotes,
    pubsno AS pubno,
    languagestatusid,
    pocketprogtext,
    persppartinfo,
    duration,
    estatten AS atten,
    kidscatid AS kids,
    divisionid,
    trackid AS track,
    typeid AS type,
    roomsetid AS roomset,
    pubstatusid,
    statusid AS status
  FROM
      Sessions S
    JOIN $ReportDB.SessionStatuses USING (statusid)
    JOIN (SELECT
	      sessionid,
	      vendorspaceid,
	      vendorspacename,
	      vendorspaceprice
	    FROM
	        Sessions
	      JOIN $ReportDB.SessionHasVendorSpace USING (sessionid)
	      JOIN $ReportDB.VendorSpaces USING (vendorspaceid)) X USING (sessionid)
    LEFT JOIN (SELECT
	           sessionid,
	           SUM(vendorfeatureprice) AS 'vendorfeaturetotal',
	           GROUP_CONCAT(DISTINCT vendorfeaturename SEPARATOR ', ') as 'vendfeaturename',
	           GROUP_CONCAT(DISTINCT vendorfeatureid SEPARATOR ', ') as 'vendfeatureid'
	         FROM
	             Sessions
	           JOIN $ReportDB.SessionHasVendorFeature USING (sessionid)
	           JOIN $ReportDB.VendorFeatures USING (vendorfeatureid)
                 GROUP BY
	           sessionid) Y USING (sessionid)
  WHERE
    title='$pubsname'
EOD;

if (!$result=mysql_query($query,$link)) {
  $message_error.=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message_error);
  exit();
 }
$rows=mysql_num_rows($result);
if ($rows==0) {
  // Set all the defaults.
  //$session['sessionid'] should be set on creation
  $session['title']=$pubsname; // Title set to the badgeid of the vendor, so the Session element can be tracked.
  $session['secondtitle']=''; // Secondtitle is set to the location of the booth/room, which, on creation is unset.
  $session['total']=0; // Nothing selected yet.
  $session['vendorspace']=0; // Unselected as to yet.
  $session['vendfeatdest']=""; //Unselected as to yet.
  $session['servnotes']=""; // Unspecified as to yet.
  $session['vendoradjustvalue']=""; // Unspecified as to yet.
  $session['vendoradjustnote']=""; // Unspecified as to yet.
  $session['update']="New"; // New entry.

  // These can probably just be ignored.
  $session['pubno']='';
  $session['languagestatusid']=1;
  $session['pocketprogtext']='';
  $session['persppartinfo']='';
  $session['duration']=0;
  $session['atten']='';
  $session['kids']=1;

  // These are queried
  $query= <<<EOD
SELECT
    divisionid,
    trackid AS track,
    typeid AS type,
    roomsetid AS roomset,
    pubstatusid,
    statusid AS status
  FROM
      $ReportDB.Divisions,
      $ReportDB.Tracks,
      $ReportDB.Types,
      $ReportDB.RoomSets,
      $ReportDB.PubStatuses,
      $ReportDB.SessionStatuses
  WHERE
    divisionname='Vendor' and
    trackname='Vendor' and
    typename='Vendor' and
    roomsetname='Vendor' and
    pubstatusname='Vendor' and
    statusname='Vendor Pending'
EOD;

  list($rows,$header_array,$defaultinfo_array)=queryreport($query,$link,$title,$description,0);
  foreach ($header_array as $element) {
    $session[$element]=$defaultinfo_array[1][$element];
  }
} elseif ($rows==1) {
  $session=mysql_fetch_assoc($result);
  $session['update']="Update"; // New entry.
  $session['vendfeatdest']=explode(",",$session['vendfeatureid']);
} else {
  /* More than one result, so fail */
  $message_error.=$query."<BR>Too many results: $rows.<BR>";
  RenderError($title,$message_error);
}

?>

<P>Welcome!  The below is where you will apply to be a vendor at this
   event, or update your requirements.  During different phases of this
   process, you might or might not be able to change what you have bid.
  If there is something you need to change, but cannot change here,
  please, use the email us at <A HREF="mailto: <?php echo VENDOR_EMAIL ?>">
  <?php echo VENDOR_EMAIL ?></A> post-haste, to see if your adjustments
  can be made.</P>

<P>Your current status is: 

<?php 
echo $session['statusname']."<br>\n";
if ($session['secondarytitle']!="") {
  echo "Your current location is: ".$session['secondarytitle'].".<br>\n";
}
if ($session['total']!=0) {
  echo "Your current total is: $".$session['total'].".<br>\n";
}
if ($session['statusname']=="Vendor Approved") {
  echo "Please <A HREF=\"VendorInvoice.php\">Pay Here</A>.</P>\n";
} elseif ($session['statusname']=="Vendor Paid") {
  echo "Thank you for paying.  We are looking forward to seeing you.</P>\n";
} else {
  echo "Should you be accepted for the event, payment will be expected promptly.</P>\n";
}
?>
<DIV class="formbox">
  <FORM name="sessform" class="bb"  method=POST action="VendorApply.php">
    <INPUT type="hidden" name="partid" value="<?php echo $badgeid; ?>">
    <?php foreach ($session as $key => $value) { echo "<INPUT type=\"hidden\" name=\"$key\" value=\"$value\">\n"; } ?>
    <TABLE><COL><COL>
      <TR>
        <TD>
          <SPAN><LABEL for="vendorspace">Space Requested: </LABEL>
            <SELECT name="vendorspace">
              <?php $query="SELECT vendorspaceid, vendorspacename from $ReportDB.VendorSpaces WHERE conid=".$_SESSION['conid']." ORDER BY display_order";
              populate_select_from_query($query, $session["vendorspaceid"], "SELECT", FALSE); ?>
            </SELECT><br><br>
          </SPAN>
        </TD>
      </TR>
<?php /*
        <TR>
        <TD class="nospace">
        <DIV class="thinbox" style="margin-top: 1em; width: 37em; font-size: 85%">
            <DIV class="blockwbox" style="width: 33em; padding-left: 0.5em; padding-right: 0.5em; margin: 0.5em"> <!-- VendorFeatures Box; -->
		 <DIV class="blockstitle"><LABEL>Extras: </LABEL></DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-celltitle"><LABEL for="featsrc">Possible Extras</LABEL></SPAN>
                        <SPAN class="tab-cell"><LABEL>&nbsp;</LABEL></SPAN>
                        <SPAN class="tab-celltitle"><LABEL for="featdest[]">Selected Extras</LABEL></SPAN>
                        </DIV>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                             <SELECT class="selectfwidth" id="featsrc" name="featsrc" size=6 multiple>
                                <?php populate_multisource_from_table("$ReportDB.VendorFeatures", $session["vendfeatdest"]); ?></SELECT>
                             </SPAN>
                        <SPAN class="thickobject">
                            <BUTTON onclick="fadditems(document.sessform.featsrc,document.sessform.featdest)"
                                name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                            <BUTTON onclick="fdropitems(document.sessform.featsrc,document.sessform.featdest)"
                                name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                            </SPAN>
                    <DIV class="tab-row">
                        <SPAN class="tab-cell">
                            <SELECT class="selectfwidth" id="featdest" name="vendfeatdest[]" size=6 multiple >
                                <?php populate_multidest_from_table("$ReportDB.VendorFeatures", $session["vendfeatdest"]); ?></SELECT>
                            </SPAN>
                        </DIV>
                </DIV> <!-- VendorFeatures --> 
      */ ?>
      <TR>
        <TD>
          <SPAN><LABEL for="vendfeatdest">Extras (Use the Control-Click to select multiple extras.):</LABEL><BR>
            <SELECT name="vendfeatdest[]" size=8 multiple >
              <?php populate_multiselect_from_table("$ReportDB.VendorFeatures", $session["vendfeatdest"]); ?>
            </SELECT><br><br>
          </SPAN>
        </TD>
      </TR>
      <TR>
        <TD>
          <SPAN><LABEL for="vendorloadin">What time do you prefer for load in?  Note: You are not guaranteed a requested load-in time.</LABEL><BR>
            <SELECT name="vendorloadin">
              <?php $query="SELECT vendorloadinid, vendorloadinname from $ReportDB.VendorLoadin WHERE conid=".$_SESSION['conid']." ORDER BY display_order";
              populate_select_from_query($query, $session["vendorloadinid"], "SELECT", FALSE); ?>
            </SELECT><br>
            &nbsp;&nbsp; * Are arriving after 7:30pm on Friday and will be responsible for own load-in.  The function rooms will be locked at 8pm on Friday, and will not open again until 9am on Saturday.<br><br>
          </SPAN>
        </TD>
      </TR>
      <TR>
        <TD>
          <SPAN><LABEL for="servnotes" id="servnotes">Please tell us what you sell, why should you be accepted to vend at this event, and any other important information that we should know:</LABEL><BR>
	    <TEXTAREA cols="70" rows="5" name="servnotes"><?php echo htmlspecialchars($session["servnotes"],ENT_NOQUOTES); ?></TEXTAREA>
          </SPAN>
        </TD>
      </TR>
      <TR>
        <TD>
          <INPUT type=submit ID="sButtonBottom" value="Save">
        </TD>
      </TR>
    </TABLE>
  </FORM>
</DIV>
<?php correct_footer(); ?>
