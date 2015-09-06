<?php
    require_once('data_functions.php');
    require_once('db_functions.php');
    require_once('validation_functions.php');
    require_once('php_functions.php');
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($title,$message_error);
        exit();
        };
?>
