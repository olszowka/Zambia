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
    $_SESSION['return_to_page']="Bios.html";

    /* Function to start the page correctly. */    
    function topofpage() {
        posting_header("Presenter Bios");
        date_default_timezone_set('US/Eastern');
        echo "<P align=center> Generated: ".date("D M j G:i:s T Y")."</P>\n";
        echo "<P>List of all biographical information.</P>\n";
        }

    /* No matching retuned values. */
    function noresults() {
        echo "<P>This report retrieved no results matching the criteria.</P>\n";
        posting_footer();
        }

    /* This complex query grabs the name, class information, and editedbio (if there is one)
       Most, if not all of the formatting is done within the query, as opposed to in
       the post-processing. */
    $query="SELECT concat('<A NAME=\"',P.pubsname,'\"></A>',P.pubsname) as 'Participants',";
    $query.=" GROUP_CONCAT(DISTINCT concat('<DT><A HREF=\"Descriptions.html#',S.sessionid,'\">',S.title,'</A>',";
    $query.="   if((moderator=1),'(m)',''),' &mdash; ',concat('<A HREF=\"Schedule.html#',";
    $query.="   DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'\">',";
    $query.="   DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p'),'</A>'),";
    $query.="   ' &mdash; ',CASE WHEN HOUR(duration) < 1 THEN concat(date_format(duration,'%i'),'min')";
    $query.="   WHEN MINUTE(duration)=0 THEN concat(date_format(duration,'%k'),'hr')";
    $query.="   ELSE concat(date_format(duration,'%k'),'hr ',date_format(duration,'%i'),'min')";
    $query.="   END,'</DT>') SEPARATOR ' ') as Title,";
    $query.=" if ((P.editedbio is NULL),' ',P.editedbio) as Bio,";
    $query.=" P.pubsname";
    $query.=" FROM Sessions S";
    $query.=" JOIN Schedule SCH USING (sessionid)";
    $query.=" JOIN Rooms R USING (roomid)";
    $query.=" LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid";
    $query.=" LEFT JOIN Participants P ON POS.badgeid=P.badgeid";
    $query.=" WHERE P.badgeid AND POS.volunteer=0 AND POS.announcer=0";
    $query.=" GROUP BY Participants ORDER BY P.pubsname;";

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
        topofpage();
        noresults();
        exit();
        }

    /* Associate the information with header_array. */
    for ($i=1; $i<=$elements; $i++) {
        $element_array[$i]=mysql_fetch_assoc($result);
        }

    /* Printing body.  Uses the page-init from above adds informational line
       then creates the bio page. */
    topofpage();
    echo "<P>Click on the session title to visit the session's <A HREF=\"Descriptions.html\">description</A>,";
    echo " the time to visit the <A HREF=\"Schedule.html\">timeslot</A>, or visit the";
    echo " <A HREF=\"Postgrid.html\">grid</A>.</P>\n";
    for ($i=1; $i<=$elements; $i++) {
      $picture=sprintf("Participant_Images/%s.jpg",$element_array[$i]['pubsname']);
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
      echo "</TD>\n</TR>\n</TABLE>\n";
    }
    posting_footer();

