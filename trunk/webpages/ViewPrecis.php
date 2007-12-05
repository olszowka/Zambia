<?php
    require_once ('db_functions.php');
    require_once('StaffCommonCode.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once ('render_functions.php');
    require_once ('retrieve.php');
    $title=CON_NAME . " - Precis";
    $showlinks=$_GET["showlinks"];
    $_SESSION['return_to_page']="ViewPrecis.php?showlinks=$showlinks";
    if ($showlinks=="1") {
            $showlinks=true;
            }
    elseif ($showlinks="0") {
            $showlinks=false;
            }
   $statusname="'Brainstorm','EditMe','Vetted'";
   $type=0;
   $track=0;
   $status=0;
   $sessionid="";
    if (retrieve_select_from_db($track,$status,$statusname,$type,$sessionid)==0) {
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
