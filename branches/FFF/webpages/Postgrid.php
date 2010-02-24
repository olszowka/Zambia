<?php
    require_once('db_functions.php');
    require_once('PostingHeader.php');
    require_once('PostingFooter.php');
    require_once('CommonCode.php');
    require_once('error_functions.php');

    /* Global Variables */
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $Grid_Spacer=(60 * 30); // space grid sections by 60 seconds per minute and 30 minutes
    $_SESSION['return_to_page']="Postgrid.html";

    /* Function to start the page correctly. */    
    function topofpage() {
        posting_header("Published Grid");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>Grid of all sessions.</P>\n";
        }

    /* No matching retuned values. */
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        posting_footer();
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
      (SELECT DISTINCT SCH.roomid FROM Schedule SCH JOIN Sessions S USING (sessionid) where pubstatusid=2)
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
    for ($i=1; $i<=$rooms; $i++) {
        $header_cells.="<TH>";
        $header_cells.=$header_array[$i]["roomname"];
        $header_cells.="</TH>";
        }
    $header_cells.="</TR>";

    /* This set of queries finds the appropriate presenters for a class,
       based on sessionid, and produces links for them. */
    $query = <<<EOD
SELECT
      S.sessionid,
      GROUP_CONCAT(concat("<A HREF=\"Bios.html#",P.pubsname,"\">",P.pubsname,"</A>") SEPARATOR ", ") as allpubsnames
    FROM
      Sessions S
    JOIN
      ParticipantOnSession POS USING (sessionid)
    JOIN
      Participants P USING (badgeid)
    WHERE 
      POS.volunteer=0 AND POS.announcer=0
    GROUP BY
      sessionid
    ORDER BY
      sessionid;
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
        $message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }
    if (0==($presenters=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }
    $tmp_array=('');
    for ($i=1; $i<=$presenters; $i++) {
        $tmp_array[$i]=mysql_fetch_assoc($result);
        } 
    for ($i=1; $i<=$presenters; $i++) {
        $presenters_array[$tmp_array[$i]['sessionid']]=$tmp_array[$i]['allpubsnames'];
        } 

    /* This query finds the first second that is actually scheduled
       so we don't waste grid-space. */
    $query="SELECT TIME_TO_SEC(starttime) as 'beginschedule' FROM Schedule ORDER BY starttime ASC LIMIT 0,1";
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	$message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }
    if (0==($earliest=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }
    $grid_start_sec=mysql_result($result,0);

    $query="SELECT (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) as 'endschedule' FROM Schedule SCH JOIN Sessions S USING (sessionid) ORDER BY endschedule DESC LIMIT 0,1";
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.<BR>";
        $message.=$query;
        $message.="<BR>";
	$message.= mysql_error();
        RenderError($title,$message);
        exit ();
        }
    if (0==($latest=mysql_num_rows($result))) {
        topofpage();
        noresults();
        exit();
        }
    $grid_end_sec=mysql_result($result,0);

    /* This complex set of queries fills in the header_cells and then
       puts the times, associated with each room along the row
       seperated out by color, by stepping along in time intervals */
    for ($time=$grid_start_sec; $time<=$grid_end_sec; $time = $time + $Grid_Spacer) {
        $query="SELECT DATE_FORMAT(ADDTIME('$ConStartDatim',SEC_TO_TIME('$time')),'%a %l:%i %p') as 'blocktime'";
        for ($i=1; $i<=$rooms; $i++) {
            $x=$header_array[$i]["roomid"];
            $y=$header_array[$i]["roomname"];
            $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.title,\"\") SEPARATOR '') as \"%s title\"",$x,$y);
            $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.sessionid,\"\") SEPARATOR '') as \"%s sessionid\"",$x,$y);
            $query.=sprintf(",GROUP_CONCAT(IF((roomid=%s AND ($time = TIME_TO_SEC(SCH.starttime))),S.duration,\"\") SEPARATOR '') as \"%s duration\"",$x,$y);
            $query.=sprintf(",GROUP_CONCAT(IF(roomid=%s,T.htmlcellcolor,\"\") SEPARATOR '') as \"%s htmlcellcolor\"",$x,$y);
            }
        $query.=" FROM Schedule SCH JOIN Sessions S USING (sessionid)";
        $query.=" JOIN Rooms R USING (roomid) JOIN Types T USING (typeid)";
        $query.=" WHERE S.pubstatusid = 2 AND TIME_TO_SEC(SCH.starttime) <= $time";
        $query.=" AND (TIME_TO_SEC(SCH.starttime) + TIME_TO_SEC(S.duration)) >= ($time + $Grid_Spacer);";
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
        $grid_array[$time]=mysql_fetch_array($result,MYSQL_BOTH);
        $skiprow=0;
        $refskiprow=0;
        for ($i=1; $i<=$rooms; $i++) {
            $j=$header_array[$i]['roomname'];
            if ($grid_array[$time]["$j htmlcellcolor"]!="") {$skiprow++;}
            if ($grid_array[$time]["$j sessionid"]!="") {$refskiprow++;}
	    }
        if ($skiprow == 0) {$grid_array[$time]['blocktime'] = "Skip";}
        if ($refskiprow != 0) {
            $k=$grid_array[$time]['blocktime'];
            $grid_array[$time]['blocktime']=sprintf("<A HREF=\"Schedule.html#%s\">%s</A>",$k,$k);
            }
        }

    /* Printing body.  Uses the page-init from above adds informational line
       then creates the grid.  $skipinit kills the rogue extra /TABLE and
       $skipaccum allows for only one new tabel per set of skips.  The extra
       ifs keep the parens out of the otherwise empty blocks.  We switch on
       htmlcellcolor, because, by design, that is the only thing written in
       a continuation block. */
    topofpage();
    echo "<P>Click on the session title to visit the session's <A HREF=\"Descriptions.html\">description</A>,";
    echo " the presenter to visit their <A HREF=\"Bios.html\">bio</A>, or the time to visit that section of";
    echo " the <A HREF=\"Schedule.html\">schedule</A>.</P>\n";
    $skipinit=0;
    $skipaccum=1;
    $Grid_Spacer=(60 * 15);
    for ($i = $grid_start_sec; $i < $grid_end_sec; $i = ($i + $Grid_Spacer)) {
        if ($i == 70200) {$Grid_Spacer =(60 * 30);}
        if ($skipaccum == 1) { 
            if ($skipinit != 0) {echo "</TABLE>\n";} else {$skipinit++;}
            echo "<TABLE BORDER=1>";
            echo $header_cells;
	    }
	if ($grid_array[$i]['blocktime'] == "Skip") {
            $skipaccum++;
            } else {
            echo sprintf("<TR><TD>%s</TD>\n",$grid_array[$i]['blocktime']);
            for ($j=1; $j<=$rooms; $j++) {
                $z=$header_array[$j]['roomname'];
                $y=$grid_array[$i]["$z htmlcellcolor"]; //cell background color
                $x=$grid_array[$i]["$z sessionid"]; //sessionid
                if ($y!="") {
                    echo sprintf("<TD BGCOLOR=\"%s\">",$y);
                    $y = $grid_array[$i]["$z title"]; //title
                    if ($y!="") {
                        echo sprintf("<A HREF=\"Descriptions.html#%s\">%s</A>",$x,$y);
                        }
                    $y = substr($grid_array[$i]["$z duration"],0,-3); // duration; drop ":00" representing seconds off the end
                    if (substr($y,0,1)=="0") {$y = substr($y,1,999);} // drop leading "0"
                    if ($y!="") {
                        echo sprintf(" (%s)",$y);
                        }
                    $y=$presenters_array[$x]; //presenters
                    if ($y!="") {
                        echo sprintf("<br>\n%s",$y);
                        }
                    }
                else
                    { echo "<TD>&nbsp;"; } 
                echo "</TD>\n";
                }
                echo "</TR>\n";
                $skipaccum=0;
            }
        }
    echo "</TABLE>";
    posting_footer();
