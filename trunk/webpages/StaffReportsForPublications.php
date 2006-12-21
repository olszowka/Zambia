<?php
  $title="Reports for Program";
  require_once('db_functions.php');
  require_once('StaffHeader.php');
  require_once('StaffFooter.php');
  require_once('StaffCommonCode.php');
  staff_header($title);
?>
<p> Web Publication </p>
<dl>
<DT> <a href="2prelimschedbriefreport.php">Preliminary Schedule</a></DT><DD>Below is the preliminary panel schedule. Please keep in mine that is it still changing as we recieve feedback from our panelists. If you have any comments please contact us a <a href="mailto: program@arisia.org">program@arisia.org</a></DD>
<DT> <a href="2finalschedbreifreport.php">Schedule for Arisia 2006 </a></DT><DD>Below is the Panel, Events, Film, Anime, Video and Arisia TV schedule. </a></DD>
</dl>
<p> Pocket Program</p>
<DL>
<DT> <a href="4pubsreport.php">Report for Pubs</a></DT><DD>Based off of Toppers spreadsheet</DD>
<DT> <a href="4pubsreport.csv">Report for Pubs - csv</a></DT><DD>Based off of Toppers spreadsheet in csv format</DD>
<DT> <a href="4pubsschedreport.php">Schedule report for Pubs</a></DT><DD>Lists all Sessions Scheduled in all Rooms.</DD>
<DT> <a href="4pubsschedreport.csv">Schedule report for Pubs - csv</a></DT><DD>Lists all Sessions Scheduled in all Rooms. in csv format</DD>
</DL>
<p> Grid </p>
<dl>
<DT> <a href="1wholegridreport.php">Whole Grid - for con</a></DT><DD>Display entire schedule with rooms on horizontal axis and time on vertical. This excludes any item marked "Do Not Print"</DD>
</dl>
<p> Pocket Program Merge </p>
<dl>
<DT> <a href="4progpacketmergereport.php">Full Participant Schedule for the Program Packet Merge</a></DT><DD>lastname, firstname, (day, time, duration, room, mod)</DD>
<DT> <a href="4progpacketmergereport.csv">Full Participant Schedule for the Program Packet Merge - csv</a></DT><DD>lastname, firstname, (day, time, duration, room, mod) in csv format</DD>
<DT> <a href="4progpanelmergereport.php">Full Participant Schedule for the Program Packet Merge</a></DT><DD>sessionid, room, starttime, duration, (badgeid, lastname, firstname, mod)</DD>
<DT> <a href="4progpanelmergereport.csv">Full Participant Schedule for the Program Packet Merge - csv</a></DT><DD>sessionid, room, starttime, duration, (badgeid, lastname, firstname, mod) in csv format</DD>
</dl>
<p> Palm Program</p>
<dl>
<DT> <a href="4benreport.php">Report for Ben and the palm</a></DT><DD>StartTime Duration Room Track Title Participants</DD>
<DT> <a href="4benreport.csv">Report for Ben and the palm - csv</a></DT><DD>StartTime Duration Room Track Title Participants in csv format</DD>

</dl>
<?php staff_footer(); ?>
