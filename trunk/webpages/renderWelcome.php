<?php
   global $participant,$message,$message_error,$message2,$congoinfo;
   //error_log("Zambia: Reached renderWelcome.php"); 
   $title="Participant View";
   require_once('data_functions.php');
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

<p><h3> Please check back often as more options will become available as we get closer to the convention. </h3>

<P><br> For our returning and first time users: 
<p> Your programming hosts this year are Jack Dietz and September Isdell.  The best way to reach us with your comments, questions, or suggestions is to email <A HREF="mailto:<?php echo PROGRAM_EMAIL."\">".PROGRAM_EMAIL ?></A>. 
<p> First, please take a moment to indicate your ability and interest in partipating in <?php echo CON_NAME; ?> programming.
<FORM class="nomargin" name="pwform" method=POST action="submitWelcome.php">
  <div id="update_section">
      <table><tr><td>&nbsp;&nbsp;&nbsp;</td>
      <td><label for="interested" class="padbot0p5">I am interested and able to participate in programming for <?php echo CON_NAME; ?> &nbsp;</label>
      <?php $int=$participant['interested']; ?>
      <SELECT name=interested class="yesno">
            <OPTION value=0 <?php if ($int==0) {echo "selected";} ?> >&nbsp;</OPTION>
            <OPTION value=1 <?php if ($int==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($int==2) {echo "selected";} ?> >No</OPTION></SELECT>
      </td></tr></table>
<?php if ($chpw) { ?>
    <p>Now take a moment and personalize your password.
      <table><tr><td>&nbsp;&nbsp;&nbsp;</td>
      <td>Change Password</td>
      <td><INPUT type="password" size="10" name="password"></td></tr>
      <tr><td>&nbsp;&nbsp;&nbsp;</td><td>Confirm New Password&nbsp;</td>
      <td><INPUT type="password" size="10" name="cpassword"></td></tr></table>
<?php } else { ?>
    <p> Thank you for changing your password. For future changes, use the "My Profile" tab.
<?php } ?>
    <DIV class="submit">
        <DIV id="submit" ><BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON></DIV>
      </DIV>
    </DIV>
  </FORM>
  <p> Use the "My Profile" tab to:
    <ul>
      <li> Check your contact information. </li>
      <li> Indicate whether you will be participating in <?php echo 
CON_NAME; ?>. </li>
      <li> Enter a short bio for Arisia publications.</li>
    </ul>
  <p> Use the "Search Panels" tab to:
    <ul>
      <li> See suggested topics for Arisia programming. </li>
      <li> Indicate panels you would like to participate on. </li>
    </ul>
  <p> Use the "My Panel Interests" tab to:
    <ul>
      <li> See what selections you have made for panels. </li>
      <li> Alter or give more information about your selections . </li>
      <li> Rank the preference of your selections . </li>
    </ul>
  <p> Use the "My Suggestions" tab to:  
    <ul>
      <li> Enter your suggestions for <?php echo CON_NAME; ?>. </li>
    </ul>
  <p> Use the "My General Interests" tab to:  
    <ul>
      <li> Describe the kinds of panels you are interested in.  </li>
      <li> Suggest the people you would like to work with.  </li>
    </ul>
</ol>

<p>Thank you for your time, and we look forward to seeing you at <?php echo CON_NAME; ?>. 
<p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </P>
<?php participant_footer(); ?>
