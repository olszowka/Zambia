<?php
  $title="Staff Manage Participants";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
?>

<p>
On this page you will find the online tools for managing Participants.</p>
<hr>
<DL>
   <DT><A HREF="InviteParticipants.php">Invite a participant to a session</A></DT>
   <DD>Use this tool to put sessions marked "invited guests only" on a participant's interest list.</DD>
   <DT><A HREF="AssignParticipants.php">Assign participants to a session</A></DT>
   <DD>Use this tool to assign participants to a session and select moderator.</DD>
   <DT><A HREF="MaintainRoomSched.php">Maintain room schedule</A></DT>
   <DD>Assign sessions at particular times in a room.</DD>
</DL>

<?php staff_footer(); ?>
