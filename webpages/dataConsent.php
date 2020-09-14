<?php
// Copyright (c) 2008-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $message, $message_error, $message2, $title;
// $participant_array is defined by file including this.
//error_log("Zambia: Reached renderWelcome.php"); 
$title = "Data Retention Consent";
// Now that title is set, get common text
if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
require_once('PartCommonCode.php');
participant_header($title, false, 'Consent', true);
if ($message_error != "") {
    echo "<P class=\"alert alert-danger\">$message_error</P>\n";
}
if ($message != "") {
    echo "<P class=\"alert alert-success\">$message</P>\n";
}
$consent_message = fetchCustomText("consent");
?>
    
<div class="container-fluid">
	<div class="text-left">
		<h3>Consent for collection and usage of your data entered into Zambia for <?php echo CON_NAME ?></h3>
		<p><?php echo $consent_message ?></p>
		
		<form class="form-inline" name="consentform" method=POST action="SubmitConsent.php">
                <div id="update_section" class="form-group">
                    <label for="consent">I, <?php echo $participant_array["firstname"]; echo " "; echo $participant_array["lastname"]; ?> grant consent for data collection of my personal data:&nbsp;</label>
                </div>
                <div class="form-group">
                    <select id="consent" name="consent">
                        <option value=0 selected="selected">No</option>
                        <option value=1>Yes</option>
                    </select>
				</div>
                <div class="form-group">
                    &nbsp;&nbsp;
                </div>
                <div class="from-group">
                    <button class="btn btn-primary" type="submit" name="submit" >Update</button>
                </div>
            </div>
		</form>
	</div>
</div>
<?php participant_footer(); ?>
