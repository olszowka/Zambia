<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="panelmergecsvreport.php";
    $title="CSV -- Report for Program Panel Merge";
    $description="<P>sessionid,room,start time,duration,track,title,participants</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1, CSVSWANTS=1, GENCSV=0";
    $resultsfile="panelmerge.csv";

    # First query sets the max length, second the actual program description query.
    $query="SET group_concat_max_len=25000";
    if (!$result=mysql_query($query,$link)) {
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        $message.="<P class\"errmsg\">".$query."\n";
	RenderError($title,$message);
        exit();
        }

    $query=<<<EOD
SELECT
    S.sessionid, 
    R.roomname AS room, 
    DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS 'start time', 
    CASE
      WHEN HOUR(S.duration) < 1 THEN
        concat(date_format(S.duration,'%i'),'min')
      WHEN MINUTE(S.duration)=0 THEN
        concat(date_format(S.duration,'%k'),'hr')
      ELSE
        concat(date_format(S.duration,'%k'),'hr ',date_format(S.duration,'%i'),'min')
      END AS duration,
    T.trackname AS track, 
    S.title, 
    group_concat(P.pubsname, if(POS.moderator=1,'(m)','') ORDER BY POS.moderator DESC SEPARATOR ', ') AS participants,
    PUB.pubstatusname AS status
  FROM
      Sessions S
    JOIN Schedule SCH USING(sessionid)
    JOIN Rooms R USING(roomid)
    JOIN Tracks T USING(trackid)
    JOIN PubStatuses PUB USING(pubstatusid)
    LEFT JOIN ParticipantOnSession POS USING(sessionid)
    LEFT JOIN Participants P USING(badgeid)
  GROUP BY
    S.sessionid
  ORDER BY
    SCH.starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=querycsvreport($query,$link);

    ## Page rendering
    topofpagecsv($resultsfile);
    rendercsvreport($headers,$rows,$header_array,$class_array);

?>
