<?php
require_once ('Local/db_name.php');
$ConName=CON_NAME; // make it a variable so it can be substituted
$ProgramEmail=PROGRAM_EMAIL; // make it a variable so it can be substituted
$ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
$HeaderTemplateFile="Local/HeaderTemplate.html";
$FooterTemplateFile="Local/FooterTemplate.html";

$constart=strtotime($ConStartDatim);
$nowis=time();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=latin-1\">
<?php
echo "  <title>Zambia -- $ConName</title>\n";
if (file_exists($HeaderTemplateFile)) {
  readfile($HeaderTemplateFile);
 } else {
?>
  <link rel="stylesheet" href="Common.css" type="text/css">
</head>
<body>
<?php } 
echo "<H1>$ConName Programming</H1>\n<hr>\n";
if ($nowis < $constart) { 
  echo "<H2>Check out the below links to learn about the great programming we will have at $ConName!</H2>\n";
 } else { 
echo "<H2>Check out the below links to give us/see your feedback and learn about the great programming we had at $ConName!</H2>\n";
 }
?>
<UL>
  <LI><A HREF="webpages/Postgrid.php">Schedule Grid</A></LI>
  <LI><A HREF="webpages/Descriptions.php">Class Descriptions</A></LI>
  <LI><A HREF="webpages/Schedule.php">Schedule</A></LI>
  <LI><A HREF="webpages/Tracks.php">Tracks</A></LI>
  <LI><A HREF="webpages/Bios.php">Presenter Bios</A></LI>
  <LI><A HREF="webpages/Vendors.php">Vendor List</A></LI>
  <LI><A HREF="webpages/Postgrid.php?volunteer=y">Volunteer Grid</A></LI>
  <LI><A HREF="webpages/Descriptions.php?volunteer=y">Volunteer Job Descriptions</A></LI>
<?php if ($nowis < $constart) { ?>
  <LI><A HREF="webpages/">Presenter Login</A></LI>
  <LI><FORM name="submitform" method="POST" action="webpages/doLogin.php">
  <INPUT type="hidden" name="badgeid" value="100">
  <INPUT type="hidden" name="passwd" value="submit">
  <INPUT type="submit" name="submit" value="Suggest a Session/Presenter">
  </FORM>
<?php } else { ?>
  <LI><A HREF="webpages/Feedback.php">Feedback</A></LI>
  <LI><A HREF="webpages/">Presenter Login</A></LI>
<?php } ?>
</UL>
<?php
if (file_exists($FooterTemplateFile)) {
  readfile($FooterTemplateFile);
 } else {
  echo "<hr>\n<P>If you have questions or wish to communicate an idea, please contact ";
  echo "<A HREF=\"mailto:$ProgramEmail\">$ProgramEmail</A>.\n</P>";
 }
include ('webpages/google_analytics.php');
?>
</body>
</html>
