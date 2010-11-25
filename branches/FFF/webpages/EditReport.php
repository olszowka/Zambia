<?php
require_once('StaffCommonCode.php');

$title="Edit Reports";
global $link;
staff_header($title);

function unfrom ($transstring) {
   $badfrom = array("FROM", "From", "from");
   $goodfrom = array("UMFRAY", "Umfray", "umfray");
   return str_replace ($badfrom, $goodfrom, $transstring);
   }

function refrom ($transstring) {
   $badfrom = array("FROM", "From", "from");
   $goodfrom = array("UMFRAY", "Umfray", "umfray");
   return str_replace ($goodfrom, $badfrom, $transstring);
   }

function SubmitEditReport () {
    global $link;
    $reportid = $_POST["selreport"];
    $reportdescription = htmlspecialchars_decode($_POST["reportdescription"]);
    $reportadditionalinfo = htmlspecialchars_decode($_POST["reportadditionalinfo"]);
    $reportquery = htmlspecialchars_decode(refrom($_POST["reportquery"]));
    $query = "update Reports set ";
    $query.="reportdescription=\"".mysql_real_escape_string(stripslashes($reportdescription));
    $query.="\",reportadditionalinfo=\"".mysql_real_escape_string(stripslashes($reportadditionalinfo));
    $query.="\",reportquery=\"".mysql_real_escape_string(stripslashes($reportquery));
    $query.="\" where reportid=".$reportid;
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        echo "<P class=\"errmsg\">".$message."\n";
        return;
        }
    $message="Database updated successfully.<BR>";
    echo "<P class=\"regmsg\">".$message."\n";
    }

function SubmitNewReport () {
    global $link;
    $reportname = $_POST["reportname"];
    $reporttitle = $_POST["reporttitle"];
    $reportdescription = $_POST["reportdescription"];
    $reportadditionalinfo = $_POST["reportadditionalinfo"];
    $reportquery = $_POST["reportquery"];
    $query = "INSERT INTO Reports (reportname,reporttitle,reportdescription,reportadditionalinfo,reportquery) VALUES ('";
    $query.=mysql_real_escape_string($reportname)."','";
    $query.=mysql_real_escape_string($reporttitle)."','";
    $query.=mysql_real_escape_string(stripslashes($reportdescription))."','";
    $query.=mysql_real_escape_string(stripslashes($reportadditionalinfo))."','";
    $query.=mysql_real_escape_string(stripslashes($reportquery))."')";
    if (!mysql_query($query,$link)) {
        $message=$query."<BR>Error updating database.  Database not updated.";
        echo "<P class=\"errmsg\">".$message."\n";
        return;
        }
    $message="Database updated successfully.<BR>";
    echo "<P class=\"regmsg\">".$message."\n";
    }

$topsectiononly=true; // no reportquery selected -- flag indicates to display only the top section of the page
if ($_POST["submit"]=="updatereport") {
    SubmitEditReport(); 
    }

if ($_POST["submit"]=="newreport") {
    SubmitNewReport(); 
    }

if (isset($_POST["selreport"])) {
        $selreportid=$_POST["selreport"];
        $topsectiononly=false;
        }
    elseif (isset($_GET["selreport"])) { // reportid was select by external page such as a report
        $selreportid=$_GET["selreport"];
        $topsectiononly=false;
        }
    else {
        $selreportid=0; // reportid was not yet selected.
        }

if ($selreportid==0) {
        $topsectiononly=true;
        unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }


if ($_GET["selreport"]=="-1") {
  $reportname='No Name Yet';
  $reporttitle='No Title Yet';
  $reportdescription='No Description Yet';
  $reportadditionalinfo='';
  $reportquery='No Query Yet';
 } else {

$query="SELECT reportid, reporttitle, reportdescription FROM Reports ";
$query.="ORDER BY reportid";
if (!$Sresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<FORM name=\"selreportform\" method=POST action=\"EditReport.php\">\n";
echo "<DIV><LABEL for=\"selreport\">Select Report</LABEL>\n";
echo "<SELECT name=\"selreport\">\n";
echo "     <OPTION value=0 ".(($selreportid==0)?"selected":"").">Select Report</OPTION>\n";
while (list($reportid,$reporttitle,$reportdescription)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$reportid."\" ".(($selreportid==$reportid)?"selected":"");
    echo ">".htmlspecialchars($reporttitle).": ".htmlspecialchars($reportdescription)."</OPTION>\n";
    }
echo "</SELECT></DIV>\n";
echo "<P>&nbsp;\n";
echo "<DIV class=\"SubmitDiv\">";
if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
    }
echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\" value=\"selectreport\">Select</BUTTON></DIV>\n";
echo "</FORM>\n";
if ($topsectiononly) {
    staff_footer();
    exit();
    }
$query="SELECT reporttitle, reportdescription, reportadditionalinfo, reportquery FROM Reports ";
$query.="WHERE reportid=$selreportid";
if (!$result=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
    }
list($reporttitle, $reportdescription, $reportadditionalinfo, $reportquery)= mysql_fetch_array($result, MYSQL_NUM);
 }
echo "<HR>&nbsp;<BR>\n";
echo "<FORM name=\"reporteditform\" method=POST action=\"EditReport.php\">";
if ($_GET["selreport"]=="-1") {
?>
<DIV class="titledtextarea">
  <SPAN><LABEL for="reportname"><B>Name:</B><BR></LABEL>
  <INPUT type="text" size=72 name="reportname" id="reportname" value="<?php echo htmlspecialchars($reportname) ?>"></SPAN>
  <SPAN><LABEL for="reporttitle"><BR><B>Title:</B><BR></LABEL>
  <INPUT type="text" size=72 name="reporttitle" id="reporttitle" value="<?php echo htmlspecialchars($reporttitle) ?>"></SPAN>
<?php } else { ?>
  <P>Edit <?php echo htmlspecialchars($reporttitle)?> for <?php echo CON_NAME; ?>:
<DIV class="titledtextarea">
<?php } ?>
  <LABEL for="reportdescription">Description:</LABEL>
  <TEXTAREA name="reportdescription" rows=2 cols=72><?php echo htmlspecialchars($reportdescription) ?></TEXTAREA>
  <LABEL for="reportadditionalinfo">Additional Information:</LABEL>
  <TEXTAREA name="reportadditionalinfo" rows=2 cols=72><?php echo htmlspecialchars($reportadditionalinfo) ?></TEXTAREA>
  <LABEL for="reportquery">Query: (note, due to a strange anomoly in the system, "FROM" is rendered in pig-latin.  Do not worry, it gets fixed.)</LABEL>
  <TEXTAREA name="reportquery" rows=15 cols=72><?php echo htmlspecialchars(unfrom($reportquery)) ?></TEXTAREA>
</DIV> 
<INPUT type="hidden" name="selreport" value="<?php echo $selreportid; ?>">
<BUTTON class="SubmitButton" type="submit" name="submit" value=<?php 
if ($_GET["selreport"]=="-1") { 
  echo "\"newreport\"";
} else {
  echo "\"updatereport\"";
 }
?>>Update</BUTTON>
</FORM>
<?php
staff_footer();
?>
