<?php
    require_once('PostingCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $Grid_Spacer=GRID_SPACER; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="Bios.php";
    $title="Bios for Presenters";
    $description="<P>List of all Presenters biographical information.</P>\n";
    $additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.php\">description</A>,\n";
    $additionalinfo.="the time to visit the <A HREF=\"Schedule.php\">timeslot</A>, the track name to visit the particular\n";
    $additionalinfo.="<A HREF=\"Tracks.php\">track</A>, or visit the <A HREF=\"Postgrid.php\">grid</A>.</P>\n";
    $additionalinfo.="<P>To get an iCal calendar of all the classes of this Presenter, click on the <H6>(Fan iCal)</H6> after their Bio entry.</P>";

    /* This complex query grabs the name, class information, and editedbio (if there is one)
       Most, if not all of the formatting is done within the query, as opposed to in
       the post-processing. */
    $query = <<<EOD
SELECT
    concat('<A NAME=\"',P.pubsname,'\"></A>',P.pubsname) as 'Participants',
    GROUP_CONCAT(DISTINCT concat('<DT><A HREF=\"Descriptions.php#',S.sessionid,'\">',S.title,'</A>',
       if((moderator=1),'(m)',''), ' &mdash; ',
       '<A HREF=\"Tracks.php#',T.trackname,'\">',T.trackname,'</A> &mdash; ',
       concat('<A HREF=\"Schedule.php#',
              DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',
              DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>'), ' &mdash; ',
       CASE 
         WHEN HOUR(duration) < 1 THEN
           concat(date_format(duration,'%i'),'min')
         WHEN MINUTE(duration)=0 THEN
           concat(date_format(duration,'%k'),'hr')
         ELSE
           concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')
         END,'</DT>') SEPARATOR ' ') as Title,
    if ((P.editedbio is NULL),' ',P.editedbio) as Bio,
    P.pubsname
  FROM
      Sessions S
    JOIN Schedule SCH USING (sessionid)
    JOIN Rooms R USING (roomid)
    JOIN Tracks T USING (trackid)
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    P.badgeid AND
    POS.volunteer=0 AND
    POS.introducer=0 AND
    POS.aidedecamp=0
  GROUP BY
    Participants
  ORDER BY
    P.pubsname
EOD;

    ## Retrieve query
    list($elements,$header_array,$element_array)=queryreport($query,$link,$title,$description,0);

    /* Printing body.  Uses the page-init then creates the bio page. */
    topofpagereport($title,$description,$additionalinfo);
    for ($i=1; $i<=$elements; $i++) {
      $picture=sprintf("../Local/Participant_Images/%s.jpg",$element_array[$i]['pubsname']);
      if (file_exists($picture)) {
	echo "<TABLE>\n<TR>\n<TD width=310>";
	echo sprintf("<img width=300 src=\"%s\"</TD>\n<TD>",$picture);
      } else {
	echo "<TABLE>\n<TR>\n<TD>";
      }
      echo sprintf("<P><B>%s</B> ",$element_array[$i]['Participants']);
      if ($element_array[$i]['Bio'] != ' ') {
	echo sprintf("%s",$element_array[$i]['Bio']);
      }
      echo sprintf("\n<DL>\n  <i>%s</i>\n</DL></P>\n",$element_array[$i]['Title']);
      echo sprintf("\n<P><H6><A HREF=\"PostScheduleIcal.php?pubsname=%s\">(Fan iCal)</A></H6></P>",$element_array[$i]['pubsname']);
      echo "</TD>\n</TR>\n</TABLE>\n";
    }
    posting_footer();

