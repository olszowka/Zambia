<?php
    function participant_footer() {
?>

<hr/>
<p/> 
If you need help or to tell us something that doesn't fit here, please email
<?php
   $x=PROGRAM_EMAIL;
   echo "<a href=\"mailto:$x\">$x</a>.\n"; 
   include('google_analytics.php');
?>
</body>
</html>

<?php } ?>
