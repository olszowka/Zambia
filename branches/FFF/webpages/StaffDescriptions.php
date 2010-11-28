<?php
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="StaffDescriptions.php";
    $title="Session Descriptions";
    $description="<P>Descriptions for all sessions.</P>\n";
    $additionalinfo="<P>Click on the time to visit the session's <A HREF=\"StaffSchedule.php\">timeslot</A>,\n";
    $additionalinfo.="the presenter to visit their <A HREF=\"StaffBios.php\">bio</A>, or visit the\n";
    $additionalinfo.="<A HREF=\"grid.php?standard=y&unpublished=y\">grid</A>.</P>\n";
    $indicies="PROGWANTS=1, GRIDSWANTS=1";
    $Grid_Spacer=GRID_SPACER;

    /* This query grabs everything necessary for the descriptions to be printed. */
    $query = <<<EOD
SELECT
    if ((P.pubsname is NULL), ' ', GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffBios.php#',P.pubsname,'\">',P.pubsname,'</A>',if((moderator=1),'(m)','')) SEPARATOR ', ')) as 'Participants',
    GROUP_CONCAT(DISTINCT concat('<A HREF=\"StaffSchedule.php#',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>') SEPARATOR ', ') as 'Start Time',
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
    concat('<A NAME=\"',S.sessionid,'\"></A>',S.title) as Title,
    S.secondtitle AS Subtitle,
    concat('<P>Web: ',S.progguiddesc,'</P>') as 'Web Description',
    concat('<P>Book: ',S.pocketprogtext,'</P>') as 'Book Description'
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
    S.title
EOD;

    ## Retrieve query
    list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

    /* Printing body.  Uses the page-init then creates the Descriptions. */
    topofpagereport($title,$description,$additionalinfo);
    echo "<DL>\n";
    for ($i=1; $i<=$elements; $i++) {
      echo sprintf("<P><DT><B>%s</B>",$element_array[$i]['Title']);
      if ($element_array[$i]['Subtitle']) {
	echo sprintf("&mdash; %s",$element_array[$i]['Subtitle']);
      }
      if ($element_array[$i]['Start Time']) {
	echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Start Time']);
      }
      if ($element_array[$i]['Duration']) {
	echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Duration']);
      }
      if ($element_array[$i]['Roomname']) {
	echo sprintf("&mdash; <i>%s</i>",$element_array[$i]['Roomname']);
      }
      echo sprintf("</DT>\n<DD>%s",$element_array[$i]['Web Description']);
      echo sprintf("</DT>\n<DD>%s",$element_array[$i]['Book Description']);
      if ($element_array[$i]['Participants']) {
	echo sprintf("<i>%s</i>",$element_array[$i]['Participants']);
      }
      echo "</DD></P>\n";
    }
    echo "</DL>\n";
    staff_footer();
