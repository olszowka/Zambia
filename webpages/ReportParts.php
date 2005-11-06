<?php
  global $participant,$message_error,$message2,$congoinfo;
  $title="Select Participant Activity Report";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
?>

<H1 align=center>Select Participant Activity Report</H1>
    <FORM name="addform" method=POST action="SubmitMyInterests.php">

<?php staff_footer(); ?>
