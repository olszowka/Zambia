<?php
require_once('StaffCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted

// LOCALIZATIONS
$_SESSION['return_to_page']="EditGroupFlows.php";
$title="Edit Group Flow Reports";
$description="<P>Edit the order of the various group flows.</P>\n";
$additionalinfo="<P><A HREF=genindex.php>Return</A> to the Group Flow choice page.</P>";

if (isset($_POST['addto'])) {
  add_flow_report($_POST['addto'],$_POST['addphase'],"Group",$_POST['togroup'],$title,$description);
 }

if (isset($_POST['unrank'])) {
  remove_flow_report($_POST['unrank'],"Group",$title,$description);
 }

if (isset($_POST['upfrom'])) {
  deltarank_flow_report($_POST['upfrom'],"Group","Up",$title,$description);
 }

if (isset($_POST['downfrom'])) {
  deltarank_flow_report($_POST['downfrom'],"Group","Down",$title,$description);
 }

// Forms inserted into the query
$uprank_query ="concat('<FORM name=\"uprank\" method=POST action=\"EditGroupFlows.php\">";
$uprank_query.="<INPUT type=\"hidden\" name=\"upfrom\" value=\"',GF.gflowid,'\">";
$uprank_query.="<INPUT type=submit value=\"Move Up\">";
$uprank_query.="</FORM>') as Earlier,";
$downrank_query ="concat('<FORM name=\"downrank\" method=POST action=\"EditGroupFlows.php\">";
$downrank_query.="<INPUT type=\"hidden\" name=\"downfrom\" value=\"',GF.gflowid,'\">";
$downrank_query.="<INPUT type=submit value=\"Move down\">";
$downrank_query.="</FORM>') as Later,";
$addto_query ="concat('<FORM name=\"addto\" method=POST action=\"EditGroupFlows.php\">";
$addto_query.="<INPUT type=\"hidden\" name=\"addto\" value=\"',R.reportid,'\">";
$addto_query.="<LABEL for=\"addphase\" ID=\"addphase\">(Phase)</LABEL>";
$addto_query.="<INPUT type=\"text\" name=\"addphase\" size=\"1\">";
$addto_query.="<LABEL for=\"togroup\" ID=\"togroup\"><BR>(Group)</LABEL>";
$addto_query.="<INPUT type=\"text\" name=\"togroup\" size=\"10\">";
$addto_query.=" <INPUT type=submit value=\"Add\">";
$addto_query.="</FORM>') as 'Add To<BR>Group',";
$remove_query ="concat('<FORM name=\"unrank\" method=POST action=\"EditGroupFlows.php\">";
$remove_query.="<INPUT type=\"hidden\" name=\"unrank\" value=\"',GF.gflowid,'\">";
$remove_query.="<INPUT type=submit value=\"Remove\">";
$remove_query.="</FORM>') as Remove,";

// First table, list of phases and their phaseids
$query = <<<EOD
SELECT
    phaseid,
    concat(phasename,if ((current=TRUE),' (c)',' ')) AS Phases
  FROM
    Phases  
  ORDER BY
    phaseid
EOD;

// Retrieve query
list($phaserows,$phaseheader_array,$phasereport_array)=queryreport($query,$link,$title,$description,0);

// Add the "all" entry, just in case.
$phaserows++;
$phasereport_array[$phaserows]['Phases']="ALL";

// Grouped reports and mods for them
$query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",R.reportid,">",R.reporttitle,"</A>") AS Title,
    $uprank_query
    $downrank_query
    $addto_query
    $remove_query
    GF.gflowname,
    GF.gfloworder,
    if((GF.phaseid IS NULL),'ALL',P.phasename) as Phase
  FROM
      $ReportDB.GroupFlow GF,
      $ReportDB.Reports R,
      Phases P
  WHERE
    GF.reportid=R.reportid AND
    (GF.phaseid is NULL OR (GF.phaseid = P.phaseid AND P.current = TRUE))
  ORDER BY
    P.phasename,GF.gflowname,GF.gfloworder
EOD;

// Retrieve query
list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

// Ungrouped reports and an add for them
$query = <<<EOD
SELECT
    $addto_query
    concat("<A HREF=genreport.php?reportid=",R.reportid,">",R.reporttitle,"</A>") AS Title
  FROM
      $ReportDB.Reports R
    LEFT JOIN $ReportDB.GroupFlow GF ON R.reportid=GF.reportid
  WHERE
    GF.reportid IS NULL
  ORDER BY
    R.reportid
EOD;

// Retrieve query
list($unrows,$unheader_array,$unreport_array)=queryreport($query,$link,$title,$description,0);

// All reports, and their groups
$query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",R.reportid,">",R.reporttitle,"</A>") AS Title,
    group_concat(GF.gflowname) AS Groups
  FROM
      $ReportDB.Reports R,
      $ReportDB.GroupFlow GF
  WHERE
    R.reportid=GF.reportid
  GROUP BY
    R.reportid
  ORDER BY
    R.reportid
EOD;

// Retrieve query
list($fullrows,$fullheader_array,$fullreport_array)=queryreport($query,$link,$title,$description,0);

// Page Rendering
topofpagereport($title,$description,$additionalinfo);
echo renderhtmlreport(1,$phaserows,$phaseheader_array,$phasereport_array);
echo renderhtmlreport(1,$rows,$header_array,$report_array);
echo renderhtmlreport(1,$unrows,$unheader_array,$unreport_array);
echo renderhtmlreport(1,$fullrows,$fullheader_array,$fullreport_array);
correct_footer();

?>