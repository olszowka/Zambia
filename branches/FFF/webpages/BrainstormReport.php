<?php
require_once('BrainstormCommonCode.php');
global $link;

$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}

if ($_GET['status']=="scheduled") {
  $selstatus="'Assigned','Scheduled'";
  $title="Scheduled Suggestions";
  $description="<P>These ideas are highly likely to make it into the final schedule. Things are looking good for them.</P>\n";
  $additionalinfo ="<P>Please remember events out of our control and last minute emergencies cause this to change!";
  $additionalinfo.=" No promises, but we are doing our best to have this happen.</P>\n";
} elseif ($_GET['status']=="likely") {
  $selstatus="'Vetted','Assigned','Scheduled'";
  $title="Likely to Occur Suggestions";
  $description="<P>These ideas have made the first cut.</P>\n";
  $additionalinfo ="<P>We like these ideas and would like to see them happen. Now to just find all the right people... </P>\n";
} elseif ($_GET['status']=="reviewed") {
  $selstatus="'Edit Me','Vetted','Assigned','Scheduled'";
  $title="Reviewed Suggestions";
  $description="<P>We've seen these. They have varying degrees of merit.</P>\n";
  $additionalinfo ="<P>We have or will sort through these suggestions: combining duplicates; splitting big ones into pieces;";
  $additionalinfo.=" checking general feasability; finding needed people to present; looking for an appropiate time and location;";
  $additionalinfo.=" rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";
  $additionalinfo.="<P>Note that ideas that we like and are pursuing further will stay on this list.  That is to make it easier";
  $additionalinfo.=" to find the idea you suggested.</P>\n";
} elseif ($_GET['status']=="unseen") {
  $selstatus="'Brainstorm'";
  $title="New (Unseen) Suggestions";
  $description="<P>If an idea is on this page, there is a good chance we have not yet seen it.</P>\n";
  $additionalinfo="<P>So, please wear your Peril Sensitive Sunglasses while reading. We do.</P>\n";
} elseif ($_GET['status']=="all") {
  $selstatus="'Edit Me','Brainstorm','Vetted','Assigned','Scheduled'";
  $title="All Suggestions";
  $description="<P>This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</P>\n";
  $additionalinfo ="<P>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces;";
  $additionalinfo.=" checking general feasability; finding needed people to present; looking for an appropiate time and location;";
  $additionalinfo.=" rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";
} else { // Same as status == all.
  $selstatus="'Edit Me','Brainstorm','Vetted','Assigned','Scheduled'";
  $title="All Suggestions";
  $description="<P>This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</P>\n";
  $additionalinfo ="<P>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces;";
  $additionalinfo.=" checking general feasability; finding needed people to present; looking for an appropiate time and location;";
  $additionalinfo.=" rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";
}

$additionalinfo.="<P>If you want to help, email us at: ";
$additionalinfo.="<A HREF=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</A></P>\n";
$additionalinfo.="<P>This list is sorted by Track and then Title.</P>\n";

if (!empty($_SERVER['QUERY_STRING'])) {
  $_SESSION['return_to_page']="BrainstormReport.php?".$_SERVER['QUERY_STRING'];
} else {
  $_SESSION['return_to_page']="BrainstormReport.php?status=all";
}

$query = <<<EOD
SELECT
    sessionid,
    trackname,
    null typename,
    title, 
    CASE
      WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')
      ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    estatten,
    pocketprogtext,
    progguiddesc,
    persppartinfo
  FROM
      Sessions
    JOIN $ReportDB.Tracks USING (trackid)
    JOIN $ReportDB.SessionStatuses USING (statusid)
    JOIN $ReportDB.Types USING (typeid)
  WHERE
    statusname in ($selstatus) and
    typename in ('Panel','Class','Presentation','Author Reading','Lounge','SIG/BOF/MnG','Social','EVENT','Performance')
  ORDER BY
    trackname,
    title
EOD;

if (($result=mysql_query($query,$link))===false) {
  $message="Error retrieving data from database.";
  RenderError($title,$message);
  exit ();
}

if (may_I("Staff")) {$showlinks=true;} else {$showlinks=false;}

topofpagereport($title,$description,$additionalinfo);
RenderPrecis($result,$showlinks);
correct_footer();
exit();
?> 
