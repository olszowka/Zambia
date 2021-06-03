<?php
// Copyright (c) 2008-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message, $message_error, $message2, $title;
// $participant_array is defined by file including this.
$title = "Participant View";
require_once('PartCommonCode.php');
participant_header($title, false, 'Normal', true);
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
    
<div class="mt-2">
	<div class="alert alert-primary" role="alert">
	    Please check back often as more options will become available as we get closer to the convention.
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p>Dear <?php echo $participant_array["firstname"]; echo " "; echo $participant_array["lastname"]; ?>,</p>
                <p>Welcome to the <?php echo CON_NAME; ?> Programming website.</p>
                <p><b>First, please take a moment to indicate your ability and interest in participating in <?php echo CON_NAME; ?> programming. You will be scheduled only if you say YES.</b></p>
                <form name="pwform" method=POST action="SubmitWelcome.php">
                    <div id="update_section" class="form-group mb-2 row">
                        <div class="col-sm-4">
                            <label for="interested">Are you interested?</label>
                        </div>
                        <?php $int=$participant_array['interested']; ?>
                        <div class="col-sm-3">
                            <select id="interested" name="interested" class="form-control">
                                <option value=0 <?php if ($int==0) {echo "selected=\"selected\"";} ?> >&nbsp;</option>
                                <option value=1 <?php if ($int==1) {echo "selected=\"selected\"";} ?> >Yes</option>
                                <option value=2 <?php if ($int==2) {echo "selected=\"selected\"";} ?> >No</option>
                            </select>
                        </div>
                    </div>
                    <?php if ($participant_array['chpw']) { ?>
                    <p class="mt-3 mb-3"><b>Now take a moment and personalize your password.</b></p>
                    <div class="form-group mb-2 row">
                        <div class="col-sm-4">
                            <label for="password">New Password:</label>
                        </div>
                        <div class="col-sm-8">
                            <input id="password" class="form-control" type="password" size="10" name="password" />
                        </div>
                    </div>
                    <div class="form-group mb-2 row">
                        <div class="col-sm-4">
                            <label for="cpassword">Confirm New Password:</label>
                        </div>
                        <div class="col-sm-8">
                            <input id="cpassword" class="form-control" type="password" size="10" name="cpassword" />
                        </div>
                    </div>
                    <?php } ?>
                    <div class="row">
                        <div class="col-6 text-center">
                            <button class="btn btn-primary" type="submit" name="submit" >Update</button>
                        </div>
                    </div>
                </form>
                <?php if (!$participant_array['chpw'] && DEFAULT_USER_PASSWORD) { ?>
                    <p class="mt-3">Thank you for changing your password. For future changes, use the "My Profile" tab.</p>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <p> Use the "Profile" menu to:</p>
                <ul>
                    <li> Check your contact information. </li>
                    <li> Indicate whether you will be participating in <?php echo CON_NAME; ?>.</li>
                    <li> Opt out of sharing your email address with other program participants.</li>
                    <li> Edit your name as you want to appear in our publications.</li>
                    <li> Enter a short bio for <?php echo CON_NAME; ?> publications.</li>
                </ul>
                <?php if (may_I('my_panel_interests')) { ?>
                <p> Use the "Session Interests" menu to:</p>
                    <ul>
                        <li> See what selections you have made for sessions.</li>
                        <li> Alter or give more information about your selections.</li>
                        <li> Rank the preference of your selections.</li>
                    </ul>
                <?php } else { ?>
                <p> The "Session Interests" menu is currently unavailable. Check back later.</p>
                <?php } ?>
                <?php if (may_I('my_gen_int_write')) { ?>
                <p> Use the "General Interests" menu to:</p>
                <ul>
                    <li> Describe the kinds of sessions you are interested in.</li>
                    <li> Suggest the people you would like to work with.</li>
                </ul>
                <?php } else { ?>
                <p> Use the "General Interests" menu to:</p>
                <ul>
                    <li> See what you previously entered as your interests.</li>
                    <li> This is currently read-only as con is approaching.  If you need to make a change here, please email us:  <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a></li>
                </ul>
                <?php } ?>

                <?php if (may_I('my_schedule')) { ?>
                <p> Use the "My Schedule" menu to:</p>
                <ul>
                    <li> See what you have been scheduled to do at con.</li>
                    <li> If there are issues, conflict or questions please email us at 
                        <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a></li>
                </ul>
                <?php } else { ?>
                <p> The "My Schedule" menu is currently unavailable.  Check back later.</p>
                <?php } ?>
                
                <?php if (may_I('search_panels')) { ?>
                <p> Use the "Search Sessions" menu to:</p>
                <ul>
                    <li> See suggested topics for <?php echo CON_NAME; ?> programming. </li>
                    <li> Indicate sessions you would like to participate on. </li>
                </ul>
                <?php } else { ?>
                <p> The "Search Sessions" menu is currently unavailable.  Check back later.</p>
                <?php } ?>
                
                <?php if (may_I('BrainstormSubmit')) { ?>
                <p> Use the "Suggest a Session" menu to:</p>  
                <ul>
                    <li> Enter the brainstorming view where you can submit panel, workshop and presentation ideas.
                    <li> You can return back to this page by clicking on "Participant View" tab in the upper right corner. 
                </ul>
                <?php } else { ?>
                <p> The "Suggest a Session" menu is currently unavailable.  Brainstorming is over.  If you have an urgent request please email us at <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a></p>
                <?php } ?>

                <p>Thank you for your time, and we look forward to seeing you at <?php echo CON_NAME; ?>.</p> 
                <p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </p>
            </div>
        </div>
    </div>
</div>
<?php participant_footer(); ?>
