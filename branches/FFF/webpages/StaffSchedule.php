<?php
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="StaffSchedule.php";
    $title="Event Schedule";
    $description="<P>Schedule for all sessions.</P>\n";
    $additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"StaffDescriptions.php\">description</A>,\n";
    $additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php\">bio</A>, or visit the\n";
    $additionalinfo.="<A HREF=\"StaffPostgrid.php\">grid</A>.</P>\n";
    $indicies="PROGWANTS=1, GRIDSWANTS=1";
    $Grid_Spacer=GRID_SPACER;

    /* This query grabs everything necessary for the schedule to be printed. */
    if (strtoupper(DOUBLE_SCHEDULE)=="TRUE") {
    $query = <<<EOD
SELECT
    if ((P.pubsname is NULL), ' ', concat('<A HREF=\"StaffBios.php#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)',''))) as 'Participants',
    concat('<A NAME=\"',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\"></A>',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p')) as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    R.roomname as Roomname,
    S.sessionid as Sessionid,
    concat('<A HREF=\"StaffDescriptions.php#',S.sessionid,'\">',S.title,'</A>') as Title,
    concat('<P>',S.progguiddesc,'</P>') as Description
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    S.pubstatusid = 2 AND
    POS.volunteer=0 AND
    POS.introducer=0 AND
    POS.aidedecamp=0
  ORDER BY
    SCH.starttime,
    R.display_order
EOD;
    } else {
    $query = <<<EOD
SELECT
    if ((P.pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffBios.php#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    concat('<A NAME=\"',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\"></A>',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p')) as 'Start Time',
    CASE
      WHEN HOUR(duration) < 1 THEN
        concat(date_format(duration,'%i'),'min')
      WHEN MINUTE(duration)=0 THEN
        concat(date_format(duration,'%k'),'hr')
      ELSE
        concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
      END AS Duration,
    GROUP_CONCAT(DISTINCT R.roomname SEPARATOR ', ') as Roomname,
    S.sessionid as Sessionid,
    concat('<A HREF=\"StaffDescriptions.php#',S.sessionid,'\">',S.title,'</A>') as Title,
    concat('<P>',S.progguiddesc,'</P>') as Description
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    S.pubstatusid = 2 AND
    POS.volunteer=0 AND
    POS.introducer=0 AND
    POS.aidedecamp=0
  GROUP BY
    sessionid
  ORDER BY
    SCH.starttime,
    R.display_order
EOD;
    }
    ## Retrieve query
    list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description);

    /* Printing body.  Uses the page-init then creates the Schedule. */
    topofpagereport($title,$description,$additionalinfo);
    echo "<DL>\n";
    $printtime="";
    for ($i=1; $i<=$elements; $i++) {
      if ($element_array[$i]['Start Time'] != $printtime) {
        $printtime=$element_array[$i]['Start Time'];
	echo sprintf("</DL><P>&nbsp;</P>\n<HR><H3>%s</H3>\n<DL>\n",$printtime);
      }
      echo sprintf("<P><DT><B>%s</B> &mdash; <i>%s</i>",
		   $element_array[$i]['Title'],$element_array[$i]['Duration']);
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
    staff_footer();
