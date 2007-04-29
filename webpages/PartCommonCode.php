<?php
    require_once('data_functions.php');
    //session_start();
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($message_error);
        exit();
        };
    $firsttime=true;
    if (isLoggedIn($firsttime)===false) {
        $message="Session expired. Please log in again.";
        require ('login.php');
        exit();
        };
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
