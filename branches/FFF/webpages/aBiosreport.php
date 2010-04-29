<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="aBiosreport.php";
    $title="Bios for Presenters";
    $description="<P>List of all Presenters biographical information.</P>\n";
    $additionalinfo="<P>Click on the session title to visit the session's <A HREF=\"Descriptions.html\">description</A>,\n";
    $additionalinfo.="the time to visit the <A HREF=\"Schedule.html\">timeslot</A>, or visit the\n";
    $additionalinfo.="<A HREF=\"Postgrid.html\">grid</A>.</P>\n";
    $indicies="PROGWANTS=1, GRIDSWANTS=1";
    $Grid_Spacer=GRID_SPACER;

    /* This complex query grabs the name, class information, and editedbio (if there is one)
       Most, if not all of the formatting is done within the query, as opposed to in
       the post-processing. */
    $query = <<<EOD
SELECT
    concat('<A NAME=\"',P.pubsname,'\"></A>',P.pubsname) as 'Participants',
    GROUP_CONCAT(DISTINCT concat('<DT><A HREF=\"aDescriptionsreport.php#',S.sessionid,'\">',S.title,'</A>',
    if((moderator=1),'(m)',''), ' &mdash; ',
       concat('<A HREF=\"aSchedulereport.php#',
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
    LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
    LEFT JOIN Participants P ON POS.badgeid=P.badgeid
  WHERE
    P.badgeid AND
    POS.volunteer=0 AND
    POS.announcer=0
  GROUP BY
    Participants
  ORDER BY
    P.pubsname
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
       then creates the bio page. */
    topofpagereport($title,$description,$additionalinfo);
    for ($i=1; $i<=$elements; $i++) {
      $picture=sprintf("Participant_Images/%s.jpg",$element_array[$i]['pubsname']);
      if (file_exists($picture)) {
	echo "<TABLE>\n<TR>\n<TD width=310>";
	echo sprintf("<img width=300 src=\"%s\"</TD>\n<TD>",$picture);
      }
      echo sprintf("<P><B>%s</B> ",$element_array[$i]['Participants']);
      if ($element_array[$i]['Bio'] != ' ') {
	echo sprintf("%s",$element_array[$i]['Bio']);
      }
      echo sprintf("\n<DL>\n  <i>%s</i>\n</DL></P>\n",$element_array[$i]['Title']);
      if (file_exists($picture)) {
	echo "</TD>\n</TR>\n</TABLE>\n";
      }
    }
    staff_footer();

