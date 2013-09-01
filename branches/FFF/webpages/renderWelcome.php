<?php
   global $participant,$message,$message_error,$message2,$congoinfo;
   //error_log("Zambia: Reached renderWelcome.php"); 
   $title="Participant View";
   require_once('PartCommonCode.php');
   $conid=$_SESSION['conid'];
   participant_header($title);
   getCongoData($badgeid);

    if ($message_error!="") { 
        echo "<P class=\"errmsg\">$message_error</P>\n";
        }
    if ($message!="") {
        echo "<P class=\"regmsg\">$message</P>\n";
        }
    $chpw=($participant["password"]=="4cb9c8a8048fd02294477fcb1a41191a");

/* Get interested state from table.  Below the full table isn't
   generated, because we _only_ want to give them a limited set of
   responses.  Tentatively (although not coded yet) 
   "Pending" or "Not Accepted", not modifyable, "Invited", "Suggested"
   or "Yes" => "No", "No", "Not Applied", or not on the table at all
   => "Suggested", perhaps this should be coded into the table itself,
   since different cons might do things differently. */
$query = <<<EOD
SELECT
    interestedtypename
  FROM
      $ReportDB.Participants
    JOIN $ReportDB.Interested I USING (badgeid)
    JOIN $ReportDB.InterestedTypes USING (interestedtypeid)
  WHERE
    badgeid=$badgeid AND
    I.conid=$conid
EOD;

if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }

list($interested)= mysql_fetch_array($result, MYSQL_NUM);

// to make the words below make sense
if ($interested=="") {
  $interested="not having been in touch";
}

    if (may_I('postcon')) { 
      if (file_exists("../Local/Verbiage/Welcome_0")) {
	echo file_get_contents("../Local/Verbiage/Welcome_0");
      } else {
?>
<P>Thank you for your participation in the <?php echo CON_NAME; ?> event.  With your help it was a great con.  We look forward 
to your participation again next year.</P>
<P>We will post instructions for participating in brainstorming for the next event soon.</P>
<P>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--Program and Events Committees</P>
<?php
    participant_footer();
    exit();
      }
    }
    if (file_exists("../Local/Verbiage/Welcome_1")) {
      echo file_get_contents("../Local/Verbiage/Welcome_1");
    } else {
?>

<p><h3> Please check back often as more options will become available as we get closer to the convention. </h3>

<?php } ?>

<P> Dear
<?php echo $congoinfo["firstname"]; echo " "; echo $congoinfo["lastname"]; ?>,

<P> Welcome to the <?php echo CON_NAME; ?> website.</P>

<?php /*
<p> First, please take a moment to indicate your ability and interest in partipating in <?php echo CON_NAME; ?>.
      <table><tr><td>&nbsp;&nbsp;&nbsp;</td>
      <td><label for="interested" class="padbot0p5">I am interested and able to participate in <?php echo CON_NAME; ?>. &nbsp;</label>
      <SELECT name=interested class="yesno">
				   <OPTION value=0 <?php if (($interested==0) OR ($interested>2)) {echo "selected";} ?> >&nbsp;</OPTION>
            <OPTION value=1 <?php if ($interested==1) {echo "selected";} ?> >Yes</OPTION>
            <OPTION value=2 <?php if ($interested==2) {echo
            "selected";} ?> >No</OPTION></SELECT>
      </td></tr></table>
      */ ?>

<P> You are currently listed on our roles as <?php echo $interested; ?>.</P>

<P> If this does not match with your expectations please, get in touch with
your liaison person, as soon as possible.</P>

