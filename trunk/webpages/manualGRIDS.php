<?php
  $title="GRIDS Reports";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
  $_SESSION['return_to_page']="REPORT_LINK";
?>
<DL>
<DT> <a href="eventgridstaticreport.php">Published Event Grid</a></DT>
  <DD>Display published event schedule with rooms on horizontal axis and
  time on vertical. This excludes any item marked "Do Not Print" or
  "Staff Only".</DD>

<DT> <a href="eventgridfullstaticreport.php">Unabridged Event Grid</a></DT>
  <DD>Display event schedule with rooms on horizontal axis and time on
  vertical. This includes all items regardless of publication
  status.</DD>

<DT> <a href="programgridstaticreport.php">Published Programming Grid</a></DT>
  <DD>Display published schedule of programming rooms with rooms on
  horizontal axis and time on vertical. This excludes any item marked
  "Do Not Print" or "Staff Only".</DD>

<DT> <a href="fasttrackgridstaticreport.php">Published Fast Track Grid</a></DT>
  <DD>Display published fast track schedule with rooms on horizontal
  axis and time on vertical. This excludes any item marked "Do Not
  Print" or "Staff Only".</DD>

<DT> <a href="staffpubgridstaticreport.php">Published Grid</a></DT>
  <DD>Display published schedule with rooms on horizontal axis and
  time on vertical. This excludes any item marked "Do Not Print" or
  "Staff Only".</DD>

<DT> <a href="staffallgridstaticreport.php">Everything Grid</a></DT>
  <DD>Display the entire schedule with rooms on horizontal axis and
  time on vertical. This includes all items regardless of publication
  status.</DD>


</DL>
<?php staff_footer(); ?>
