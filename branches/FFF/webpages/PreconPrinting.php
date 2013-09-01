<?php
$title="Staff - Useful Printing Links";
require_once('StaffCommonCode.php');
staff_header($title);
?>

<P>On this page you will find the tools for printing what is needful before the event.</P>
<HR>
<DL>
  <DT id="schedules"><A HREF="SchedulePrint.php?group=Participant">Presenters</A> <A HREF="SchedulePrint.php?group=Programming">Programming Volunteers</A> <A HREF="SchedulePrint.php?group=General">General Volunteers</A></DT>
  <DD id="schedules">Preview and then print a schedule for each group.</DD>
  <DT id="classintro"><A HREF="ClassIntroPrint.php">Introduction pages</A></DT>
  <DD id="classintro">Preview and then print the Introduction pages, including the roles info for everyone applicable.</DD>
  <DT id="logistics"><A HREF="LogisticsPrint.php">Room States</A></DT>
  <DD id="logistics">Preview and then print the Logistics pages for con logistics management.</DD>
  <DT id="gridwide"><A HREF="Postgrid-wide.php?print_p=y">Times x Rooms</A><DT>
  <DD id="gridwide">Print the public grid with the Times across the top and the Rooms down the left side.</DD>
  <DT id="gridtall"><A HREF="Postgrid.php?print_p=y">Rooms x Times</A><DT>
  <DD id="gridtall">Print the public grid with the Rooms across the top and the Times down the left side.</DD>
  <DT id="badges"><A HREF="BadgesPrint.php">Badges</A></DT>
  <DD id="badges">Print up simple paper badges.</DD>
  <DT id="badgebacks"><A HREF="BadgeBackPrint.php">Badge Backs</A></DT>
  <DD id="badges">Print up the sechedule for the back of the simple paper badges.</DD>
  <DT id="tents"><A HREF="TentsPrint.php">Tents</A></DT>
  <DD id="tents">Print the name-tents.</DD>
  <DT id="lables"><A HREF="LabelsPrint.php">Labels</A></DT>
  <DD id="labels">Print up sticky-lables to go on a folder or envelope for everyone.</DD>
  <DT id="letters"><A HREF="WelcomeLettersPrint.php">Welcome Letters</A></DT>
  <DD id="letters">Preview and then print the Welcome Letters for Presenters, Volunteers, and folks who are doing both.</DD>
  <DT id="feedback"><A HREF="StaffFeedback.php">Feedback forms</A></DT>
  <DD id="feedback">Feedback forms for the various days or types to be printed, probably on different colour paper, for easier sorting.</DD>
</DL>
<HR>
<P>And one that is useful after the event feedback is done.</P>
<DL>
  <DT id="returnedfeedback"><A HREF="FeedbackPrint.php">Returned Feedback</A></DT>
  <DD id="returnedfeedback">All the feedback on all the schedule elements that we have.</DD>
</DL>
<?php correct_footer(); ?>
