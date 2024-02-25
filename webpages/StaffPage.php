<?php
// Copyright (c) 2011-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "Staff Overview";
require_once('StaffCommonCode.php');
staff_header($title, 'bs4');

if (!populateCustomTextArray()) {
    $message_error = "Failed to retrieve custom text. " . $message_error;
    RenderError($message_error);
    exit();
}
echo "<br/>";
echo fetchCustomText("staff_overview");
?>
<p>
Number of con days: <?php echo CON_NUM_DAYS; ?><br />
Con name: <?php echo CON_NAME; ?><br />

<?php staff_footer(); ?>
