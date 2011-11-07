<?php
require_once('StaffCommonCode.php');
require ('RenderEditCreateSession.php');
global $name, $email, $message2;
get_name_and_email($name,$email);
$error=false;
$title="Edit Session";
$description="<P>Please, select the session you wish to edit.</P>";

if (isset($_GET["id"])) { // Sets the "id" from the GET string
  $id=$_GET["id"];
 } elseif (isset($_POST["id"])) { // Sets the "id" from the POST string
  $id=$_POST["id"];
 }

if ((is_numeric($id)) and ($id>0)) { // If the "id" is numerica and greater than one, test it
  $status=retrieve_session_from_db($id);
  if ($status==-3) {
    $message_error.="Error retrieving record from database. ".$message2;
    $error=true;
    $id="";
   }
  if ($status==-2) {
    $message_error.="Session record with id=".$id." not found (or error with Session primary key).";
    $error=true;
    $id="";
   }
 }

// If the "id" still is not set, or reset to "", add the "Select" to the top of the form, so it can be chosen.
if ((!isset($id)) or ($id=="")) {
  $query=<<<EOD
SELECT
    sessionid,
    concat(trackname,' - ',sessionid,' - ',title) as sname
  FROM
      Sessions
    JOIN Tracks USING (trackid)
    JOIN SessionStatuses USING (statusid)
  WHERE
    may_be_scheduled=1
  ORDER BY
    trackname,
    sessionid,
    title
EOD;

  topofpagereport($title,$description,$additionalinfo);
  if (isset($message_error)) {
    echo "<P class=\"errmsg\">$message_error</P>\n";
   }
  echo "<FORM name=\"idform\" method=POST action=\"EditSession.php\">\n";
  echo "<DIV><LABEL for=\"id\">Select Session</LABEL>\n";
  echo "<SELECT name=\"id\">\n";
  populate_select_from_query($query, $sessionid, "Select Session", false);
  echo "</SELECT></DIV>\n";
  echo "<P>&nbsp;\n";
  echo "<DIV class=\"SubmitDiv\">";
  if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</A>";
  }
  echo "<BUTTON type=\"submit\" name=\"submit\" class=\"SubmitButton\">Select Session</BUTTON></DIV>\n";
  echo "</FORM>\n";
  correct_footer();
  exit();
 }

// Set up for Rendering
$message_warn="";
$action="edit";

// Actually do the rendering work
RenderEditCreateSession($action,$session,$message_warn,$message_error);
exit();
?>
