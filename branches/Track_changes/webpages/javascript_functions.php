<?php
//	$Header$
//	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.
function load_jquery() {
?>
<!--<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="javascript/prefixfree.min.js" type="text/javascript"></script>-->
<script src="jquery/jquery-1.7.2.min.js"></script>
<script src="jquery/jquery-ui-1.8.16.custom.min.js"></script>
<script src="javascript/bootstrap.js" type="text/javascript"></script>
<script src="javascript/main.js"></script>
<?php
}

function load_javascript($title) {
	switch ($title) {
		case "Assign Participants":
			echo "<script src=\"javascript/StaffAssignParticipants.js\"></script>\n";
			break;
		case "Participant Assignment History":
			echo "<script src=\"javascript/ParticipantAssignmentHistory.js\"></script>\n";
			break;
		default:
?>
<script src="javascript/AdminParticipants.js"></script>
<script src="javascript/editCreateSession.js"></script>
<script src="javascript/MaintainRoomSched.js"></script>
<!--<script src="javascript/mousescripts.js"></script>-->
<script src="javascript/myProfile.js"></script>
<script src="javascript/SearchMySessions1.js"></script>
<script src="javascript/staffMaintainSchedule.js"></script>
<script src="javascript/partPanelInterests.js"></script>
<?php
		}
	}
?>


