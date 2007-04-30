<?php
    require_once('data_functions.php');
    require_once('db_functions.php');
    require_once('render_functions.php');
    require_once('validation_functions.php');
    session_start();
    if (prepare_db()===false) {
        $message_error="Unable to connect to database.<BR>No further execution possible.";
        RenderError($title,$message_error);
        exit();
        };
    $firsttime=true;
    if (isLoggedIn($firsttime)===false) {
        $message="Session expired. Please log in again.";
        require ('login.php');
        exit();
        };
?>
