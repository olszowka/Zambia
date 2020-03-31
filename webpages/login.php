<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('CommonCode.php');
require_once('ParticipantHeader.php');
require_once('ParticipantFooter.php');

$title="Login";

participant_header($title, false, 'Login');

if (SHOW_BRAINSTORM_LOGIN_HINT === TRUE) {
?>

	<p id="brainstorm-login-hint"><span style="font-weight:bold">Brainstorm</span> users: if you want to submit ideas for panels, please enter "brainstorm" for your Badge ID
	and use the last name of the author of the Foundation series as your password (in all lowercase). </p>
<?php
    }
participant_footer(); ?>
