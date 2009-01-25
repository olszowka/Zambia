<?php
    function participant_footer() {
?>

<hr>
<p> 
If you need help or to tell us something that doesn't fit here, please email <a href="mailto: <?php echo PROGRAM_EMAIL."\">".PROGRAM_EMAIL ?></a>.
<?php
// This line is for debugging only
// echo "<PRE>".print_r($_SESSION['permission_set_specific'])."</PRE><BR>";
?>

<!---  Google Analytics Tracking Report, see Ben for details --->

<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-1841041-6");
pageTracker._initData();
pageTracker._trackPageview();
</script>
<!--- End of Google Analytics --->

</body>
</html>

<?php } ?>
