<?php
    $title="Categorized Session Count Report";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="partsesscategorycountreport.php";

    function topofpage() {
        staff_header($title);
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Show count of how many sessions each participant is scheduled for broken down by division</P>\n";
        }

    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    $query = <<<EOD
SELECT
        P.pubsname,
        P.badgeid,
        CD.regtype,
        SUM(IF(S.divisionid=2,1,0)) AS program,
        SUM(IF(S.divisionid=3,1,0)) AS events,
        SUM(IF(S.divisionid!=2 AND S.divisionid!=3,1,0)) AS other
    FROM
        Participants P
   JOIN CongoDump CD USING (badgeid)
   JOIN ParticipantOnSession POS USING (badgeid)
   JOIN Sessions S USING (sessionid) 
   JOIN Schedule SCH USING (sessionid)
    GROUP BY
        P.badgeid
    ORDER BY
        CD.regtype, program desc;
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	    $message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }
    if (0==($rows=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }
    for ($i=1; $i<=$rows; $i++) {
        $grid_array[$i]=mysql_fetch_array($result,MYSQL_ASSOC);
        } 
    topofpage();
    //echo "<BR>debug1:<BR>\n";
    //print_r($grid_array[1]);
    //echo "<BR>debug2:<BR>\n";
	//print_r($grid_array[1]['sessionid']);
    //echo "<BR>\n";
    //echo "<P>Click on the room name to edit the room's schedule; the session id to edit the session's participants; or the title to edit the session.</P>\n";
    echo "<TABLE BORDER=1>";
    echo "<COL><COL><COL><COL width=\"15%\"><COL width=\"15%\"><COL width=\"15%\">";
    echo "<TR><TH>Badge ID</TH><TH>Publication Name</TH><TH>Registration Type</TH><TH>Number of Programming Sessions</TH>";
    echo "<TH>Number of Events Sessions</TH><TH>Number of other Sessions</TH></TR>\n";
    for ($i=1; $i<=$rows; $i++) {
        echo "<TR>";
        echo "<TD>{$grid_array[$i]['badgeid']}</TD>";
        echo "<TD>{$grid_array[$i]['pubsname']}</TD>";
        echo "<TD>{$grid_array[$i]['regtype']}</TD>";
        echo "<TD>{$grid_array[$i]['program']}</TD>";
        echo "<TD>{$grid_array[$i]['events']}</TD>";
        echo "<TD>{$grid_array[$i]['other']}</TD>"; 
        echo "</TR>\n";
        }
    echo "</TABLE>";
    staff_footer();

