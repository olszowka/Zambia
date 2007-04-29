<?php
    require_once('CommonCode.php');
    require_once('StaffFooter.php');
    $badgeid=$_SESSION['badgeid'];
    if (!(may_I("Staff"))) {
        $message="You are not authorized to access this page.";
        require ('login.php');
        exit();
        };

    function StaffRenderError ($title, $message) {
      require_once('StaffHeader.php');
      staff_header($title);
      echo "<P id=\"errmsg\">".$message."</P>\n";
      staff_footer();
      }
?>
