<?php
global $participant,$message,$message_error,$message2,$congoinfo;
$title="My Profile";

// initialize db, check login, set $badgeid from session
require_once('PartCommonCode.php'); 

if (getCongoData($badgeid)!=0) {
  RenderError($title,$message_error);
  exit();
}

// if the bio, progbio or pubsname is passed (updated) record them.
if (isset($bio)) {
  $participant["bio"]=$bio;
}
if (isset($progbio)) {
  $participant["progbio"]=$progbio;
}
if (isset($pubsname)) {
  $participant["pubsname"]=$pubsname;
}

// Set waspubsname before the possiblity of copying it from badgename
$waspubsname=$participant["pubsname"];

// if no pubsname, copy it from badgename
if (strlen($participant["pubsname"])<1) {
  $participant["pubsname"]=$congoinfo["badgename"];
}

// Begin the page display
participant_header($title);

// Illuminate any errors
if ($message_error!="") {
  echo "<P class=\"errmsg\">$message_error</P>";
}
if ($message!="") { 
  echo "<P class=\"regmsg\">$message</P>";
}

// Begin the form
?>

<FORM name="partform" method=POST action="SubmitMyContact.php">
  <div id="update_section">
    <div class="divlistbox">
      <span class="spanlabcb">I am interested and able to participate in 
        programming for <?php echo CON_NAME; ?>&nbsp;</span>
      <?php $int=$participant['interested']; ?>
      <span class="spanvalcb"><SELECT name=interested class="yesno">
            <OPTION value=0 <?php if ($int==0) {echo "selected";} ?> >&nbsp</OPTION>
            <OPTION value=1 <?php if ($int==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($int==2) {echo "selected";} ?> >No</OPTION></SELECT>
          </span>
      </div>
    <div id="bestway">
      <span class="spanlabcb">Preferred mode of contact&nbsp;</span>
      <div id="bwbuttons">
<?php if (strlen($congoinfo['email'])>0) { ?>
        <div id="bwemail">
          <input name="bestway" id="bwemailRB" value="Email" type="radio"
<?php
    if ($participant["bestway"]=="Email") {echo " checked ";}
?>
              >
          <label for="bwemailRB">Email</label>
          </div>
<?php } ?>
<?php if (strlen($participant['altcontact'])>0) { ?>
        <div id="bwalt">
          <input name="bestway" id="bwaltRB" value="AltContact" type="radio"
<?php
    if ($participant["bestway"]=="AltContact") {echo " checked ";}
?>
              >
          <label for="bwaltRB">Alternative Contact</label>
          </div>
<?php } ?>
<?php if (strlen($congoinfo['postaddress1'])>0) { ?>
        <div id="bwpmail">
          <input name="bestway" id="bwpmailRB" value="PostalMail" type="radio"
<?php
    if ($participant["bestway"]=="PostalMail") {echo " checked ";}
?>
              >
          <label for="bwpmailRB">Postal Mail</label>
          </div>
<?php } ?>
<?php if (strlen($congoinfo['phone'])>0) { ?>
        <div id="bwphone">
          <input name="bestway" id="bwphoneRB" value="Phone" type="radio"
<?php
    if ($participant["bestway"]=="Phone") {echo " checked ";}
?>
              >
          <label for="bwphoneRB">Phone</label>
          </div>
<?php } ?>
        </div>
      </div>
    <div class="password">
      <span class="password2">Change Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="password"></span>
      </div>
    <div class="password">
      <span class="password2">Confirm New Password&nbsp;</span>
      <span class="value"><INPUT type="password" size="10" name="cpassword"></span>
      </div>
    </div>
    <DIV >
  <div id="congo_section" class="border2222">
    <div class="congo_table">
     <div class="congo_data">
      <span class="label">Badge ID&nbsp;</span>
      <span class="value"><?php echo $badgeid; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">First Name&nbsp;</span>
      <span class="value"><?php echo $congoinfo["firstname"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Last Name&nbsp;</span>
      <span class="value"><?php echo $congoinfo["lastname"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Badge Name&nbsp;</span>
      <span class="value"><?php echo $congoinfo["badgename"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Publications Name&nbsp;</span>
      <span class="value"><?php echo $participant["pubsname"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Phone Info&nbsp;</span>
      <span class="value"><?php echo $congoinfo["phone"]; ?></span>
      </div>
    <div class="congo_data">
      <span class="label">Email Address&nbsp;</span>
      <span class="value"><?php echo $congoinfo["email"]; ?></span>
    </div>
    <div class="congo_data">
      <span class="label">Alternative Contact&nbsp;</span>
      <span class="value"><?php echo $participant["altcontact"]; ?></span>
    </div>
    <div class="congo_data">
      <span class="label">Postal Address&nbsp;</span>
      <span class="value"><?php echo $congoinfo["postaddress1"]; ?></span>
      </div>
<?php if (strlen($congoinfo['postaddress2'])>0) { ?>      
    <div class="congo_data">
      <span class="label">&nbsp;</span>
      <span class="value"><?php echo $congoinfo["postaddress2"]; ?></span>
      </div>
      <?php } ?>
<?php if ((strlen($congoinfo['postcity'])>0) or (strlen($congoinfo['poststate'])>0) or (strlen($congoinfo['postzip'])>0)) { ?>      
    <div class="congo_data">
      <span class="label">&nbsp;</span>
      <span class="value"><?php echo "{$congoinfo['postcity']}, {$congoinfo['poststate']} {$congoinfo['postzip']}"; ?></span>
      </div>
      <?php } ?>
<?php if (strlen($congoinfo['postcountry'])>0) { ?>      
    <div class="congo_data">
      <span class="label">&nbsp;</span>
      <span class="value"><?php echo $congoinfo['postcountry']; ?></span>
      </div>
      <?php } ?>
  </div>
  <P class="congo-note">Please confirm your contact information.  If it is 
not correct, contact <A href="mailto:<?php echo 
REG_EMAIL; ?>">registration</a> with your 
current information. This data is downloaded periodically from the registration database, and should be correct within a week.
</div>

<?php
// Deal with the bio information
$bio=MAX_BIO_LEN;
$progbio=MAX_PROG_BIO_LEN;
if (may_I('EditBio')) {
  echo "<HR>\n<BR>\n";
  echo "Your name as you wish to have it published&nbsp;&nbsp;";
  echo "<INPUT type=\"text\" size=\"20\" name=\"pubsname\" ";
  echo "value=\"".htmlspecialchars($participant["pubsname"],ENT_COMPAT)."\">\n";
  echo "<P>Note: When you update your bio, please give us a few days for our editors to get back to you.</P>\n";
  echo "<P>Web-based Bio: ".$participant["pubsname"].htmlspecialchars($participant["editedbio"],ENT_COMPAT)."</P>\n";
  echo "<LABEL class=\"spanlabcb\" for=\"bio\">Change your web-based biography ($bio characters or fewer):</LABEL><BR>\n";
  echo "Note: Your web-based biography will appear immediately following your name on the web page.<BR>\n";
  echo "<TEXTAREA rows=\"5\" cols=\"72\" name=\"bio\">".htmlspecialchars($participant["bio"],ENT_COMPAT)."</TEXTAREA>\n<BR>\n";
  echo "<P>Program guide Bio: ".$participant["pubsname"].htmlspecialchars($participant["progeditedbio"],ENT_COMPAT)."</P>\n";
  echo "<LABEL class=\"spanlabcb\" for=\"progbio\">Change your program guide biography ($progbio characters or fewer):</LABEL><BR>\n";
  echo "Note: Your program guide biography will appear immediately following your name in the program book.<BR>\n";
  echo "<TEXTAREA rows=\"5\" cols=\"72\" name=\"progbio\">".htmlspecialchars($participant["progbio"],ENT_COMPAT)."</TEXTAREA>";
} else {
  if (strlen($participant["editedbio"])>0) {
    echo "\n<P>Web-based Bio: ".$participant["pubsname"].htmlspecialchars($participant["editedbio"],ENT_COMPAT)."</P>\n";
  }
  if (strlen($participant["progeditedbio"])>0) {
    echo "\n<P>Program guide Bio: ".$participant["pubsname"].htmlspecialchars($participant["progeditedbio"],ENT_COMPAT)."</P>\n";
  }
// Block to pass the various matching values, if there is to be no bio edits.
?>
        <INPUT type="hidden" name="pubsname" value="<?php echo htmlspecialchars($waspubname,ENT_COMPAT); ?>">
        <INPUT type="hidden" name="bio" value="<?php echo htmlspecialchars($participant['bio'],ENT_COMPAT); ?>">
        <INPUT type="hidden" name="progbio" value="<?php echo htmlspecialchars($participant['progbio'],ENT_COMPAT); ?>">
<?php
}

// Block to pass the submit button and the various old values
?>
        <DIV class="SubmitDiv"><BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON></DIV>
        <INPUT type="hidden" name="waspubsname" value="<?php echo htmlspecialchars($waspubsname,ENT_COMPAT); ?>">
        <INPUT type="hidden" name="wasbio" value="<?php echo htmlspecialchars($participant['bio'],ENT_COMPAT); ?>">
        <INPUT type="hidden" name="wasprogbio" value="<?php echo htmlspecialchars($participant['progbio'],ENT_COMPAT); ?>">
        <INPUT type="hidden" name="wasbestway" value="<?php echo $participant['bestway']; ?>">
        <INPUT type="hidden" name="wasinterested" value="<?php echo $participant['interested']; ?>">
    </form>
<?php participant_footer() ?>
