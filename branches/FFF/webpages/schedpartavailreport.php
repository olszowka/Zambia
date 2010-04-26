<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="schedpartavailreport.php";
    $title="Participant availablity";
    $description="<P>When they said they were available.</P>\n";
    $additionalinfo="";
    $indicies="PROGWANTS=1";

    $query = <<<EOD
SELECT
        P.badgeid, P.pubsname, 
        DATE_FORMAT(ADDTIME('$ConStartDatim',starttime),'%a %l:%i %p') AS 'Start Time', 
        DATE_FORMAT(ADDTIME('$ConStartDatim',endtime),'%a %l:%i %p') AS 'End Time',
        PA.otherconstraints,
        PA.preventconflict
    FROM
        Participants AS P LEFT JOIN
        ParticipantAvailabilityTimes AS PAT USING (badgeid)
        JOIN ParticipantAvailability PA USING (badgeid)
    WHERE
        P.interested=1
    ORDER BY
        CAST(P.badgeid AS UNSIGNED),starttime
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
