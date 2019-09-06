<?php
// Copyright (c) 2009-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
// This file used by email spooler which is no longer used
require_once('data_functions.php');
require_once('db_functions.php');
require_once('validation_functions.php');
// inclusion of configuration file db_name.php occurs here
if (prepare_db_and_more() === false) {
    $message_error="Unable to connect to database.<br />No further execution possible.";
    RenderError($message_error);
    exit();
};
?>
