<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="pubsbiocsvreport.php";
    $title="CSV - Biographies for publication";
    $description="<P>Show the badgeid, all names, and bio for each participant who is on at least one scheduled session.</P>\n";
    $additionalinfo="";
    $indicies="CSVSWANTS=1";
    $resultsfile="PubBio.csv";

    // order by: if lastname is part of pubsname, order by it, otherwise, order by last word/token in pubsname
    $query=<<<EOD
SELECT
        P.badgeid, CD.lastname, CD.firstname,
	    CD.badgename, P.pubsname, P.bio 
	FROM
	    Participants P JOIN
	    CongoDump CD USING (badgeid) JOIN
	    (SELECT DISTINCT badgeid 
	       FROM ParticipantOnSession POS JOIN 
	            Schedule SCH USING (sessionid)
	     ) as X
	   USING (badgeid) 
	ORDER BY
	    IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=querycsvreport($query,$link);

    ## Page rendering
    topofpagecsv($resultsfile);
    rendercsvreport($headers,$rows,$header_array,$class_array);

?>
