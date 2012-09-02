<?php
require_once('BrainstormCommonCode.php');
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}


$title="Search Panels";
$description="<P>Clicking Search without making any selections will display all panels.</P>";
$additionalinfo="";

// Test to see if searching is allowed
if (!may_I('BS_sear_sess')) {
  $message_error="You do not currently have permission to view this page.<BR>\n";
  RenderError($title,$message_error);
  exit();
 }

// Variables, passed in
if (isset($_POST["track"])) {
  $trackid=$_POST["track"];
 } else {
  $trackid=0;
 }
$titlesearch=stripslashes($_POST["title"]);

// Header, followed by search, followed by (if there is a search) the results of the search.
topofpagereport($title,$description,$additionalinfo);

// If there are any specific words for this event
if (file_exists("../Local/Verbiage/BrainstormSearchSession_0")) {
  echo file_get_contents("../Local/Verbiage/BrainstormSearchSession_0");
 }
?>

<FORM name="brainstormsearchsession" method=POST action="BrainstormSearchSession.php">
<INPUT type="hidden" name="issearch" value="1">
<TABLE>
  <TR>
    <TD>Track:</TD>
    <TD><SELECT class="tcell" name="track">
          <?php $query = "SELECT trackid, trackname FROM $ReportDB.Tracks WHERE selfselect=1 ORDER BY display_order"; populate_select_from_query($query, $trackid, "ANY", false); ?>
        </SELECT></TD>
    <TD>Title Search:</TD>
    <TD> <INPUT type="text" name="title" value="<?php echo $titlesearch; ?>"></TD>
  </TR>
  <TR>													      
    <TD colspan=5, align=right>
        <BUTTON type=submit value="search">Search</BUTTON>
    </TD>
  </TR>
</TABLE>
</FORM>

<?php

// Stop here if there wasn't a previous search using the "issearch" variable.
if (!isset($_POST["issearch"])) {
  correct_footer();
  exit();
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
    statusname in ('Edit Me','Brainstorm','Vetted','Assigned','Scheduled') and
    typename in ('Panel','Class','Presentation','Author Reading','Lounge','SIG/BOF/MnG','Social','EVENT','Performance')
EOD;
if ($trackid!=0) {
  $query.=" and trackid=".$trackid;
 }
if ($titlesearch!="") {
  $query.=" AND title LIKE \"%".mysql_real_escape_string($titlesearch,$link)."%\" ";
}
$query.=" ORDER BY trackname, title";

if (!$result=mysql_query($query,$link)) {
  $message=$query."<BR>Error querying database.<BR>";
  RenderError($title,$message);
  exit();
 }

RenderPrecis($result,$showlinks);
correct_footer();
exit();
?>
