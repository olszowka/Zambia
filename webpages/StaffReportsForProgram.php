<?php
  $title="Reports for Program";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
?>
<p> People Data </p>
<DL>
<DT> <a href="congoinforeport.php">Congo Info (all info)</a></DT><DD>Shows the information retreived from Congo</DD>
<DT> <a href="participantbioreport.php">Participant Bio</a></DT><DD>Show the badgeid, name and bio for each participant that is interested in programming</DD>
<DT> <a href="schedpartavailreport.php">Participant availablity</a></DT><DD>When they said they were available.</DD>
</DL>
<p> Reports that are of interest during Brainstorming </p>
<P>
<DL>
<DT> <a href="participantinterestsreport.php">Participant Interests</a></DT><DD>What is that participant interested in? </DD>
<DT> <a href="participantnumpanelreport.php">Participant # Panel and Constraints</a></DT><DD>How many panels does each person want to be on and the other constraints they indicated</DD>
<DT> <a href="participantrolesreport.php">Participant Roles</a></DT><DD>What Roles is a participant willing to take?</DD>
<DT> <a href="participantsuggestionsreport.php">Participant Suggestions</a></DT><DD>What is did each participant suggest?</DD>
</DL>
<p> Reports that are of interest during Precis Creation </p>
<dl>
<DT> <a href="sessionnotesreport.php">Session Notes</a></DT><DD>Contains Session specific infomation including notes for programming, notes for hotel, if the session is invited guest.</DD>
<dt>the next report link</dt>
<dd>its description</dd>
</dl>
<?php staff_footer(); ?>
