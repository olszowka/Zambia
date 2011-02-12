<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="StaffPostvolgrid.php";
    $title="Volunteer Grid";
    $description="<P>For the volunteer coordinators, with links between the classes, the schedule, and their descriptions. Volunteer, Introducer, and Assistants listed.</P>\n";
    $additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"StaffDescriptions.php\">description</A>,\n";
    $additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php\">bio</A>, or the time to visit that section of\n";
    $additionalinfo.="the <A HREF=\"StaffSchedule.php\">schedule</A>. Look at the standard <A HREF=\"StaffPostgrid.php\">grid</A>.</P>\n";
    $indicies="PROGWANTS=1, GRIDSWANTS=1";
    $Grid_Spacer=GRID_SPACER;

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


    ## Retrieve query
    list($rooms,$unneeded_array_a,$header_array)=queryreport($query,$link,$title,$description,0);

    ## Set up the header cells
    $header_cells="<TR><TH>Time</TH>";
    for ($i=1; $i<=$rooms; $i++) {
        $header_cells.="<TH>";
        $header_cells.=sprintf("<A HREF=\"MaintainRoomSched.php?selroom=%s\">%s</A>",$header_array[$i]["roomid"],$header_array[$i]["roomname"]);
        $header_cells.="</TH>";
        }
    $header_cells.="</TR>";

    /* This set of queries finds the appropriate presenters for a class,
       based on sessionid, and produces links for them. */
    $query = <<<EOD
SELECT
      S.sessionid,
      GROUP_CONCAT(IF((POS.volunteer=1 OR POS.introducer=1 OR POS.aidedecamp=1),concat(P.pubsname,", "),"") SEPARATOR "") as allpubsnames
    FROM
      Sessions S
    JOIN
      ParticipantOnSession POS USING (sessionid)
    JOIN
      Participants P USING (badgeid)
    GROUP BY
      sessionid
    ORDER BY
      sessionid;
EOD;
    ## Retrieve query
    list($presenters,$unneeded_array_b,$presenters_tmp_array)=queryreport($query,$link,$title,$description,0);

    for ($i=1; $i<=$presenters; $i++) {
        $presenters_array[$presenters_tmp_array[$i]['sessionid']]=$presenters_tmp_array[$i]['allpubsnames'];
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
        $message="<P>This report retrieved no results matching the criteria.</P>\n";
        RenderError($title,$message);
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
        $message="<P>This report retrieved no results matching the criteria.</P>\n";
        RenderError($title,$message);
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
        $query.=" WHERE TIME_TO_SEC(SCH.starttime) <= $time";
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
            $message="<P>This report retrieved no results matching the criteria.</P>\n";
            RenderError($title,$message);
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
            $grid_array[$time]['blocktime']=sprintf("<A HREF=\"StaffSchedule.php#%s\">%s</A>",$k,$k);
            }
        }

    /* Printing body.  Uses the page-init then creates the grid.  $skipinit
       kills the rogue extra /TABLE and $skipaccum allows for only one new
       tabel per set of skips.  The extra ifs keep the parens out of the
       otherwise empty blocks.  We switch on htmlcellcolor, because, by
       design, that is the only thing written in a continuation block. */
    topofpagereport($title,$description,$additionalinfo);
    $skipinit=0;
    $skipaccum=1;
    for ($i = $grid_start_sec; $i < $grid_end_sec; $i = ($i + $Grid_Spacer)) {
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
                    if ($x!="") {
                        echo sprintf("(<A HREF=\"StaffAssignParticipants.php?selsess=%s\">%s</A>)",$x,$x);
                        }
                    $y = $grid_array[$i]["$z title"]; //title
                    if ($y!="") {
                        echo sprintf("<A HREF=\"EditSession.php?id=%s\">%s</A>",$x,$y);
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
    staff_footer();
