<?php
    // Used by email spooler which is no longer used
    require_once('data_functions.php');
    require_once('db_functions.php');
    require_once('validation_functions.php');
    // require_once('php_functions.php'); For setting session timeout which doesn't seem to work
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($message_error);
        exit();
        };
?>
