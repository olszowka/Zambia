<?php
  $title="REPORT_TITLE";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
  $_SESSION['return_to_page']="REPORT_LINK";
?>
<div class="reportPageContent">
<div style="text-align:center">Generated: REPORT_DATE</div>
<div>REPORT_DESCRIPTION</div>
