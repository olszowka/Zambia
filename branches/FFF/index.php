<?php
require_once ('Local/db_name.php'); // This should be generalized so everything can be one directory
$ConKey=CON_KEY; // should be passed in, rather than hard set
$ProgramEmail=PROGRAM_EMAIL; // should be from a database lookup
$HeaderTemplateFile="Local/HeaderTemplate.html";
$FooterTemplateFile="Local/FooterTemplate.html";
$ReportDB=REPORTDB; // Temporary, until we move to one database for it all

// Failover if REPORTDB isn't set.
if ($ReportDB=="REPORTDB") {$ReportDB=DBDB;}

// Database link
$link = mysql_connect(DBHOSTNAME,DBUSERID,DBPASSWORD);
mysql_select_db($ReportDB,$link);

// Establish the con info
$query= <<<EOF
SELECT
    conname,
    constartdate
  FROM
      ConInfo
  WHERE
      conid=$ConKey
EOF;

// Retrieve query fail if database can't be found, and if there isn't just one result
if (($result=mysql_query($query,$link))===false) {
  $message ="<P>Error retrieving data from database.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (0==($rows=mysql_num_rows($result))) {
  $message.="<P>No results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (1<($rows=mysql_num_rows($result))) {
  $message.="<P>Too many results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
$ConInfo_array=mysql_fetch_assoc($result);

$ConName=$ConInfo_array['conname'];
$ConStartDate=$ConInfo_array['constartdate'];

$constart=strtotime($ConStartDate);
$nowis=time();

// Establish the states, for the look of the page
$query= <<<EOF
SELECT
    phasestate,
    phasetypename
  FROM
      Phase
    JOIN PhaseTypes USING (phasetypeid)
  WHERE
    conid=$ConKey
EOF;

// Retrieve query, fail if database can't be found, or there aren't any results
if (($result=mysql_query($query,$link))===false) {
  $message ="<P>Error retrieving data from database.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}
if (0==($rows=mysql_num_rows($result))) {
  $message.="<P>No results found.</P>\n<P>";
  $message.=$query;
  $message.="</P>\n";
}

// Set up the phase_array such that the typename is the key and the state is the value
for ($i=1; $i<=$rows; $i++) {
  $element_array=mysql_fetch_assoc($result);
  $phase_array[$element_array['phasetypename']]=$element_array['phasestate'];
}

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
  echo "<H2>Check out the below links to learn about the great programming and vending we will have at $ConName!</H2>\n";
 } else { 
echo "<H2>Check out the below links to give us/see your feedback and learn about the great programming we had at $ConName!</H2>\n";
 }
echo "<UL>\n";
if ($phase_array['Prog Available'] == '0' ) {
?>
  <LI><A HREF="webpages/Postgrid.php">Schedule Grid</A></LI>
  <LI><A HREF="webpages/Descriptions.php">Class Descriptions</A></LI>
  <LI><A HREF="webpages/Schedule.php">Schedule</A></LI>
  <LI><A HREF="webpages/Tracks.php">Tracks</A></LI>
  <LI><A HREF="webpages/Bios.php">Presenter Bios</A></LI>
<?php
  }
if ($phase_array['Vendors Available'] == '0' ) {
?>
  <LI><A HREF="webpages/Vendors.php">Vendor List</A></LI>
<?php
  }
if ($phase_array['Vol Available'] == '0' ) {
?>
  <LI><A HREF="webpages/Postgrid.php?volunteer=y">Volunteer Grid</A></LI>
  <LI><A HREF="webpages/Descriptions.php?volunteer=y">Volunteer Job Descriptions</A></LI>
<?php 
  }
if ($nowis < $constart) { ?>
  <LI><A HREF="webpages/">Presenter/Vendor Login</A></LI>
  <LI><FORM name="brainstormform" method="POST" action="webpages/doLogin.php">
  <INPUT type="hidden" name="badgeid" value="100">
  <INPUT type="hidden" name="passwd" value="submit">
  <INPUT type="hidden" name="target" value="brainstorm">
  <INPUT type="submit" name="submit" value="Suggest a Session/Presenter">
  </FORM>
  <LI><FORM name="vendorform" method="POST" action="webpages/doLogin.php">
  <INPUT type="hidden" name="badgeid" value="100">
  <INPUT type="hidden" name="passwd" value="submit">
  <INPUT type="hidden" name="target" value="vendor">
  <INPUT type="submit" name="submit" value="New Vendor Application">
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
