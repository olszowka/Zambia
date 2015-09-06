<?php
    function brainstorm_footer() {
?>

<hr>
<p> 
If you would like assistance using this tool, please contact
<?php echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> "?>. 
If you would like to communicate an idea that you cannot fit into this form, please contact 
<?php
    echo "<a href=\"mailto:".BRAINSTORM_EMAIL."\">".BRAINSTORM_EMAIL."</a>.";
    include('google_analytics.php');
?>
</body>
</html>
<?php  } ?>
