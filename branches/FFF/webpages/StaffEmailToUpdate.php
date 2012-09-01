<?php
require_once('StaffCommonCode.php');
global $link;

$title="EmailTo Update";
$description="<P>Return to the <A HREF=\"genreport.php?reportname=emailtolist\">Possible EmailTo Recipients</A></P>\n";
$additionalinfo ="<P>The query should return the following:\n<UL>\n<LI>badgeid</LI>\n<LI>firstname</LI>\n<LI>lastname</LI>\n<LI>email</LI>\n<LI>pubsname</LI>\n<LI>badgename</LI>\n</UL></P>\n";
$additionalinfo.="<P>If the description matches permrolename from the PermissionRoles list, it will get sent to, from the pages that do some auto-email sending.</P>\n";

topofpagereport($title,$description,$additionalinfo);

// Submit the task, if there was one, when this was called
if ((isset($_POST["emailtoupdate"])) and ($_POST["emailtoupdate"]!="")) {
  if ($_POST["emailtoid"] == "-1") {
    $element_array=array('emailtodescription','display_order','emailtoquery');
    $value_array=array(htmlspecialchars_decode($_POST['emailtodescription']),
		       htmlspecialchars_decode($_POST['display_order']),
		       htmlspecialchars_decode(refrom($_POST['emailtoquery'])));
    submit_table_element($link, $title, "EmailTo", $element_array, $value_array);
   } else {
    $pairedvalue_array=array("emailtodescription='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['emailtodescription'])))."'",
			     "display_order='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['display_order'])))."'",
			     "emailtoquery='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode(refrom($_POST['emailtoquery']))))."'");
    $match_field="emailtoid";
    $match_value=$_POST['emailtoid'];
    update_table_element($link, $title, "EmailTo", $pairedvalue_array, $match_field, $match_value);
   }
}

// Clear the agendaupdate value
$emailtoupdate="";

// Carry over the task list element, from the form before, if they exist
if (isset($_POST["emailtoid"])) {
  $emailtoid=$_POST["emailtoid"];
 } elseif (isset($_GET["emailtoid"])) {
  $emailtoid=$_GET["emailtoid"];
 } else {
  $emailtoid=0;
 }

// Build the top of form query
$query=<<<EOD
SELECT
    emailtoid,
    emailtodescription
  FROM
      EmailTo
  ORDER BY
    display_order
EOD;

if (!$emailtoresult=mysql_query($query,$link)) {
  $message=$query."<BR>Error querying database. Unable to continue.<BR>";
  echo "<P class\"errmsg\">".$message."\n";
  correct_footer();
  exit();
 }

?>

<FORM name="emailtolistselect" method=POST action="StaffEmailToUpdate.php">
<DIV><LABEL for="emailtoid">Select EmailTo Query</LABEL>
<SELECT name="emailtoid">

<?php 
echo "     <OPTION value=0";
if ($emailtoid==0) {echo " selected";}
echo ">Select EmailTo query</OPTION>\n";
echo "     <OPTION value=-1>New EmailTo query</OPTION>\n";
while (list($emailtocheckid,$emailtocheckdescription)=mysql_fetch_array($emailtoresult, MYSQL_NUM)) {
  echo "     <OPTION value=\"$emailtocheckid\"";
  if ($emailtoid==$emailtocheckid) {echo " selected";}
  echo ">".htmlspecialchars($emailtocheckdescription)."</OPTION>\n";
  }
?>

</SELECT>
</DIV>
<DIV class="SubmitDiv">
<BUTTON class="SubmitButton" type="submit" name="submit">Submit</BUTTON>
</DIV>
</FORM>

<?php
// Stop page here if and individual has not yet been selected
if ($emailtoid==0) {
  correct_footer();
  exit();
 }

// Switch on if it is a new report or not
if ($emailtoid == "-1") {
  $emailtodescription="No Description yet";
  $display_order='';
  $emailtoquery="No Query Yet";
 } else {

$query= <<<EOD
SELECT
    emailtodescription,
    display_order,
    emailtoquery
  FROM
      EmailTo
  WHERE
    emailtoid='$emailtoid'

EOD;

  list($rows,$header_array,$emailto_array)=queryreport($query,$link,$title,$description,0);

  $emailtodescription=$emailto_array[1]['emailtodescription'];
  $display_order=$emailto_array[1]['display_order'];
  $emailtoquery=$emailto_array[1]['emailtoquery'];

}
// Update form
?>

<HR>
<FORM name="emailtoform" method=POST action="StaffEmailToUpdate.php">
<DIV class="titledtextarea">
<INPUT type="hidden" name="emailtoid" value="<?php echo $emailtoid; ?>">
<INPUT type="hidden" name="emailtoupdate" value="Yes">
<LABEL for"emailtodescription">Description:</LABEL>
<INPUT type="text" size="65" name="emailtodescription" id="emailtodescription" value="<?php echo htmlspecialchars($emailtodescription) ?>">
<LABEL for="emailtoquery">Query: (note, due to a strange anomoly in the system, "FROM" is rendered in pig-latin.  Do not worry, it gets fixed.)</LABEL>
  <TEXTAREA name="emailtoquery" rows=6 cols=72><?php echo htmlspecialchars(unfrom($emailtoquery)) ?></TEXTAREA>
<LABEL for="display_order">Display Order:</LABEL>
<INPUT type="text" size=3 name="display_order" id="display_order" value="<?php echo htmlspecialchars($display_order) ?>">
</DIV>

<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
correct_footer();
?>
