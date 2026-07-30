<?php
//	Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
    function participant_footer() {
?>
<footer>
<hr />
<p /> 
<p>If you need help or to tell us something that doesn't fit here, please email
<?php
   $x=PROGRAM_EMAIL;
   echo "<a href=\"mailto:$x\">$x</a>.\n"; 
   include('google_analytics.php');
?></p>
</footer><!-- end footer div -->
</div><!-- end whole page div -->
</body>
</html>

<?php } ?>
