<?php
  $title="Reports for Program";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
?>
<p> </p>
<dl>
<DT> <a href="thsessionfeaturereport.php">Session Features</a></DT><DD>Which Session needs which Features?</DD>
<DT> <a href="thsessionroomsetsreport.php">Session roomsets</a></DT><DD>What roomsets are we using</DD>
<DT> <a href="thsessionservicesreport.php">Session Services</a></DT><DD>Which Session needs which Services?</DD>
<DT> <a href="thsessionservicesservicereport.php">Session Services by Service</a></DT><DD>Which Session needs which Services?</DD>
<DT> <a href="thsessiontechnotesreport.php">Session Tech and Hotel notes</a></DT><DD>What notes are in on this panel for tech and hotel?</DD>
</dl>

</dl>
<?php staff_footer(); ?>
