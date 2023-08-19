<?php
// Copyright (c) 2008-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message, $message_error, $message2, $title;
// $participant_array is defined by file including this.
$title = "Participant View";
require_once('PartCommonCode.php');
populateCustomTextArray(); // title changed above, reload custom text with the proper page title
participant_header($title, false, 'Normal', true);
?>
<div id="confNotAttModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm No Longer Attending</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>You are currently scheduled to participate in sessions at <?php echo CON_NAME; ?> but
                    are now changing your status to not attending.  Please confirm.</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelNotAtt" class="btn btn-primary" data-dismiss="modal">Cancel</button>
                <button type="button" id="confNotAtt" class="btn btn-secondary">Confirm Not Attending</button>
            </div>
        </div>
    </div>
</div>
<?php
if ($message_error != "") {
    echo "<P class=\"alert alert-error\">$message_error</P>\n";
}
if ($message != "") {
    echo "<P class=\"alert alert-success\">$message</P>\n";
}
$chint = ($participant_array["interested"] == 0);
if (may_I('postcon')) { ?>
    <p>Thank you for your participation in <?php echo CON_NAME; ?>. With your help it was a great con. We look forward
        to your participation again next year.</p>
    <p>We will post instructions for participating in brainstorming for next year soon.</p>
    <p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--<?php echo CON_NAME; ?> Program and Events Committees</p>
    <?php
    participant_footer();
    exit();
}
    ?>

<div class="welcome-page-wrapper" style="margin: 0 auto; width:100rem;">
<form id="pwform" name="pwform" method=POST action="SubmitWelcome.php">
<div class="row mt-4">
	<div class="col col-sm-12">
		<p>Dear <?php echo $participant_array["firstname"]; echo " "; echo $participant_array["lastname"]; ?>,</p>
		<p>Welcome to Boskone 60’s program application. To be considered for programming, you must answer “yes” to the below question and then complete the questions on the survey tab of this application.</p>
    </div>
</div>
<div class="row mt-2">
    <div class="col col-sm-1"></div>
    <div class="col col-sm-9">
        <div id="update_section">
            <label for="interested" style="margin-right: 5px;"><strong>Would you like to be considered for programming?</strong></label>
            <?php $int=$participant_array['interested']; ?>
            <select id="interested" name="interested" data-schedule-count="<?php echo $participant_array['scheduleCount'];?>">
                <option value=0 <?php if ($int==0) {echo "selected=\"selected\"";} ?> >&nbsp;</option>
                <option value=1 <?php if ($int==1) {echo "selected=\"selected\"";} ?> >Yes</option>
                <option value=2 <?php if ($int==2) {echo "selected=\"selected\"";} ?> >No</option>
            </select>
		</div>
	</div>
</div>
<div class="row mt-2">
    <div class="col col-sm-12">
        <p>
            Surveys are sent to every person who requests one as well as to people who we think would be interested in being on programming this year.
            Completing a survey signals your interest in being on programming at Boskone, but it does not guarantee placement. We will issue as many
            invitations to participate as the program can accommodate, but we may not be able to include everyone. To be considered, we must receive
            your survey by November 15.
        </p>
        <p>
            After you answer the question above and save your response, you will be able to see the tabs for filling out your survey and profile as
            well as the current set of sessions that you can volunteer to have on your schedule. New sessions will be added on a regular basis, and
            you are welcome to return at any time to look for additional items that you may like to volunteer for on the program.
        </p>
    </div>
</div>

    <!--
<div class="row mt-2">
	<div class="col col-sm-12">
        <?php // if ($participant_array['chpw']) { ?>
        <h4>Now take a moment and personalize your password.</h4>
    </div>
</div>
 <div class="row mt-2">
    <div class="col col-sm-1"></div>
    <div class="col col-sm-9">
        <fieldset>
            <label for="password" style="margin-right: 5px;">New Password:</label>
            <input id="password" type="password" size="10" name="password" />
            <label for="cpassword" style="margin-left: 20px; margin-right:5px;" >Confirm New Password:</label>
            <input id="cpassword" type="password" size="10" name="cpassword" />
		</fieldset>
        <?php //} ?>
     </div>
</div>
-->
 <div class="row mt-2">
    <div class="col col-sm-12">
        <button class="btn btn-primary" type="submit" name="submitBtn" >Save Response</button>
	</div>
</div>
</form>
<div class="row mt-4">
	<div class="col col-sm-12">
		<h4>Timeline for New Programming Process:</h4>
        <ul>
            <li> Oct 1-Nov 15: Surveys sent to potential program participants.</li>
            <li> Oct 1-Nov 15: Potential participants fill in surveys, suggest program ideas, and volunteer for sessions.</li>
            <li> Oct 15-Dec 15: Official Program Participant invitations sent, confirming program
                participation, on a rolling basis.
            </li>
            <li> Nov 15: Survey Due Date.</li>
            <li> Dec 1: Final cutoff date for all surveys, suggested items, and volunteering for sessions.</li>
            <li> Dec 15: Draft schedules sent to confirmed program participants.</li>
            <li> Dec 15-Jan 6: Confirmed participants approve schedules and have a chance to request additional sessions, if openings are available.</li>
            <li> Jan 8: Final program schedules sent to confirmed participants.</li>
            <li> Jan 13: Publication of Boskone 60 Program Schedule</li>
        </ul>
    </div>
</div>
<div class="row mt-4">
    <div class="col col-sm-12">
        <h4>Use the "Profile" menu to:</h4>
        <ul>
            <li> Update your contact information.</li>
            <li> Change your password.</li>
            <li> Add your Bio.</li>
        </ul>

        <h4 class="mt-2">Use the “Survey” menu to:</h4>
        <ul>
            <li> Share your programming interests.</li>
        </ul>

        <h4 class="mt-2">Use the "Availability" menu to:</h4>
        <ul>
            <li> List the general times that you are available.</li>
        </ul>

        <h4 class="mt-2">Use the "Search Sessions" menu (when available) to:</h4>
        <ul>
            <li> Volunteer for sessions that interest you.</li>
            <li> <em>Note:</em> As the program is developed, we may also add you to items that we think would be a good fit for you based upon your survey responses.</li>
        </ul>

	</div>
</div>
</div>
<?php participant_footer(); ?>
