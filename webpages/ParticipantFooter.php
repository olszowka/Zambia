<?php
    function participant_footer() {
?>
<div>
<hr/>
<p/> 
<p>If you need help or to tell us something that doesn't fit here, please email
<?php
   $x=PROGRAM_EMAIL;
   echo "<a href=\"mailto:$x\">$x</a>.\n"; 
   include('google_analytics.php');
?></p>
</div><!-- end footer div -->
</div><!-- end whole page div -->
</body>
</html>

<?php } ?>
