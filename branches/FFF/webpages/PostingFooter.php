<?php
    function posting_footer() {
?>

<hr>
<p> 
If you have questions or wish to communicate an idea, please contact 
<?php
    echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> ";
    include ('google_analytics.php');
?>
</body>
</html>

<?php } ?>
