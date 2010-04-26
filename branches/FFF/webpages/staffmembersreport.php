<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="staffmembersreport.php";
    $title="Staff Members";
    $description="<P>List Staff Members and their privileges.</P>\n";
    $additionalinfo="";
    $indicies="ADMINWANTS=1";

    $query = <<<EOD
SELECT
      badgeid as Badgeid,
      if(P.pubsname is null or P.pubsname = '',concat(firstname,' ',lastname),P.pubsname) as Name,
      if (password='4cb9c8a8048fd02294477fcb1a41191a','changme','OK') as Password,
      group_concat(permrolename SEPARATOR ', ') as Privileges
    FROM
        Participants P
            JOIN CongoDump using (badgeid)
            JOIN UserHasPermissionRole using (badgeid)
            JOIN PermissionRoles using (permroleid)
    WHERE badgeid in 
          (SELECT DISTINCT badgeid FROM UserHasPermissionRole where permroleid=2)
    GROUP BY badgeid, name, password
EOD;

    ## Retrieve query
    list($headers,$rows,$header_array,$class_array)=queryhtmlreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($headers,$rows,$header_array,$class_array);
