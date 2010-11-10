<?php
require_once('StaffCommonCode.php');
require ('RenderEditCreateSession.php');
global $name, $email, $message2;
get_name_and_email($name,$email);
$error=false;
$message_error="";

// Sets the "id" from the GET string
$id=$_GET["id"];

// If the "id" is not set, check to see if it is from the POST string
if ($id=="") {$id=$_POST["id"];}

// If the "id" still is not set, add the "Select" to the top of the form, so it can be chosen.
if ($id=="") {
  staff_header("Edit a Session");
  $query="SELECT T.trackname, S.sessionid, S.title FROM Sessions AS S ";
  $query.="JOIN Tracks AS T USING (trackid) ";
  $query.="JOIN SessionStatuses AS SS USING (statusid) ";
  $query.="WHERE SS.may_be_scheduled=1 ";
  $query.="ORDER BY T.trackname, S.sessionid, S.title";
  if (!$Sresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
  }
  echo "<FORM name=\"idform\" method=POST action=\"EditSession.php\">\n";
  echo "<DIV><LABEL for=\"id\">Select Session</LABEL>\n";
  echo "<SELECT name=\"id\">\n";
  echo "     <OPTION value=0 SELECTED>Select Session</OPTION>\n";
  while (list($trackname,$sessionid,$title)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$sessionid."\">".htmlspecialchars($trackname)." - ";
    echo htmlspecialchars($sessionid)." - ".htmlspecialchars($title)."</OPTION>\n";
  }
  echo "</SELECT></DIV>\n";
  echo "<P>&nbsp;\n";
  echo "<DIV class=\"SubmitDiv\">";
  if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
  }
  echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
  echo "</FORM>\n";
  echo "<HR>&nbsp;<BR>\n";
  staff_footer();
  exit();
 }

// Make sure it's an integer
$id=intval($id);

// Check to make sure the ID selected is positive
if ($id<1) {
  $message_error.="The id parameter must be a valid row index.";
  $error=true;
 }

// Check to see if the id exists in the database
$status=retrieve_session_from_db($id);
if ($status==-3) {
  $message_error.="Error retrieving record from database. ".$message2;
  $error=true;
 }
if ($status==-2) {
  $message_error.="Session record with id=".$id." not found (or error with Session primary key).";
  $error=true;
 }

// Set up for Rendering
$message_warn="";
$action="edit";

// Actually do the rendering work
RenderEditCreateSession($action,$session,$message_warn,$message_error);
exit();
?>
