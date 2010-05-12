<?php
  require_once('StaffCommonCode.php');
  $title="Grids";
  staff_header($title);
  $_SESSION['return_to_page']="genindex.php";
?>
<p align=center> Generated: Tue Feb 16 17:07:42 EST 2010
<P>For report changes email zambia@arisia.org.
<DL>
<DT> <a href="StaffBios.php">Bios for Presenters</a> (<a href="Bios.php">public version</a>)</DT><DD>For the public, with links between the bios, their classes, the schedule and a link to the grid.</DD>
<DT> <a href="StaffDescriptions.php">Descriptions Page</a> (<a href="Descriptions.php">public version</a>)</DT><DD>For the public, with links between the descriptons, the bios of the presenters, the schedule, and a link to the grid.</DD>
<DT> <a href="StaffSchedule.php">Schedule Listings</a> (<a href="Schedule.php">public version</a>)</DT><DD>For the public, with links between the schedule elements to their descriptions, the bios of the presenters, and a link to the grid.</DD>
<DT> <a href="StaffPostgrid.php">Postable Grid</a> (<a href="Postgrid.php">public version</a>)</DT><DD>For the public, with links between the classes, the schedule, their descriptions and the bios of the presenters.</DD>
<DT> <a href="StaffPostvolgrid.php">Postable Volunteer Grid</a></DT><DD>For the volunteer coordinators, with links between the classes, the schedule, and their descriptions. Volunteer and Announcer listed.</DD>
<DT> <a href="completepubgridcolor.php">Published Color Grid</a></DT><DD>Display published schedule with rooms on horizontal axis and time on vertical, keyed by color. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="completepubgrid.php">Published Grid</a></DT><DD>Display published schedule with rooms on horizontal axis and time on vertical. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="completepubgridtimecolor.php">Published Time Filled Color Grid</a></DT><DD>Display published schedule with rooms on horizontal axis and regular time on vertical, keyed by color. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="completepubgridtime.php">Published Time Filled Grid</a></DT><DD>Display published schedule with rooms on horizontal axis and regular time on vertical. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="completepubgridtimeunfilledcolor.php">Published Time Semi-filled Color Grid</a></DT><DD>Display published schedule with rooms on horizontal axis and regular time on vertical, keyed by color (only). This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridcolor.php">Published Color Event Grid</a></DT><DD>Display published event schedule with rooms on horizontal axis and time on vertical, keyed by color. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridfullcolor.php">Unabridged Color Event Grid</a></DT><DD>Display entire event schedule with rooms on horizontal axis and time on vertical, keyed by color. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridfull.php">Unabridged Event Grid</a></DT><DD>Display entire event schedule with rooms on horizontal axis and time on vertical. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridfulltimecolor.php">Unabridged Time Filled Color Event Grid</a></DT><DD>Display entire event schedule with rooms on horizontal axis and regular time on vertical, keyed by color. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridfulltime.php">Unabridged Time Filled Event Grid</a></DT><DD>Display entire event schedule with rooms on horizontal axis and regular time on vertical. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridfulltimeunfilledcolor.php">Unabridged Time Semi-filled Color Event Grid</a></DT><DD>Display entire event schedule with rooms on horizontal axis and regular time on vertical, keyed by color (only). This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgrid.php">Published Event Grid</a></DT><DD>Display published event schedule with rooms on horizontal axis and time on vertical. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridtimecolor.php">Published Time Filled Color Event Grid</a></DT><DD>Display published event schedule with rooms on horizontal axis and regular time on vertical, keyed by color. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridtime.php">Published Time Filled Event Grid</a></DT><DD>Display published event schedule with rooms on horizontal axis and regular time on vertical. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="eventgridtimeunfilledcolor.php">Published Time Semi-filled Color Event Grid</a></DT><DD>Display published event schedule with rooms on horizontal axis and regular time on vertical, keyed by color (only). This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="everythinggridcolor.php">Unabridged Color Complete Grid</a></DT><DD>Display complete schedule of all rooms with rooms on horizontal axis and time on vertical, keyed by color. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="everythinggrid.php">Unabridged Complete Grid</a></DT><DD>Display complete schedule of all rooms with rooms on horizontal axis and time on vertical. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="everythinggridtimecolor.php">Unabridged Time Filled Color Complete Grid</a></DT><DD>Display complete schedule of all rooms with rooms on horizontal axis and regular time on vertical, keyed by color. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="everythinggridtime.php">Unabridged Time Filled Complete Grid</a></DT><DD>Display complete schedule of all rooms with rooms on horizontal axis and regular time on vertical. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="everythinggridtimeunfilledcolor.php">Unabridged Time Semi-filled Color Complete Grid</a></DT><DD>Display complete schedule of all rooms with rooms on horizontal axis and regular time on vertical, keyed by color (only). This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="gohgrid.php">GOH Grid</a></DT><DD>Display unabridged schedule of all events with any GOHs participanting with rooms on horizontal axis and time on vertical. This includes all items marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="programgridcolor.php">Published Color Programming Grid</a></DT><DD>Display published schedule of programming rooms with rooms on horizontal axis and time on vertical, keyed by color. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="programgrid.php">Published Programming Grid</a></DT><DD>Display published schedule of programming rooms with rooms on horizontal axis and time on vertical. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="programgridtimecolor.php">Published Time Filled Color Programming Grid</a></DT><DD>Display published schedule of programming rooms with rooms on horizontal axis and regular time on vertical, keyed by color. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="programgridtime.php">Published Time Filled Programming Grid</a></DT><DD>Display published schedule of programming rooms with rooms on horizontal axis and regular time on vertical. This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="programgridtimeunfilledcolor.php">Published Time Semi-filled Color Programming Grid</a></DT><DD>Display published schedule of programming rooms with rooms on horizontal axis and regular time on vertical, keyed by color (only). This excludes any item marked "Do Not Print" or "Staff Only".</DD>
<DT> <a href="staffonlygrid.php">Staff Only Grid</a></DT><DD>Display only the items that are "Staff-Only" or "Do-Not-Publish" with rooms on horizontal axis and time on vertical.</DD>
</DL>
<?php staff_footer(); ?>
