<?php
    require_once('Constants.php');
    require_once('data_functions.php');
    require_once('db_functions.php');
    require_once('render_functions.php');
    require_once('validation_functions.php');
    require_once('php_functions.php');
    //set_session_timeout();
    session_start();
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($title,$message_error);
        exit();
        };
    if (isLoggedIn()==false and !isset($logging_in)) {
	    $message="Session expired. Please log in again.";
		if (isset($_GET["ajax_request_action"]) || isset($_POST["ajax_request_action"])) {
			RenderErrorAjax("Session expired. Please <a HREF=\"index.php\">log in</a> again.");
			exit();
			}
	    require ('login.php');
	    exit();
    	};
    if (!populateCustomTextArray()) {
		$message_error="Failed to retrieve custom text. ".$message_error;
        RenderError($title,$message_error);
        exit();
		}

    // function to generate a clickable tab.
    // 'text' contains the text that should appear in the tab.
    // 'usable' indicates whether the tab is usable.
    //
    // if the tab is usable, its background and foreground color will
    // be determined by the 'usabletab' class.  when the mouse is over the tab
    // the background and foreground colors of the tab will be determined
    // by the 'mousedovertab' class.
    //
    // if the tab is not usable, the tab will use class 'unusabletab'
    
    Function maketab($text,$usable,$url) {
	if ($usable) {
		echo '<SPAN class="usabletab" onmouseover="mouseovertab(this)" onmouseout="mouseouttab(this)">';
		echo '<IMG class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
		echo '<A HREF="' . $url . '">' ;// XXX link needs to be quoted
		echo $text;                     // XXX needs to be quoted
		echo '<IMG class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
		echo '</SPAN>';
	    }
	else {
		echo '<SPAN class="unusabletab">';
		echo '<IMG class="tabborder" SRC="images/leftCorner.gif" alt="&nbsp;">';
		echo $text;                     // XXX needs to be quoted
		echo '<IMG class="tabborder" SRC="images/rightCorner.gif" alt="&nbsp;">';
		echo '</SPAN>';
	    }
    }
?>
