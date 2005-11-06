<?php
require_once('db_functions.php');
require_once('ParticipantHeader.php');
require_once('ParticipantFooter.php');
$firsttime=false;
if (isLoggedIn($firsttime)===false) {
	exit(0);
	}
if (isset($bio)) {
    $participant["bio"]=$bio;
    }
participant_header($title);
?>

<?php if ($message_error!="") { ?>
	<P class="errmsg"><?php echo $message_error; ?></P>
	<?php } ?>
<?php if ($message!="") { ?>
	<P class="regmsg"><?php echo $message; ?></P>
	<?php } ?>
<H4>The following information is copied from the Registration system.</H4>
<div id="congo_section">
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
    <span class="label">Phone Info&nbsp;</span>
    <span class="value"><?php echo $congoinfo["phone"]; ?></span>
    </div>
  <div class="congo_data">
    <span class="label">Email Address&nbsp;</span>
    <span class="value"><?php echo $congoinfo["email"]; ?></span>
    </div>
  <div class="congo_data">
    <span class="label">Postal Address&nbsp;</span>
    <span class="value"><?php echo $congoinfo["postaddress"]; ?></span>
    </div>
  </div>

<P class="note">The above data is from the Registration database and cannot be modified here.  Please email
<A href="mailto:reg@arisia.org">Registration</A> to update this data.</P>

<HR>
  <FORM name="partform" method=POST action="submitMyContact.php">
  <div id="update_section">
    <div class="divcheckbox">
      <span class="spanlabcb">I am interested and able to participate in programming for Arisia '06&nbsp;</span>
      <?php $int=$participant['interested']; ?>
      <span class="spanvalcb"><SELECT name=interested >
            <OPTION value=0 <?php if ($int==0) {echo "selected";} ?> >&nbsp</OPTION>
            <OPTION value=1 <?php if ($int==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($int==2) {echo "selected";} ?> >No</OPTION></SELECT>
          </span>
      </div>
    <div id="bestway">
      <span class="radiohead">Best way to reach me&nbsp;</span>
      <div id="bwbuttons">
        <div id="bwemail">
          <input name="bestway" id="bwemailRB" value="Email" type="radio"
<?php
    if ($participant["bestway"]=="Email") {echo " checked ";}
?>
              >
          <label for="bwemailRB">Email</label>
          </div>
        <div id="bwpmail">
          <input name="bestway" id="bwpmailRB" value="Postal mail" type="radio"
<?php
    if ($participant["bestway"]=="Postal mail") {echo " checked ";}
?>
              >
          <label for="bwpmailRB">Postal Mail</label>
          </div>
        <div id="bwphone">
          <input name="bestway" id="bwphoneRB" value="Phone" type="radio"
<?php
    if ($participant["bestway"]=="Phone") {echo " checked ";}
?>
              >
          <label for="bwphoneRB">Phone</label>
          </div>
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
        <DIV class="label"><LABEL class="label" for="bio">Please enter a biography of fewer than 500 characters.</LABEL></DIV>
            <DIV><TEXTAREA rows=5 cols=72 name="bio"><?php echo htmlspecialchars($participant["bio"],ENT_COMPAT); ?></TEXTAREA></DIV>
            </DIV>
    <DIV class="submit">
        <DIV id="submit"><BUTTON type="submit" name="submit" >Update</BUTTON></DIV>
        </DIV>
    </form>
<?php participant_footer() ?>
