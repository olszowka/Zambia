<?php
    require_once ('db_functions.php');
    require_once('StaffCommonCode.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once ('render_functions.php');
    require_once ('retrieve.php');
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    $title=CON_NAME . " - Precis";
    $showlinks=$_GET["showlinks"];
    $_SESSION['return_to_page']="ViewPrecis.php?showlinks=$showlinks";
    if ($showlinks=="1") {
            $showlinks=true;
            }
    elseif ($showlinks="0") {
            $showlinks=false;
            }
   $statusidlist=get_idlist_from_db("$ReportDB.SessionStatuses","statusid","statusname","'Brainstorm','Edit Me','Vetted'");
   $typeidlist="";
   $trackidlist="";
   $sessionid="";
    if (retrieve_select_from_db($trackdlist,$statusidlist,$typeidlist,$sessionid)==0) {
       staff_header($title);
       echo "<p> If you have any questions please contact ";
       echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> </p>\n";
       RenderPrecis($result,$showlinks);
       staff_footer();
       exit();
       }
    $message_error="Error retrieving from database. ".$message2;
    RenderError($title,$message_error);
?> 
