<?php
$title="Send Email to Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
staff_header($title);

echo "<P>Sorry.  This is a non-functional stubb.</P>";
echo "<FORM name=\"emailform\" method=POST action=\"POSTStaffSendEmail2.php\">";
echo "<DIV><LABEL for=\"selpart\">Select Participant</LABEL>\n";
echo "<SELECT name=\"selpart\">\n";
echo "<P>&nbsp;";
staff_footer(); ?>
