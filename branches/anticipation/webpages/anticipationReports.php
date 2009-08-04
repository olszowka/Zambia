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
<DT> <a href="scheduledsessiondetailreport.php">Scheduled Session Details</a></DT>
  <DD>Display scheduled sessions and select characteristics.</DD>
<DT> <a href="unscheduledsessiondetailreport.php">Unscheduled Session Details</a></DT>
  <DD>Display unscheduled sessions and select characteristics.</DD>
<DT> <a href="techandfacilities.php">Tech and Facilities Info</a></DT>
  <DD>Display all sessions with info regarding tech and facilities.</DD>
<DT> <a href="statrepconflict2few.php">Conflict Report -- Too Few Participants</A></DT>
  <DD>Display all scheduled sessions with fewer than 4 participants except those
    of type performance, reading, autographing, or Kaffeeklatsch.</DD>
<DT> <a href="staffallgridstaticreportpubsnopublic.php">Programme Grid - public</A></DT>
  <DD>Grid of all sessions.</DD>
<DT> <a href="staffallgridstaticreportpubsno.php">Programme Grid - not public</A></DT>
  <DD>Grid of all sessions (including DO-NOT-PUB and STAFF-ONLY).</DD>
</DL>
<?php staff_footer(); ?>
