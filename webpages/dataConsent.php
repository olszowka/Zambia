<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
//
// This file is required directly from ParticipantHeader.php or StaffHeader.php if consent is required and not given
global $message, $message_error, $message2, $participant_array, $title;
$title = "Data Retention Consent";
// Now that title is set, get common text
if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
$participant_array = retrieveFullParticipant($_SESSION['badgeid']);
if ($message_error != "") {
    echo "<p class=\"alert alert-danger\">$message_error</p>\n";
}
if ($message != "") {
    echo "<p class=\"alert alert-success\">$message</p>\n";
}
?>
    
<div class="container-fluid">
	<div class="mt-2">
		<h3 class="mb-2">Consent for collection and usage of your data entered into Zambia for <?php echo CON_NAME ?></h3>
		<?php echo fetchCustomText("consent"); ?>
		
		<form class="form-inline" name="consentform" method=POST action="SubmitConsent.php">
            <div id="update_section" class="form-group pr-2">
                <label for="consent">I, <?php echo $participant_array["firstname"]." ".$participant_array["lastname"]; ?>, grant consent for data collection of my personal data:&nbsp;</label>
            </div>
            <div class="form-group pr-4">
                <select id="consent" name="consent">
                    <option value=0 selected="selected">No</option>
                    <option value=1>Yes</option>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit" name="submit" >Update</button>
            </div>
		</form>
	</div>
</div>
<?php participant_footer(); ?>
