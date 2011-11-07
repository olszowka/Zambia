<?php
require_once('BrainstormCommonCode.php');

$title="All Suggestions";
$description="<P>This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</P>\n";
$additionalinfo ="<P>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces;";
$additionalinfo.=" checking general feasability; finding needed people to present; looking for an appropiate time and location;";
$additionalinfo.=" rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</P>\n";

$additionalinfo.="<P>If you want to help, email us at: ";
$additionalinfo.="<A HREF=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</A></P>\n";
$additionalinfo.="<P>This list is sorted by Track and then Title.</P>\n";

$showlinks=$_GET["showlinks"];
$_SESSION['return_to_page']="ViewPrecis.php?showlinks=$showlinks";
if ($showlinks=="1") {
  $showlinks=true;
}
elseif ($showlinks="0") {
  $showlinks=false;
}
if (prepare_db()===false) {
  $message="Error connecting to database.";
  RenderError($title,$message);
  exit ();
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
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses USING (statusid)
    JOIN Types USING (typeid)
  WHERE
    statusname in ('Edit Me','Brainstorm','Vetted','Assigned','Scheduled') and
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

topofpagereport($title,$description,$additionalinfo);
RenderPrecis($result,$showlinks);
correct_footer();
exit();
?> 
