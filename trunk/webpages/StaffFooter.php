<?php
    function staff_footer() {
?>
<div xxclass="container-fluid">
<hr>
<p> 
If you would like assistance using this tool or you would like to communicate an idea that you cannot fit into this form, please contact 
<?php
    echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> ";
    include ('google_analytics.php');
?>
</div>
</body>
</html>

<?php } ?>
