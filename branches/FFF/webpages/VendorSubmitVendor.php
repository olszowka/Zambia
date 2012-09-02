<?php
require_once('VendorCommonCode.php');
// Localisms
global $message,$message_error,$message2;
$conid=$_SESSION['conid'];
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

$_SESSION['return_to_page']='VendorWelcome.php';
$title="Submit Vendor Application";

// Get the permroleid and name for assigning as Vendor
$query= <<<EOD
SELECT
    permroleid,
    permrolename
  FROM
      $ReportDB.PermissionRoles
EOD;
if (($result=mysql_query($query,$link))===false) {
  $message_error="Error retrieving data from database<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
if (0==($rows=mysql_num_rows($result))) {
  $message_error="Database query did not return any rows.<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
for ($i=1; $i<=$rows; $i++) {
  $permrole_arr[$i]=mysql_fetch_array($result,MYSQL_ASSOC);
  if ($permrole_arr[$i]['permrolename'] == "Vendor") {
    $permstring="permroleid".$permrole_arr[$i]['permroleid'];
    $participant_arr[$permstring]="checked";
  }
}

// If the information has already been added, and we are
// on the return loop, add the Vendor to the database.
if ((isset ($_POST['update'])) and ($_POST['update']=="Yes")) {
  $_POST['partid']=$_SESSION['badgeid'];
  $_POST['badgename']=$_POST['pubsname'];
  if (may_I('BrainstormSubmit')) {
    if (($_POST['password']!="") and ($_POST['password']==$_POST['cpassword'])) {
      $_POST['password']=md5($_POST['cpassword']);
    } else {
      $message_error.="Passwords do not match each other.  Please contact the ".VENDOR_EMAIL." for further help.";
    }
    list($message,$message_error)=create_participant ($_POST,$permrole_arr);
    $message.="Account Created.  Click on the Login Number on the <A HREF=\"VendorSearch.php\">List</A> page, to login with your new password.";

    /* Redirect browser for now.  Should actually log them in, because
       they need that much more hurding through the system. */
    require ('renderVendorWelcome.php'); 
    exit;
  } else {
    if (($_POST['npassword']!="") and ($_POST['npassword']==$_POST['ncpassword'])) {
      $_POST['password']=md5($_POST['npassword']);
    } else {
      $message_error.="Passwords do not match each other.  Not updating password field.";
    }
    edit_participant ($_POST,$permrole_arr);
  }
}

// Begin the display
vendor_header($title);

if (strlen($message)>0) {
  echo "<P id=\"message\"><font color=green>".$message."</font></P>\n";
}
if (strlen($message_error)>0) {
  echo "<P id=\"message2\"><font color=red>".$message_error."</font></P>\n";
  exit(); // If there is a message2, then there is a fatal error.
}

// Set the values.
if (may_I('BrainstormSubmit')) {
  $participant_arr['interested']="0"; // 0 means has not replied
  $participant_arr['prognotes']="Submitted via Brainstorm"; 
  $participant_arr['regtype']="Vendor";
} else {
  //Get Participant information for updating
  $selpartid=$_SESSION['badgeid'];
  $query= <<<EOD
SELECT
    CD.badgeid,
    CD.firstname,
    CD.lastname,
    CD.badgename,
    CD.phone,
    CD.email,
    CD.postaddress1,
    CD.postaddress2,
    CD.postcity,
    CD.poststate,
    CD.postzip,
    CD.regtype,
    P.bestway,
    P.interested,
    P.pubsname,
    P.altcontact,
    P.prognotes,
    P.password,
    group_concat(UHPR.permroleid) as 'permroleid_list'
  FROM 
      $ReportDB.CongoDump CD
    JOIN $ReportDB.Participants P USING (badgeid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
  WHERE
    CD.badgeid='$selpartid'
EOD;
  // Retrieve query
  list($rows,$header_array,$part_arr)=queryreport($query,$link,$title,$description,0);

  // Get a set of bioinfo, and map it to the appropriate $participant_arr.
  $bioinfo=getBioData($selpartid);

  /* We are only updating the raw en-us bios here, so only a 1-depth
     search happens on biotypename. */
   $biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
   $biolang='en-us'; //  for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {
   for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
          
     // Setup for keyname, to collapse all three variables into one passed name.
     $biotype=$bioinfo['biotype_array'][$i];
     // $biolang=$bioinfo['biolang_array'][$j];
     // $biostate=$bioinfo['biostate_array'][$k];
     $keyname=$biotype."_".$biolang."_".$biostate."_bio";

     // Clear the values.
     $part_arr[1][$keyname]=$bioinfo[$keyname];
   }
}

?>

<script language="javascript" type="text/javascript">
var phase1required=new Array("pubsname", "firstname", "lastname", "phone", "email", "web_en-us_raw_bio",
                             "postaddress1", "postcity", "poststate", "postzip",
                             "uri_en-us_raw_bio", "password", "cpassword");
var currentPhase, unhappyColour, happyColor;

function colourCodeElements(phaseName, unhappyC, happyC) {
  var i, o;

  currentPhase = phaseName;
  unhappyColor = unhappyC;
  happyColor = happyC;
  eval('var requiredElements = ' + phaseName + 'required');
  if (requiredElements == null) return;
  for (i = 0; i < requiredElements.length; i++) {
    o = document.getElementById(requiredElements[i]);
    if (o != null) {
      o.style.color = "red";
    }
  }
}

function checkSubmitButton() {
  var i, j, o, relatedO, controls;
  var enable = true;
  
  eval('var requiredElements = ' + currentPhase + 'required');
  if (requiredElements == null) return;
  for (i = 0; i < requiredElements.length; i++) {
    controls = document.getElementsByName(requiredElements[i]);
    if (controls != null) {
      for (j = 0; j < controls.length; j++) {

	o = controls[j];
	relatedO = document.getElementById(requiredElements[i]);
	switch (o.tagName) {
	case "LABEL":
	  break;
	case "SELECT":
	  if (o.options[o.selectedIndex].value == 0) {
	    enable = false;
	    relatedO.style.color = unhappyColor;
	  } else {
	    relatedO.style.color = happyColor;
	  }
	  break;
	case "TEXTAREA":
	  if (o.value == "") {
	    enable = false;
	    relatedO.style.color = unhappyColor;
	  } else {
	    relatedO.style.color = happyColor;
	  }
	  break;
	case "INPUT":
	  if (o.value == "") {
	    enable = false;
	    relatedO.style.color = unhappyColor;
	  } else {
	    relatedO.style.color = happyColor;
	  }
	  break;
	}
      }
    }
  }
  var saveButton = document.getElementById("sButtonTop");
  if (saveButton != null) {
    saveButton.disabled = !enable;
  }	
  var saveButton = document.getElementById("sButtonBottom");
  if (saveButton != null) {
    saveButton.disabled = !enable;
  }	
}
</script>

<DIV class="formbox">
  <FORM name="vendorform" class="bb"  method=POST action="VendorSubmitVendor.php">
    <INPUT type="submit" ID="sButtonTop" value="Save">&nbsp;
    <INPUT type="reset" value="Reset">
    <INPUT type="hidden" name="update" value="Yes">
    <INPUT type="hidden" name="interested" <?php echo "value=\"".$part_arr[1]['interested']."\""; ?> >
    <INPUT type="hidden" name="prognotes" <?php echo "value=\"".$part_arr[1]['prognotes']."\""; ?> >
    <INPUT type="hidden" name="regtype" <?php echo "value=\"".$part_arr[1]['regtype']."\""; ?> >
    <?php foreach ($participant_arr as $key => $value) { echo "<INPUT type=\"hidden\" name=\"$key\" value=\"$value\">\n"; } ?>
    <P>Note: items in red must be completed before you can save.</P>
    <P>Please make sure all your information is valid, there are no double checks.
       If they are not valid they are not going to resolve properly the chance that we might
       see you at the event decreases exponentially.</P>
    <TABLE>
      <TR>
        <TD class="form1">
          <LABEL for="pubsname" ID="pubsname">Business Name:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="pubsname" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['pubsname']!="") echo "value=\"".$part_arr[1]['pubsname']."\""; ?> ></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="firstname" ID="firstname">Contact Person First Name:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="firstname" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['firstname']!="") echo "value=\"".$part_arr[1]['firstname']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="lastname" ID="lastname">Contact Person Last Name:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="lastname" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['lastname']!="") echo "value=\"".$part_arr[1]['lastname']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="phone" ID="phone">Contact Phone Number:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="phone" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['phone']!="") echo "value=\"".$part_arr[1]['phone']."\""; ?> ></TD></TR>
      <?php if ($part_arr[1]['password']!="") { ?>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="npassword" ID="npassword">Change Password</LABEL><BR>
          <INPUT type="password" size="10" name="npassword"><TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="ncpassword" ID="ncpassword">Confirm New Password</LABEL><BR>
          <INPUT type="password" size="10" name="cpassword"></TD></TR>
      <?php } else { ?>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="password" ID="password">Create Password</LABEL><BR>
          <INPUT type="password" size="10" name="password" onKeyPress="return checkSubmitButtion();"><TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="cpassword" ID="cpassword">Confirm New Password</LABEL><BR>
          <INPUT type="password" size="10" name="cpassword" onKeyPress="return checkSubmitButtion();"></TD></TR>
      <?php } ?>
      <TR>
        <TD class="form1">&nbsp;<BR>
	<LABEL for="email" ID="email">Contact Email Address (all lower case):</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="email" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['email']!="") echo "value=\"".$part_arr[1]['email']."\""; ?> ></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="web_en-us_raw_bio" ID="web_en-us_raw_bio">Description of Product or Business Suitable for Publication</LABEL><BR>
          <TEXTAREA cols="50" rows="5" NAME="web_en-us_raw_bio" onKeyPress="return checkSubmitButton();"
          ><?php if ($part_arr[1]['web_en-us_raw_bio']!="") echo $part_arr[1]['web_en-us_raw_bio']; ?></TEXTAREA></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
	  <LABEL for="uri_en-us_raw_bio" ID="uri_en-us_raw_bio">URL/Website (in the form of: &lt;A HREF="http://mysite.com"&gt;My Site&lt;/A&gt;)</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="uri_en-us_raw_bio" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['uri_en-us_raw_bio']!="") echo "value=\"".$part_arr[1]['uri_en-us_raw_bio']."\""; ?> ></TD></TR>
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="postaddress1" ID="postaddress1">Address:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="postaddress1" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['postaddress1']!="") echo "value=\"".$part_arr[1]['postaddress1']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
	  <LABEL for="postaddress2" ID="postaddress2">(second line):</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="postaddress2" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['postaddress2']!="") echo "value=\"".$part_arr[1]['postaddress2']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="postcity" ID="postcity">City:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="postcity" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['postcity']!="") echo "value=\"".$part_arr[1]['postcity']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="poststate" ID="poststate">State:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="poststate" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['poststate']!="") echo "value=\"".$part_arr[1]['poststate']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="postzip" ID="postzip">ZIP Code:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="postzip" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['postzip']!="") echo "value=\"".$part_arr[1]['postzip']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="altcontact" ID="altcontact">Alternative form of Contact:</LABEL><BR>
          <INPUT TYPE="TEXT" NAME="altcontact" size="50" onKeyPress="return checkSubmitButton();"
          <?php if ($part_arr[1]['altcontact']!="") echo "value=\"".$part_arr[1]['altcontact']."\""; ?> ></TD></TR> 
      <TR>
        <TD class="form1">&nbsp;<BR>
          <LABEL for="bestway" ID="bestway">Best way to contact:</LABEL><BR>
          <SELECT name="bestway">
                    <OPTION value="Email">Email</OPTION>
                    <OPTION value="Phone" selected>Phone</OPTION>
                    <OPTION value="SMS">SMS</OPTION>
                    <OPTION value="Postal mail">Postal mail</OPTION>
                    <OPTION value="Twitter DM">Twitter DM</OPTION>
                    <OPTION value="Fet Life">Fet Life</OPTION>
                    <OPTION value="Facebook">Facebook</OPTION>
                    <OPTION value="G+">G+</OPTION>
                    <OPTION value="Instant Messenger">Instant Messenger</OPTION>
                    </SELECT>
    </TABLE>
    <BR>
    <INPUT type="submit" ID="sButtonBottom" value="Save">&nbsp;
    <INPUT type="reset" value="Reset">
  </FORM>
</DIV>
<script language="javascript" type="text/javascript">
  colourCodeElements("phase1", "red", "green");
  checkSubmitButton();
</script>
<?php correct_footer(); ?>
