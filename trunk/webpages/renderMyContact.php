<?php
require_once('PartCommonCode.php');
if (isset($bio)) {
    $participant["bio"]=$bio;
    }
if (isset($pubsname)) {
        $participant["pubsname"]=$pubsname;
        }
    else {
        $pubsnameold=$participant["pubsname"];
        if (strlen($participant["pubsname"])<1) {
             $participant["pubsname"]=$congoinfo["badgename"];
             }
        }
participant_header($title);
?>

<?php if ($message_error!="") { ?>
	<P class="errmsg"><?php echo $message_error; ?></P>
	<?php } ?>
<?php if ($message!="") { ?>
	<P class="regmsg"><?php echo $message; ?></P>
	<?php } ?>
<FORM name="partform" method=POST action="SubmitMyContact.php">
    <div id="update_section">
        <div class="divlistbox">
            <span class="spanlabcb">I am interested and able to participate in 
programming<BR>for <?php echo CON_NAME; ?>&nbsp;</span>
     <?php $int=$participant['interested']; ?>
            <span class="spanvalcb"><SELECT name="interested" class="yesno">
                <OPTION value=0 <?php if ($int==0) {echo "selected";} ?> >&nbsp;</OPTION>
                <OPTION value=1 <?php if ($int==1) {echo "selected";} ?> >Yes</OPTION>
                <OPTION value=2 <?php if ($int==2) {echo "selected";} ?> >No</OPTION></SELECT>
                </span>
            </div>
<?php
    if (ENABLE_SHARE_EMAIL_QUESTION===TRUE) {
    ?>
        <div class="divlistbox">
            <span class="spanlabcb">I give permission for <?php echo CON_NAME; ?> to share
                my<BR>email address with other participants</span>
    <?php $int=$participant['share_email']; ?>
            <span class="spanvalcb"><SELECT name="share_email" class="yesno">
                <OPTION value="null" <?php if ($int==="") {echo "selected";} ?> >&nbsp;</OPTION>
                <OPTION value="0" <?php if ($int==="0") {echo "selected";} ?> >No</OPTION>
                <OPTION value="1" <?php if ($int==="1") {echo "selected";} ?> >Yes</OPTION></SELECT>
                </span>
            </div>
<?php
    }
    else { // share_email_question not enabled
    ?>
    <INPUT type="hidden" name="share_email" value="<?php
    $int=$participant['share_email'];
    if ($int=="") $int="null";
    echo $int."\">\n";
    }
    if (ENABLE_BESTWAY_QUESTION===TRUE) {
    ?>
        <div id="bestway">
            <span class="spanlabcb">Preferred mode of contact&nbsp;</span>
            <div id="bwbuttons">
                <div id="bwemail">
                    <input name="bestway" id="bwemailRB" value="Email" type="radio"
<?php if ($participant["bestway"]=="Email") echo " checked "; ?>
    >
                    <label for="bwemailRB">Email</label>
                    </div>
                <div id="bwpmail">
                    <input name="bestway" id="bwpmailRB" value="Postal mail" type="radio"
<?php if ($participant["bestway"]=="Postal mail") echo " checked "; ?>
    >
                    <label for="bwpmailRB">Postal Mail</label>
                    </div>
                <div id="bwphone">
                    <input name="bestway" id="bwphoneRB" value="Phone" type="radio"
<?php if ($participant["bestway"]=="Phone") echo " checked "; ?>
    >
                    <label for="bwphoneRB">Phone</label>
                    </div>
                </div>
            </div>
<?php
    }
    else { // bestway_question not enabled
    ?>
    <INPUT type="hidden" name="bestway" value="<?php
    $int=$participant['bestway'];
    if ($int=="") $int="null";
    echo $int."\">\n";
    }
    ?>
        <div class="password">
            <span class="password2">Change Password&nbsp;</span>
            <span class="value"><INPUT type="password" size="10" name="password"></span>
            </div>
        <div class="password">
            <span class="password2">Confirm New Password&nbsp;</span>
            <span class="value"><INPUT type="password" size="10" name="cpassword"></span>
            </div>
        </div>
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
                <span class="label">Phone Info&nbsp;</span>
                <span class="value"><?php echo $congoinfo["phone"]; ?></span>
                </div>
            <div class="congo_data">
                <span class="label">Email Address&nbsp;</span>
                <span class="value"><?php echo $congoinfo["email"]; ?></span>
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
            <div class="congo_data">
                <span class="label">&nbsp;</span>
                <span class="value"><?php echo "{$congoinfo['postcity']}, {$congoinfo['poststate']} {$congoinfo['postzip']}"; ?></span>
                </div>
<?php if (strlen($congoinfo['postcountry'])>0) { ?>      
            <div class="congo_data">
                <span class="label">&nbsp;</span>
                <span class="value"><?php echo $congoinfo['postcountry']; ?></span>
                </div>
<?php } ?>
            </div>
        <P class="congo-note">Please confirm your contact information.  If it is 
            not correct, contact <A href="mailto:<?php echo REG_EMAIL; ?>">registration</a>
            with your current information. This data is downloaded periodically from the
            registration database, and should be correct within a week.
        </div>
<HR>
<?php if (!(may_I('EditBio'))) { // no permission to edit bio
          echo "<P class=\"errmsg\">At this time, you may not edit either your biography or your name for publication.  They have already gone to print.</P>\n";
          }
echo "<BR>\n";
echo "Your name as you wish to have it published&nbsp;&nbsp;";
echo "<INPUT type=\"text\" size=\"20\" name=\"pubsname\" ";
echo "value=\"".htmlspecialchars($participant["pubsname"],ENT_COMPAT)."\"";
if (!(may_I('EditBio'))) { // no permission to edit bio
    echo " readonly";
    }
echo "><BR><BR>\n";
$bio=MAX_BIO_LEN;
echo "<LABEL class=\"spanlabcb\" for=\"bio\">Your biography ($bio characters or fewer):</LABEL><BR>\n";
echo "Note: Your biography will appear immediately following your name in the program.<BR>\n";
echo "<TEXTAREA rows=\"5\" cols=\"72\" name=\"bio\"";
if (!(may_I('EditBio'))) { // no permission to edit bio
    echo " readonly";
    }
echo ">".htmlspecialchars($participant["bio"],ENT_COMPAT)."</TEXTAREA>"; ?>
        <DIV class="SubmitDiv"><BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON></DIV>
        <INPUT type="hidden" name="pubsnameold" value="<?php echo htmlspecialchars($pubsnameold,ENT_COMPAT); ?>">
    </form>
<?php participant_footer() ?>
