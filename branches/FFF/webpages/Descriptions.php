<?php
    require_once('db_functions.php');
    require_once('PostingHeader.php');
    require_once('PostingFooter.php');
    require_once('CommonCode.php');
    require_once('error_functions.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="Descriptions.html";
    $_SESSION['return_to_page']="aDescriptionsreport.php";
    $title="Session Descriptions";
    $description="<P>Descriptions for all sessions.</P>\n";
    $additionalinfo="<P>Click on the time to visit the session's <A HREF=\"Schedule.html\">timeslot</A>,\n";
    $additionalinfo.="the presenter to visit their <A HREF=\"Bios.html\">bio</A>, or visit the\n";
    $additionalinfo.="<A HREF=\"Postgrid.html\">grid</A>.</P>\n";
    $indicies="PROGWANTS=1, GRIDSWANTS=1";
    $Grid_Spacer=GRID_SPACER;
    $oldrole=$_SESSION['role'];
    $_SESSION['role']="Posting";

    /* This query grabs everything necessary for the schedule to be printed. */
    $query="SELECT if ((P.pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"Bios.html#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',";
    $query.=" concat('<A HREF=\"Schedule.html#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') as 'Start Time',";
    $query.=" CASE WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min') WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr') ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min') END AS Duration,";
    $query.=" GROUP_CONCAT(DISTINCT R.roomname SEPARATOR ', ') as Roomname,";
    $query.=" S.sessionid as Sessionid,";
    $query.=" concat('<A NAME=\"',S.sessionid,'\"></A>',S.title) as Title,";
    $query.=" concat('<P>',S.progguiddesc,'</P>') as Description";
    $query.=" FROM Sessions S";
    $query.=" JOIN Schedule SCH USING (sessionid)";
    $query.=" JOIN Rooms R USING (roomid)";
    $query.=" LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid";
    $query.=" LEFT JOIN Participants P ON POS.badgeid=P.badgeid";
    $query.=" WHERE S.pubstatusid = 2 AND POS.volunteer=0 AND POS.announcer=0";
    $query.=" GROUP BY sessionid ORDER BY S.title;";

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
    if (0==($elements=mysql_num_rows($result))) {
        $message="<P>This report retrieved no results matching the criteria.</P>\n";
        RenderError($title,$message);
        exit();
        }

    /* Associate the information with header_array. */
    for ($i=1; $i<=$elements; $i++) {
        $element_array[$i]=mysql_fetch_assoc($result);
        }

    /* Printing body.  Uses the page-init from above adds informational line
       then creates the Descriptions. */
    topofpagereport($title,$description,$additionalinfo);
    $_SESSION['role']=$oldrole;
    echo "<DL>\n";
    for ($i=1; $i<=$elements; $i++) {
      echo sprintf("<P><DT><B>%s</B>",$element_array[$i]['Title']);
      if ($element_array[$i]['Start Time']) {
	echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Start Time']);
      }
      if ($element_array[$i]['Duration']) {
	echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Duration']);
      }
      if ($element_array[$i]['Roomname']) {
	echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Roomname']);
      }
      echo sprintf("</DT>\n<DD>%s",$element_array[$i]['Description']);
      if ($element_array[$i]['Participants']) {
	echo sprintf("<i>%s</i>",$element_array[$i]['Participants']);
      }
      echo "</DD></P>\n";
    }
    echo "</DL>\n";
    posting_footer();
