<?php
    require_once('CommonCode.php');
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    $_SESSION['role'] = "Participant";
    $badgeid=$_SESSION['badgeid'];
    if (!(may_I("Participant"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };

    function PartRenderError ($title, $message) {
      require_once('ParticipantHeader.php');
      participant_header($title);
      echo "<P id=\"errmsg\">".$message."</P>\n";
      participant_footer();
      }

?>
