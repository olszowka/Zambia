<?php
require_once('CommonCode.php');

// Render Error reporting
function StaffRenderError ($title, $message) {
    global $debug;
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    staff_header($title);
    if (isset($debug)) echo $debug."<BR>\n";
    echo "<P id=\"errmsg\">".$message."</P>\n";
    staff_footer();
    }

function PartRenderError ($title, $message) {
    participant_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
    participant_footer();
    }

function BrainstormRenderError ($title, $message) {
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    brainstorm_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
    brainstorm_footer();
    }

function PostingRenderError ($title, $message) {
    require_once('PostingHeader.php');
    require_once('PostingFooter.php');
    posting_header($title);
    echo "<P id=\"errmsg\">".$message."</P>\n";
    posting_footer();
    }

function RenderError($title,$message) {
  if ($_SESSION['role'] == "Brainstorm") {
    BrainstormRenderError($title,$message);
  }
  elseif ($_SESSION['role'] == "Participant") {
    PartRenderError($title,$message);
  }
  elseif ($_SESSION['role'] == "Staff") {
    StaffRenderError($title,$message);
  }
  elseif ($_SESSION['role'] == "Posting") {
    PostingRenderError($title,$message);
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
}

?>
