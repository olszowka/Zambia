<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="publongdesccsvreport.php";
    $title="CSV - Session Characteristics plus long description";
    $description="<P>For Scheduled items ONLY. Show sessionid, track, type, divisionid, pubstatusid, pubno, pubchardest, kids, title, long description.</P>\n";
    $additionalinfo="";
    $indicies="PUBSWANTS=1, CSVSWANTS=1, GENCSV=0";
    $resultsfile="longdesc.csv";

    $query=<<<EOD
SELECT
	          S.sessionid,
	          T.trackname AS track,
	          TY.typename AS type,
	          DV.divisionname AS division,
	          PS.pubstatusname AS 'publication status',
	          S.pubsno,
	          group_concat(PC.pubcharname SEPARATOR ' ') AS 'publication characteristics',
	          K.kidscatname AS 'kids category',
	          S.title,
	          S.progguiddesc as description
	FROM
	          Schedule SCH
	     JOIN Sessions S USING(sessionid)
	     JOIN Tracks T USING(trackid)
	     JOIN Types TY USING(typeid)
	     JOIN Divisions DV USING(divisionid)
	     JOIN PubStatuses PS USING(pubstatusid)
	     JOIN KidsCategories K USING(kidscatid)
	left join SessionHasPubChar SHPC USING(sessionid)
	left join PubCharacteristics PC USING(pubcharid)
	    where PS.pubstatusname = 'Public'
	 GROUP BY scheduleid
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=querycsvreport($query,$link);

    ## Page rendering
    topofpagecsv($resultsfile);
    rendercsvreport($headers,$rows,$header_array,$class_array);

?>
