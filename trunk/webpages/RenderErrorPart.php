<?php
    function RenderError ($title, $message) {
	participant_header($title);
?>

<P class="errmsg">
<?php
        echo $message;
?>
    </P>
</BODY>
</HTML>
<?php
    }
?>
