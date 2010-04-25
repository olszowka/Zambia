<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="palmcsvreport.php";
    $title="CSV -- Report for uploading to PDA's";
    $description="<P>StartTime, Duration, Room, Track, Title, Participants</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1, CSVSWANTS=1, GENCSV=0";
    $resultsfile="PDASchedule.csv";

    $query=<<<EOD
SELECT
            DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a') AS Day,
            DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%l:%i %p') AS 'Start Time',
            left(duration,5) AS Duration,
	    roomname AS 'Room Name',
            trackname as Track,
            Title,
            if(group_concat(pubsname) is NULL,'',group_concat(pubsname SEPARATOR ', ')) as Participants
    FROM
            Rooms R
       JOIN Schedule SCH USING (roomid)
       JOIN Sessions S USING (sessionid)
  LEFT JOIN Tracks T USING (trackid)
  LEFT JOIN ParticipantOnSession POS ON SCH.sessionid=POS.sessionid
  LEFT JOIN Participants P ON POS.badgeid=P.badgeid
    WHERE
            S.pubstatusid = 2
    GROUP BY
            SCH.sessionid
    ORDER BY
            SCH.starttime, R.roomname
EOD;
//SELECT
//        P.badgeid, CD.lastname, CD.firstname,
//	    CD.badgename, P.pubsname, P.bio 
//	FROM
//	    Participants P JOIN
//	    CongoDump CD USING (badgeid) JOIN
//	    (SELECT DISTINCT badgeid 
//	       FROM ParticipantOnSession POS JOIN 
//	            Schedule SCH USING (sessionid)
//	     ) as X
//	   USING (badgeid) 
//	ORDER BY
//	    IF(locate(" ",pubsname)!=0,substring(P.pubsname,char_length(pubsname)-locate(" ",reverse(pubsname))+2),pubsname)
//EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=querycsvreport($query,$link);

    ## Page rendering
    topofpagecsv($resultsfile);
    rendercsvreport($headers,$rows,$header_array,$class_array);

?>
