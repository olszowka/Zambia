<?php
require_once('StaffCommonCode.php');
global $link;
$ReportDB=REPORTDB; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}

// LOCALIZATIONS
$searchstring1=$_GET["searchstring1"];
$searchstring2=$_GET["searchstring2"];
$searchstring3=$_GET["searchstring3"];
$searchstring4=$_GET["searchstring4"];

if ($searchstring1) {$searchstring.=" AND reportquery like '%".$searchstring1."%'";}
if ($searchstring2) {$searchstring.=" AND reportquery like '%".$searchstring2."%'";}
if ($searchstring3) {$searchstring.=" AND reportquery like '%".$searchstring3."%'";}
if ($searchstring4) {$searchstring.=" AND reportquery like '%".$searchstring4."%'";}
$_SESSION['return_to_page']="genindex.php";
$title="Search for report";
$description="<P>Search for a report.</P>\n";
$additionalinfo="<P>Please put in the field you want to search for (\"ANDed\"), so it can generate the appropriate list of reports.</P>";
$additionalinfo.="<P>If there is a report to be tweaked or added, email <A HREF=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</A> and let us know.</P>\n";

// Page Rendering
topofpagereport($title,$description,$additionalinfo);

?>
<FORM name="searchform" method=GET action="searchreport.php">
      <DIV><LABEL for="searchstring">Search terms: </LABEL>
      <INPUT type=text name="searchstring1" size=12 value=<?php echo $_GET["searchstring1"]; ?>>
      <INPUT type=text name="searchstring2" size=12 value=<?php echo $_GET["searchstring2"]; ?>>
      <INPUT type=text name="searchstring3" size=12 value=<?php echo $_GET["searchstring3"]; ?>>
      <INPUT type=text name="searchstring4" size=12 value=<?php echo $_GET["searchstring4"]; ?>></DIV>
      <P>&nbsp
      <DIV class="SubmitDiv"><BUTTON type="submit" name="submit" class="SubmitButton">Search</BUTTON></DIV>
</FORM>

<?php
if (!isset($searchstring)) {
  correct_footer();
  exit;
}

$query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",reportid,">",reporttitle,"</A> (<A HREF=genreport.php?reportid=",reportid,"&csv=y>csv</A>)") AS Title,
    reportdescription AS Description
  FROM
    $ReportDB.Reports
  WHERE
    reportid > 0

EOD;
$query.=$searchstring;
// Retrieve query
list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

// Page Rendering
echo "<HR>\n";
echo renderhtmlreport(1,$rows,$header_array,$report_array);
correct_footer();
?>
