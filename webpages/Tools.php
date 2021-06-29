<?php
    global $title;
    $title = "Tools and Utilities";
    require_once('StaffCommonCode.php');
    staff_header($title, true);
?>

</div>
<div class="container">

<?php RenderXSLT('Tools.xsl', array()); ?>

</div>
<div class="container-fluid">



<?php staff_footer(); ?>