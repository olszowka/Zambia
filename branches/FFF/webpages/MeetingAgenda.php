<?php
require_once('StaffCommonCode.php');
global $link;

$title="Meeting Agenda Update";
$description="<P>Return to the <A HREF=\"genreport.php?reportname=meetingagendadisplay\">Meeting Agenda</A></P>";

// Submit the task, if there was one, when this was called
if ((isset($_POST["agendaupdate"])) and ($_POST["agendaupdate"]!="")) {
  if ($_POST["agendaid"] == "-1") {
    $element_array=array('agendaname','permroleid','agenda','agendanotes','meetingtime');
    $value_array=array(htmlspecialchars_decode($_POST['agendaname']),
		       htmlspecialchars_decode($_POST['permroleid']),
		       htmlspecialchars_decode($_POST['agenda']),
		       htmlspecialchars_decode($_POST['agendanotes']),
		       htmlspecialchars_decode($_POST['meetingtime']));
    $message.=submit_table_element($link, $title, "AgendaList", $element_array, $value_array);
   } else {
    $pairedvalue_array=array("agendanotes='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['agendanotes'])))."'",
			     "agendaname='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['agendaname'])))."'",
			     "permroleid='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['permroleid'])))."'",
			     "agenda='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['agenda'])))."'",
			     "meetingtime='".mysql_real_escape_string(stripslashes(htmlspecialchars_decode($_POST['meetingtime'])))."'");
    $match_field="agendaid";
    $match_value=$_POST['agendaid'];
    $message.=update_table_element($link, $title, "AgendaList", $pairedvalue_array, $match_field, $match_value);
   }
 }

// Carry over the task list element, from the form before, if they exist
if (isset($_POST["agendaid"])) {
  $agendaid=$_POST["agendaid"];
 }
 elseif (isset($_GET["agendaid"])) {
  $agendaid=$_GET["agendaid"];
 }
 else {
  $agendaid=0;
 }

// Build the top of form query
$query=<<<EOD
SELECT
    agendaid,
    concat(permrolename,": ",agendaname) as aname
  FROM
      AgendaList
    JOIN $ReportDB.PermissionRoles USING (permroleid)
  ORDER BY
    permrolename,
    agendaname
EOD;

if (!$agendaresult=mysql_query($query,$link)) {
  $message_error=$query."<BR>Error querying database. Unable to continue.<BR>";
  RenderError($title,$message_error);
  exit();
 }

// Begin the page
topofpagereport($title,$description,$additionalinfo);

// Any messages
echo "<P class=\"errmsg\">$message_error</P>\n";
echo "<P class=\"regmsg\">$message</P>\n";

?>

<FORM name="agendalistselect" method=POST action="MeetingAgenda.php">
<DIV><LABEL for="agendaid">Select Agenda</LABEL>
<SELECT name="agendaid">
<OPTION value=-1>New Agenda</OPTION>
<?php populate_select_from_query($query, $agendaid, "Select Agenda from the List Below", false); ?>
</SELECT></DIV>
<DIV class="SubmitDiv">
<BUTTON class="SubmitButton" type="submit" name="submit" >Submit</BUTTON>
</DIV>
</FORM>

<?php
// Stop page here if and individual has not yet been selected
if ($agendaid==0) {
  correct_footer();
  exit();
 }

// Switch on if it is a new report or not
if ($agendaid == "-1") {
  $agendaname="New Agenda";
  $agenda="Enter Agenda Here";
  $permroleid='';
  $agendanotes='';
  $meetingtime='';
 } else {

  $query= <<<EOD
SELECT
    agendaname,
    agenda,
    permroleid,
    agendanotes,
    meetingtime
  FROM
      AgendaList
  WHERE
    agendaid='$agendaid'

EOD;

  list($rows,$header_array,$agenda_array)=queryreport($query,$link,$title,$description,0);

  $agendaname=$agenda_array[1]['agendaname'];
  $agenda=$agenda_array[1]['agenda'];
  $permroleid=$agenda_array[1]['permroleid'];
  $agendanotes=$agenda_array[1]['agendanotes'];
  $meetingtime=$agenda_array[1]['meetingtime'];

}
// Update form
?>

<HR>
<FORM name="meetingagendaform" method=POST action="MeetingAgenda.php">
<DIV class="titledtextarea">
<INPUT type="hidden" name="agendaid" value="<?php echo $agendaid; ?>">
<INPUT type="hidden" name="agendaupdate" value="Yes">
<LABEL for"agendaname">Agenda Name:</LABEL>
<INPUT type="text" size="25" name="agendaname" id="agendaname" value="<?php echo htmlspecialchars($agendaname); ?>">
<LABEL for="agenda">Agenda:</LABEL>
<TEXTAREA name="agenda" rows=6 cols=72><?php echo $agenda; ?></TEXTAREA>
<LABEL for="agendanotes">Notes:</LABEL>
<TEXTAREA name="agendanotes" rows=6 cols=72><?php echo $agendanotes; ?></TEXTAREA>
<LABEL for="meetingtime">Meeting Time: (eg: 2038-12-12)</LABEL>
<INPUT type="text" size=10 name="meetingtime" id="meetingtime" value="<?php echo htmlspecialchars($meetingtime); ?>">
<LABEL for="permroleid">Select Meeting Group:</LABEL>
<SELECT name="permroleid">
<?php $query="SELECT permroleid, permrolename FROM $ReportDB.PermissionRoles" ; populate_select_from_query($query, $permroleid, "Select Meeting Group", false); ?>
</SELECT>
</DIV>

<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
correct_footer();
?>