<?php if ($chpw) { ?>
<FORM class="nomargin" name="pwform" method=POST action="SubmitWelcome.php">
  <div id="update_section">
    <p>Now take a moment and personalize your password.
    <table>
      <tr>
        <td>&nbsp;&nbsp;&nbsp;</td>
        <td>Change Password</td>
        <td><INPUT type="password" size="10" name="password"></td>
      </tr>
      <tr>
        <td>&nbsp;&nbsp;&nbsp;</td><td>Confirm New Password&nbsp;</td>
        <td><INPUT type="password" size="10" name="cpassword"></td>
      </tr>
    </table>
    <DIV class="submit">
      <DIV id="submit" >
        <BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
      </DIV>
    </DIV>
  </DIV>
</FORM>
<?php } ?>
<?php
if (file_exists("../Local/Verbiage/Welcome_2")) {
  echo file_get_contents("../Local/Verbiage/Welcome_2");
} else {
?>
  <p> Use the <A HREF="my_contact.php">"My Profile"</A> tab above, at any point to:
    <ul>
      <li> Check your contact information. </li>
      <li> Change your passowrd. </li>
<?php  if (may_I('EditBio')) { ?>
      <li> Edit your name as you want to appear in our publications.</li>
      <li> Enter a short and long bio for <?php echo CON_NAME; ?> web and program book publications.</li>
<?php
  }
} ?>
    </ul>

<?php
if (may_I('my_availability')) {
  if (file_exists("../Local/Verbiage/Welcome_7")) {
    echo file_get_contents("../Local/Verbiage/Welcome_7");
  } else {
?>
  <p> Use the <A HREF="my_sched_constr.php">"My Availability"</A> tab above, at any point to:
    <ul>
      <li> Set the total number of times you would be willing to commit to, for all of <?php echo CON_NAME; ?>.</li>
      <li> Set the per day number of times you would be willing to commit to. </li>
      <li> Indicate the times you are able to commit to <?php echo CON_NAME; ?>. </li>
      <li> Indicate any conflicts or other constraints. </li>
    </ul>
<?php
  }
}
if (may_I('search_panels')) {
  if (file_exists("../Local/Verbiage/Welcome_3")) {
    echo file_get_contents("../Local/Verbiage/Welcome_3");
  } else {
?>
  <p> Use the <A HREF="my_sessions1.php">"Search Panels"</A> tab above, at any point to:
    <ul>
      <li> See suggested topics for <?php echo CON_NAME; ?> programming. </li>
      <li> Indicate panels you would like to participate on. </li>
    </ul>
<?php 
  }
}
if (may_I('my_panel_interests')) {
  if (file_exists("../Local/Verbiage/Welcome_4")) {
    echo file_get_contents("../Local/Verbiage/Welcome_4");
  } else {
?>
  <p> Use the <A HREF="PartPanelInterests.php">"My Panel Interests"</A> tab above, at any point to:
    <ul>
      <li> See what selections you have made for panels. </li>
      <li> Alter or give more information about your selections . </li>
      <li> Rank the preference of your selections . </li>
    </ul>
<?php
  }
}
if (may_I('my_schedule')) { 
    if (file_exists("../Local/Verbiage/Welcome_5")) {
      echo file_get_contents("../Local/Verbiage/Welcome_5");
    } else {
?>
  <p> Use the <A HREF="MySchedule.php">"My Schedule"</A> tab above, at any point to:
    <ul>
      <li> See what you have been scheduled to do at con. 
      <li> If there are issues, conflict or questions please email us at 
<a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a>
    </ul>
<?php
  }
}
if (may_I('my_gen_int_write')) { 
  if (file_exists("../Local/Verbiage/Welcome_6")) {
    echo file_get_contents("../Local/Verbiage/Welcome_6");
  } else {
?>
  <p> Use the <A HREF="my_interests.php">"My General Interests"</A> tab above, at any point to:
    <ul>
      <li> Describe the kinds of panels you are interested in.  </li>
      <li> Suggest the people you would like to work with.  </li>
    </ul>
<?php
  }
}
if (may_I('BrainstormSubmit')) { 
  if (file_exists("../Local/Verbiage/Welcome_8")) {
    echo file_get_contents("../Local/Verbiage/Welcome_8");
  } else {
?>
  <p> Use the <A HREF="MyProposals.php">"Submit a Proposal"</A> tab above, at any point to:
    <ul>
      <li> Enter the brainstorming view where you can submit panel, workshop and presentation ideas.
      <li> You can return back to this page by clicking on "Participant View" tab in the upper right corner. 
    </ul>
<?php
  }
} ?>
</ol>

<p>Thank you for your time, and we look forward to seeing you at <?php echo CON_NAME; ?>. 
<p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </P>
<?php participant_footer(); ?>
