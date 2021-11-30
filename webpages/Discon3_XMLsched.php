<?php
    require_once('Discon3_XMLsched_func.php');

    if (isset($_GET['type'])) {
        if ($_GET['type'] == 's') {
            $results = retrieveD3XMLDataSched();
        }
        if ($_GET['type'] == 'p') {
            $results = retrieveD3XMLDataParticipants();
        }
        if ($_GET['type'] == 'ps') {
            $results = retrieveD3XMLDataPocketProgram();
        }
    }
	exit();
?>