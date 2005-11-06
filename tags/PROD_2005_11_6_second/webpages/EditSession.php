<?php
    require ('db_functions.php');
    require ('data_functions.php');
    require_once('StaffCommonCode.php');
    require ('RenderEditCreateSession.php');
    $id=$_GET["id"];
    if ($id=="") {
	$message="The id parameter is required.";
        report_error();
        exit();
        }
    $id=intval($id);
    if ($id<1) {
        $message="The id parameter must be a value row index.";
        report_error();
        exit();
        }
    prepare_db();
    $status=retrieve_session_from_db($id);
    if ($status==-3) {
        $message="Error retrieving record from database.";
        report_error();
        exit();
        }
    if ($status==-2) {
        $message="Session record with id=".$id." not found (or error with Session primary key).";
        report_error();
        exit();
        }
    $message_error="";
    $message_warn="";
    $action="edit";
    RenderEditCreateSession($action,$session,$message_warn,$message_error);
    exit();
?>
