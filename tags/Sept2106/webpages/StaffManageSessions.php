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
On this page you will find the online tools for managing Panels, Events, Films, Anime, and Videos.  (which is why we refer to them with the target neutral term sessions. </p>
<hr>
<table>
  <COL><COL>
   <tr><td><A HREF="CreateSession.php">Create a New Session</A>
   <tr><td>Used for creating new sessions.  They are intially created in status "edit me".  Once created, a second persion edits for content (and uniqueness). This person promotes the session to status "Brainstorm".  A third set of eyes does a basic grammar and spelling edit and promotes the session to status "Vetted".   At that time it is ready for general viewing by prospective panelists. 
   <tr><td><A HREF="ViewSessionCountReport.php">View Counts of Sessions</A> 
   <tr><td>A quick report broken down by status and then by track to give an idea of where we are. 
   <tr><td><A HREF="ViewAllSessions.php">View All Sessions</A>
   <tr><td>A tabular report on all sessions organized by track.  Key information on each session is visible from the top level and a link takes you down into the details for any session. 
   <tr><td><A HREF="ViewPrecis06.php">Precis View</A>
   <tr><td>Since the purpose of the Precis is to get participants to signup to be on various panels or to help with various events, this report contains sessions where that status is "brainstorm" or "vetted".  Note that sessions marked "invited guest only" are not included in the precis (regardless of status).
</table>

<p> Session Search (shows same data as Precis View except on all sessions):</p>
<?php RenderSearchSession(); ?>
<?php staff_footer(); ?>
