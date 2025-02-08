<?php
// Copyright (c) 2011-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// This page has two completely different entry points from a user flow standpoint:
//   1) Beginning of send email flow -- start to specify parameters
//   2) After verify -- 'back' can change parameters -- 'send' fire off email sending code
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
require_once('StaffSendEmailCommonCode.php'); //actually do the email sends

/* this should come from db_name.php */
$batchSize = 100;
global $title, $message, $link;
if (!(isLoggedIn() && may_I("SendEmail"))) {
    exit(0);
}
if (isset($_POST['sendto'])) { // page has been visited before
// restore previous values to form
    $email = get_email_from_post();
} else { // page hasn't just been visited
    $email = set_email_defaults();
}
$message_warning = "";
if (empty($_POST['navigate']) || $_POST['navigate']!='send') {
    render_send_email($email,$message_warning);
    exit(0);
}
// put code to send email here.
// render_send_email_engine($email,$message_warning);
$title = "Staff Send Email";
if (SMTP_QUEUEONLY === TRUE) {
    staff_header($title, 'bs4');
}

$email = get_email_from_post();

$query = "SELECT emailtoquery FROM EmailTo where emailtoid=".$email['sendto'];
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$emailto = mysqli_fetch_array($result,MYSQLI_ASSOC);
mysqli_free_result($result);
$query = $emailto['emailtoquery'];
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$i = 0;
while ($row=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
    $recipientinfo[$i] = $row;
    $i++;
}
mysqli_free_result($result);
$recipient_count = $i;
$query = "SELECT emailfromaddress FROM EmailFrom where emailfromid = {$email['sendfrom']};";
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$emailfrom = $row['emailfromaddress'];
mysqli_free_result($result);
$email['emailfrom'] = $emailfrom;

$query="SELECT emailaddress FROM EmailCC where emailccid = {$email['sendcc']};";
$result = mysqli_query_exit_on_error($query);
if (!$result) {
    exit(-1); // Though should have exited already anyway
}
$row = mysqli_fetch_array($result, MYSQLI_ASSOC);
$emailcc = $row['emailaddress'];
$email['emailcc'] = $emailcc;
$email['batchsize'] = $batchSize;
mysqli_free_result($result);

if (SMTP_QUEUEONLY === TRUE || $recipient_count <= ($batchSize + 10)) { // leave a little slop to avoid having to split items right on the edge.
    sendEmails($email, $recipientinfo, $startIndex = 0, $recipient_count, false);
    if (SMTP_QUEUEONLY === TRUE) {
        staff_footer();
    }
} else {
    // too large to send in one batch, write a page to loop over the list inz 125 unit increments
    ?>
<script src="javascript/BulkEmailSend.js"></script>
<script type='text/javascript'>
    var email = <?php echo json_encode($email, JSON_HEX_QUOT); ?>;
    var recipient_count = <?php echo $recipient_count; ?>;
    var recipientinfo = <?php echo json_encode($recipientinfo, JSON_HEX_QUOT); ?>;
</script>
<?php
    staff_header($title, 'bs4');

?>
    <div id='resultBoxDIV' class='container-fluid'><span class='beforeResult' id='resultBoxSPAN'></span>
    </div>
    <div class="row">
        <div class="col-md-12">
            Starting bulk email send in batches of <?php echo $email['batchsize']; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="bulkSendStatusDiv"></div>
    </div>
<?php
    staff_footer();
}
?>
