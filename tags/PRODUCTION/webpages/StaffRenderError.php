<?php
    function StaffRenderError ($title, $message) {
      require_once('StaffHeader.php');
      staff_header($title);
?>

<P id="errmsg">
<?php
        echo $message;
?>
    </P>
</BODY>
</HTML>
<?php
    }
?>
