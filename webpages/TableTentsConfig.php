<?php
    global $title;
    $title = "Table Tents";
    require_once('StaffCommonCode.php');
    staff_header($title, true);
?>

</div>
<div class="container">

<?php RenderXSLT('TableTentsConfig.xsl', array()); ?>

</div>
<div class="container-fluid">



<?php staff_footer(); ?>