<?php
    require_once('CommonCode.php');
    require_once('BrainstormHeader.php');
    require_once('BrainstormFooter.php');
    $badgeid=$_SESSION['badgeid'];
    $_SESSION['role']="Brainstorm";
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
