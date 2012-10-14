<?php
    require_once ('db_functions.php');
    require_once('BrainstormCommonCode.php');
    $title="Likely to Occur Suggestions";
    $ConStartDatim = CON_START_DATIM;
    $showlinks=$_GET["showlinks"];
    global $message_error;
    $_SESSION['return_to_page']="ViewPrecis.php?showlinks=$showlinks";
    if ($showlinks=="1") {
            $showlinks=true;
            }
    elseif ($showlinks="0") {
            $showlinks=false;
            }
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        RenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
SELECT
        sessionid, trackname, null typename, title,
        CONCAT( IF(LEFT(duration,2)=00, '',
                IF(LEFT(duration,1)=0, CONCAT(RIGHT(LEFT(duration,2),1),'hr '), CONCAT(LEFT(duration,2),'hr '))),
                IF(DATE_FORMAT(duration,'%i')=00, '',
                IF(LEFT(DATE_FORMAT(duration,'%i'),1)=0, CONCAT(RIGHT(DATE_FORMAT(duration,'%i'),1),'min'),
            CONCAT(DATE_FORMAT(duration,'%i'),'min')))) Duration,
        estatten, progguiddesc, persppartinfo, roomname,
        DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
                  Sessions S
             JOIN Tracks TR USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
        LEFT JOIN Schedule SCH USING (sessionid)
        LEFT JOIN Rooms R USING (roomid)
    WHERE
            SS.statusname IN ('Vetted','Assigned','Scheduled')
        AND S.invitedguest=0;
EOD;
    if (!$result = mysql_query_with_error_handling($query,$link)) {
        $message="Error retrieving data from database." . $message_error;
        RenderError($title,$message);
        exit ();
        }
    brainstorm_header($title);
    echo "<p> These ideas have made the first cut.  We like them and would like to see them happen.   Now to just find all the right people... ";
    echo "<p> If you want to help, email us at "; 
    echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> </p>\n";
    echo "This list is sorted by Track and then Title." ;
    RenderPrecis($result,$showlinks);
    brainstorm_footer();
    exit();
?> 

