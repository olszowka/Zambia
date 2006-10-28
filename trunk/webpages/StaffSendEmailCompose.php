<?php
require_once('email_functions.php');
require_once('db_functions.php');
require_once('StaffCommonCode.php'); //reset connection to db and check if logged in
if (isset($_POST['sendto'])) { // page has been visited before
// restore previous values to form
        $email=get_email_from_post();
        }
    else { // page hasn't just been visited
        $email=set_email_defaults();
        }
render_send_email($email);
?>
