<?php
require_once('PartCommonCode.php');
$_SESSION=array();                 // Unset session data
unset($_COOKIE[session_name()]);   // Clear cookie
session_destroy();                 // Destroy session data

$title="Logout Confirmation";
participant_header($title);

?>

<P align="center">You have logged out from Zambia</P>
<P align="center"><A HREF="login.php">Log in</A> again.</P>

<?php participant_footer() ?>
