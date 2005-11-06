<?php
   global $participant,$message_error,$message2,$congoinfo;
   $title="Welcome";
   require_once('db_functions.php');
   require_once('ParticipantHeader.php');
   require_once('ParticipantFooter.php');
   require_once('PartCommonCode.php');
   participant_header($title);
?>

<?php if ($message_error!="") { ?>
	<P class="errmsg"><?php echo $message_error; ?></P>
	<?php } ?>
<?php if ($message!="") { ?>
	<P class="regmsg"><?php echo $message; ?></P>
	<?php } ?>

<P> Welcome to the Program Participant Pages! 
<p>Using the tabs above, please take this opportunity to: 
<ol>
<!-- ?php if (your encrypted password is 4cb9c8a8048fd02294477fcb1a41191a -->
  <li> First, please change your password </li>
    <ul>
      <li> You'll find a box for this on "My Contact Info". </li>
    </ul>
  </li>
<!-- end of if -->
  <li> Use the "My Contact Info" tab to:
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
