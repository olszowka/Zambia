<?php
  global $participant,$message_error,$message2,$congoinfo;
  $title="Staff Manage Sessions";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  require_once('RenderSearchSession.php');
  staff_header($title);
?>

<p>
First cut at categorizing reports is underway.   The reports are starting to be grouped based on who is interested in seeing them. The last link shows all the reports. </p>
<hr>
<table>
  <COL><COL>
   <tr><td><A HREF="StaffReportsForProgram.php">Reports for Program</A>
   <tr><td><A HREF="StaffReportsForHotel.php">Reports for Hotel</A>
   <tr><td><A HREF="reportindex.php">All Reports</A>
</table>
<?php staff_footer(); ?>
