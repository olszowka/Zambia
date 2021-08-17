<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message_error, $message2, $congoinfo, $title;
$title = "Staff Overview";
$bootstrap4 = true;
require_once('StaffCommonCode.php');
staff_header($title,  $bootstrap4);

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

<?php
// echo '<pre>'; print_r($_SESSION['permission_set']); echo '</pre>';  //to show the permissions of the logged in staff member for debugging purposes
// echo phpinfo();
?>

<?php staff_footer(); ?>
