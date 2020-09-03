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
participant_header($title, false, 'Consent');
if ($message_error != "") {
    echo "<P class=\"alert alert-error\">$message_error</P>\n";
}
if ($message != "") {
    echo "<P class=\"alert alert-success\">$message</P>\n";
}
$consent_message = fetchCustomText("consent");
?>
    
<div class="row-fluid">
	<div class="span12">
		<h3>Consent for collection and usage of your data entered into Zambia for <?php echo CON_NAME ?></h3>
		<p><?php echo $consent_message ?></p>
		
		<form class="form-horizontal" name="consentform" method=POST action="SubmitConsent.php">
			<fieldset>
                <div id="update_section" class="control-group">
                    <label for="consent" class="control-label">I, <?php echo $participant_array["firstname"]; echo " "; echo $participant_array["lastname"]; ?> grant consent for data collection of my personal data:</label>
                    <div class="controls">
                    <select id="consent" name="consent" class="span2">
                        <option value=0 selected="selected">No</option>
                        <option value=1>Yes</option>
                    </select>
				</div>
			  </div>
			</fieldset>
            <button class="btn btn-primary" type="submit" name="submit" >Update</button>
		</form>
	</div>
</div>
<?php participant_footer(); ?>
