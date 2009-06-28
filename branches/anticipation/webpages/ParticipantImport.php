<?php
  $title="Staff - Import Participant(s)";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title, 'javascript_for_import_participant');
?>

<p>
On this page you will find the online tools for managing Participants. This is a list of participants from the PFA data that
do not appear to match any Participant's within Zambia.</p>
<p>NOTE: Please check using the <a href='/ViewParticipantDetails.php' target='new'>View Participants</a> page 
to avoid duplicates, as it is possible that the name or email may have been enetered differently on two occasions</p>
<hr>
<?php if(may_I('create_participant')) { ?>
<span id="rsperror" style="color:red"></span>
<table id="list" class="scroll"></table> 
<div id="pager" class="scroll" style="text-align:center;"></div> 
<?php } ?>

<?php staff_footer(); ?>