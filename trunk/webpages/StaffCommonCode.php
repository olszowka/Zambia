<?php
    require_once('data_functions.php');
    session_start();
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($title, $message_error);
        exit();
        };
    $firsttime=true;
    if (isLoggedIn($firsttime)===false) {
        $message="Session expired. Please log in again.";
        require ('login.php');
        exit();
        };
    $badgeid=$_SESSION['badgeid'];
    if (!(may_I("Staff"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };

// StaffRenderError does X
// Requires Y
// Causes Z (may be nothing)

    function StaffRenderError ($title, $message) {
      require_once('StaffHeader.php');
      staff_header($title);
      echo "<P id=\"errmsg\">".$message."</P>\n";
      staff_footer();
      }

?>
