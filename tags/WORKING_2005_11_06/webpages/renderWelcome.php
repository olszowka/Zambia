<?php
   global $participant,$message_error,$message2,$congoinfo;
   $title="Welcome";
   require_once('ParticipantHeader.php');
   require_once('ParticipantFooter.php');
   participant_header($title);
?>

<?php if ($message_error!="") { ?>
	<P class="errmsg"><?php echo $message_error; ?></P>
	<?php } ?>
<?php if ($message!="") { ?>
	<P class="regmsg"><?php echo $message; ?></P>
	<?php } ?>
<?php
    $chpw=($participant["password"]=="4cb9c8a8048fd02294477fcb1a41191a");
    $chint=($participant["interested"]==0);
?>
<P> Welcome to the Program Participant Pages! 
<ol>
  <li> First, please indicate whether you will partipate in Arisia'06 and change your password.
<FORM class="nomargin" name="pwform" method=POST action="submitWelcome.php">
  <div id="update_section">
    <ul><li><label for="interested" class="padbot0p5">I am interested and able to participate in programming for Arisia '06&nbsp;</label>
      <?php $int=$participant['interested']; ?>
      <SELECT name=interested class="yesno">
            <OPTION value=0 <?php if ($int==0) {echo "selected";} ?> >&nbsp;</OPTION>
            <OPTION value=1 <?php if ($int==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($int==2) {echo "selected";} ?> >No</OPTION></SELECT>
<?php if ($chpw) { ?>
    <li>Your password is still set to the default value.<table><tr><td>
      Change Password</td>
      <td><INPUT type="password" size="10" name="password"></td></tr>
      <tr><td>Confirm New Password&nbsp;</td>
      <td><INPUT type="password" size="10" name="cpassword"></td></tr></table>
<?php } else { ?>
    <li> Thank you for changing your password. For future changes, use the "My Profile" tab.
<?php } ?>
    </ul>
    <DIV class="submit">
        <DIV id="submit"><BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON></DIV>
      </DIV>
    </DIV>
  </FORM>
  <li> Use the "My Profile" tab to:
    <ul>
      <li> Check your contact information. </li>
      <li> Indicate whether you will be participating in Arisia'06. </li>
      <li> Enter your biography.</li>
    </ul>
  </li>
  <li> Use the "My Availability" tab to:  
    <ul>
      <li> Tell us when you are available to be on panels.</li>
    </ul>
  </li>
  <li> Use the "Search Panels" and "My Panel Interests" tab to:  
    <ul>
      <li> Find and select the specific panels you are interested in. </li>
      <li> On the "Search Panels" tab, you may browse the list of prospective panels and check the ones you like. </li>
      <li> On the "My Panel Interests" tab, you may rank your relative interest in each panel and comments on each. </li>
    </ul>
  </li>
  <li> Use the "My Suggestions" tab to:  
    <ul>
      <li> Enter your suggestions for Arisia'06. </li>
    </ul>
  </li>
  <li> Use the "My General Interests" tab to:  
    <ul>
      <li> Describe the kinds of panels you are interested in.  </li>
      <li> Suggest the people you would like to work with.  </li>
    </ul>
  </li>
</ol>

<p>Thank you for your time, and we look forward to see you at Arisia'06. 
<p>- <a href="mailto: program@arisia.org"> Program@Arisia.org </a> </P>
<?php participant_footer() ?>
