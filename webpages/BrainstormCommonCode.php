<?php
    require_once('data_functions.php');
    require_once('render_functions.php');
    session_start();
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
    if (!(may_I("Brainstorm"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };
    function BrainstormRenderError ($title, $message) {
      require_once('BrainstormHeader.php');
      brainstorm_header($title);
      echo "<P id=\"errmsg\">".$message."</P>\n";
      brainstorm_footer();
      }
?>
