<?php
require_once('CommonCode.php');

// Render Error reporting
function RenderError($title,$message) {
  if ($_SESSION['role'] == "Brainstorm") {
    brainstorm_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  }
  elseif ($_SESSION['role'] == "Vendor") {
    vendor_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  }
  elseif ($_SESSION['role'] == "Participant") {
    participant_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  }
  elseif ($_SESSION['role'] == "Staff") {
    global $debug;
    staff_header($title);
    if (isset($debug)) echo $debug."<BR>\n";
    echo "<P id=\"errmsg\">".$message."</P>\n";
  }
  elseif ($_SESSION['role'] == "Posting") {
    posting_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
  }
  else {
    // do something generic here (though this might be way too generic)
    // better to output some error message reliably than none at all
    echo "<html>";
    echo "<head>";
    echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
    echo "<title>Zambia -".$title."</title>";
    echo "</head>";
    echo "<body>";
    echo "<H1>Zambia&ndash;The".CON_NAME."Scheduling Tool</H1>";
    echo "<hr>";
    echo "<p> An error occurred: </p>";
    echo $message;
    echo "</body>";
    echo "</html>";
  }
  correct_footer();
}

?>
