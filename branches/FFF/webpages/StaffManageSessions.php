<?php
require_once('StaffCommonCode.php');
require_once('RenderSearchSession.php');
global $participant,$message_error,$message2,$congoinfo;

$title="Staff - Manage Sessions";
$description="<P>On this page you will find the online tools for managing Panels, Events, Films, Anime, and Videos.  (Which is why we refer to them with the target neutral term sessions.)</P>\n";

topofpagereport($title,$description,$additionalinfo);
?>

<hr>
<DL>
   <DT><A HREF="CreateSession.php">Create a New Session</A></DT>
   <DD>Used for creating new sessions.  They are intially created in status "edit me".  Once created, a second persion edits for content (and uniqueness). This person promotes the session to status "Brainstorm".  A third set of eyes does a basic grammar and spelling edit and promotes the session to status "Vetted".   At that time it is ready for general viewing by prospective panelists.</DD>
   <DT><A HREF="EditSession.php">Edit an Existing Session</A></DT>
   <DD>Rapidly access a Session from the list of Sessions to Edit or Update.</DD>
   <DT><A HREF="genreport.php?reportname=viewsessioncountreport">View Counts of Sessions</A>(<A HREF="genreport.php?reportname=viewrollupsessioncountreport">Alternate View Counts of Sessions</A>)</DT>
   <DD>A quick report broken down by status and then by track to give an idea of where we are.</DD>
   <DT><A HREF="genreport.php?reportname=ViewAllSessions">View All Sessions:</A></DT>
   <DD>A tabular report on all sessions organized by track.  Key information on each session is visible from the top level and a link takes you down into the details for any session.</DD>
   <DT><A HREF="CommentOnSessions.php">Session Comments</A></DT>
   <DD>Add comments and feedback specifically for Sessions.</DD>
   <DT><A HREF="ViewPrecis.php?showlinks=0">Precis View</A>&nbsp;
       (<A HREF="ViewPrecis.php?showlinks=1">Precis View With Links</A>)</DT>
   <DD>Since the purpose of the Precis is to get participants to signup to be on various panels or to help with various events, this report contains sessions where that status is "brainstorm" or "vetted".  Note that sessions marked "invited guest only" are not included in the precis (regardless of status).</DD>
</DL>

<P> Session Search (shows same data as Precis View except on all sessions):</P>
<?php RenderSearchSession(); ?>
<?php correct_footer(); ?>
