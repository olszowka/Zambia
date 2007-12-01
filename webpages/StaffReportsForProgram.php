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
<DT> <a href="sessioneditreport.php">Session Edit History</a></DT><DD>Who last touched each session? </DD>
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
<p> Reports that are of interest during schedule creation </p>
<DT> <a href="sessioninterestcountreport.php">Session Interest Report (counts)</a></DT><DD>For each session, show number of participants who have put it on their interest list. (Excludes invited guest sessions.)</DD>
<DT> <a href="sessioninterestpartcountreport.php">Session Interest Counts by Participant</a></DT><DD>Just how many panels did each participant sign up for anyway?</DD>
<DT> <a href="sessioninterestpartreport.php">Session Interest by participant (all info)</a></DT><DD>Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report. (All data included including for invited sessions.) order by participant</DD>
<DT> <a href="sessioninterestreport.php">Session Interest Report (all info)</a></DT><DD>Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report. (All data included including for invited sessions.)</DD>
</dl>
<p> Schedules.   Take your pick.</p>
<DT> <a href="1proggridreport.php">Programming Grid</a></DT><DD>Display Programming schedule with rooms on horizontal axis and time on vertical.</DD>
<DT> <a href="allpartschedbyparttimereport.php">Full Participant Schedule by time </a></DT><DD>The schedule sorted by participant</DD>
<DT> <a href="allroomschedreport.php">Full Room Schedule by room then time</a></DT><DD>Lists all Sessions Scheduled in all Rooms.</DD>
<DT> <a href="allroomschedtimereport.php">Full Room Schedule by time then room</a></DT><DD>Lists all Sessions Scheduled in all Rooms.</DD>
<DT> <a href="allroomschedtrackreport.php">Full Room Schedule by track then time</a></DT><DD>Lists all Sessions Scheduled in all Rooms.</DD>
<DT> <a href="allroomschedtrackroomreport.php">Full Room Schedule by track then room then time</a></DT><DD>Lists all Sessions Scheduled in all Rooms.</DD>
<DT> <a href="1wholegrid4staffreport.csv">Whole Grid - Staff view - csv</a></DT><DD>Display entire schedule with rooms on horizontal axis and time on vertical. (this includes items marked "Do Not Print" in csv format</DD>
<DT> <a href="1wholegridreport.php">Whole Grid - for con</a></DT><DD>Display entire schedule with rooms on horizontal axis and time on vertical. This excludes any item marked "Do Not Print"</DD>
<DT> <a href="2finalschedbreifdiffreport.php">Schedule for Arisia 2006 </a></DT><DD>Below is the Panel, Events, Film, Anime, Video and Arisia TV schedule. </a></DD>
<DT> <a href="2finalschedbreifreport.php">Schedule for Arisia 2006 </a></DT><DD>Below is the Panel, Events, Film, Anime, Video and Arisia TV schedule. </a></DD>
<DT> <a href="2prelimschedbriefreport.php">Preliminary Schedule</a></DT><DD>Below is the preliminary panel schedule. Please keep in mine that is it still changing as we recieve feedback from our panelists. If you have any comments please contact us a <a href="mailto: program@arisia.org">program@arisia.org</a></DD>
<?php staff_footer(); ?>
