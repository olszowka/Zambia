<?php
    $title="Staff Grid";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');

    /* Global Variables */
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $_SESSION['return_to_page']="everythinggridcolorreport.php";

    /* Function to start the page correctly. */    
    function topofpage() {
        staff_header($title);
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Grid of all sessions (including DO-NOT-PUB and STAFF-ONLY)</P>\n";
        }

    /* No matching retuned values. */
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        staff_footer();
        }

    /* This query returns the room names for an array. */    
    $query = <<<EOD
SELECT
        R.roomname,
        R.roomid
    FROM
            Rooms R
    WHERE
        R.roomid in
        (SELECT DISTINCT roomid FROM Schedule)
    ORDER BY
    	  R.display_order;
EOD;

    /* Standard test for failing to connect to the database. */
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	$message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }

    /* Standard test to make sure there was some information returned. */
    if (0==($rooms=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }

    /* Associate the information with header_array. */
    for ($i=1; $i<=$rooms; $i++) {
        $header_array[$i]=mysql_fetch_assoc($result);
        }
    $header_cells="<TR><TH>Time</TH>";

    /* This complex query fills in the header_cells and then
       puts the times, associated with each room along the row
       seperated out by color. */
    $query="SELECT DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') as 'starttime'";
    for ($i=1; $i<=$rooms; $i++) {
        $header_cells.="<TH>";
        $header_cells.=sprintf("<A HREF=\"MaintainRoomSched.php?selroom=%s\">%s</A>",$header_array[$i]["roomid"],$header_array[$i]["roomname"]);
        $header_cells.="</TH>";
        $x=$header_array[$i]["roomid"];
        $y=$header_array[$i]["roomname"];
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.title,\"\") SEPARATOR '') as \"%s title\"",$x,$y);
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.sessionid,\"\") SEPARATOR '') as \"%s sessionid\"",$x,$y);
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,S.duration,\"\") SEPARATOR '') as \"%s duration\"",$x,$y);
        $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,T.htmlcellcolor,\"\") SEPARATOR '') as \"%s htmlcellcolor\"",$x,$y);
        }
    $header_cells.="</TR>";
    $query.=" FROM Schedule SCH JOIN Sessions S USING (sessionid)";
    $query.=" JOIN Rooms R USING (roomid) JOIN Types T USING (typeid)";
    $query.=" GROUP BY SCH.starttime ORDER BY SCH.starttime;";
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
        $grid_array[$i]=mysql_fetch_array($result,MYSQL_BOTH);
        } 

    /* Printing body.  Uses the page-init from above adds informational line
       then creates the grid  */
    topofpage();
    echo "<P>Click on the room name to edit the room's schedule; the session id to edit the session's participants; or the title to edit the session.</P>\n";
    echo "<TABLE BORDER=1>";
    echo $header_cells;
    for ($i=1; $i<=$rows; $i++) {
        echo "<TR><TD>";
        echo $grid_array[$i]['starttime'];
        echo "</TD>";
        for ($j=1; $j<=$rooms; $j++) {
            $z=$header_array[$j]['roomname'];
            $y=$grid_array[$i]["$z htmlcellcolor"]; //cell background color
            $x=$grid_array[$i]["$z sessionid"]; //sessionid
            if ($y!="") {
                echo sprintf("<TD BGCOLOR=\"%s\">",$y);
                echo sprintf("(<A HREF=\"StaffAssignParticipants.php?selsess=%s\">%s</A>) ",$x,$x);
                $y = $grid_array[$i]["$z title"]; //title
                echo sprintf("<A HREF=\"EditSession.php?id=%s\">%s</A>",$x,$y);
                $y = substr($grid_array[$i]["$z duration"],0,-3); // duration; drop ":00" representing seconds off the end
                if (substr($y,0,1)=="0") {$y = substr($y,1,999);} // drop leading "0"
                echo sprintf(" (%s)",$y);
                }
            else
                { echo "<TD>&nbsp;"; } 
            echo "</TD>";
            }
        echo "</TR>\n";
        }
    echo "</TABLE>";
    staff_footer();
