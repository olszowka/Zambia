<?php
  $title="Anticipation-specific Reports";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
  $_SESSION['return_to_page']="REPORT_LINK";
?>
<DL>

<DT> <a href="participantassignmentreport.php">Participant Assignments</a></DT>
  <DD>Display quick list of all Participants and what their assignments are.</DD>

</DL>
<?php staff_footer(); ?>
