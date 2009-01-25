<?php
    function brainstorm_footer() {
?>

<hr>
<p> 
If you would like assistance using this tool, please contact
<?php echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> "?>. 
If you would like to communicate an idea that you cannot fit into this form, please contact 
<?php echo "<a href=\"mailto:".BRAINSTORM_EMAIL."\">".BRAINSTORM_EMAIL."</a> "?>.

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
