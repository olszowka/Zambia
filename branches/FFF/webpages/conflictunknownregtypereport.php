<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="conflictunknownregtypereport.php";
    $title="Conflict Report - Unknown RegTypes";
    $description="<P>Congo RegTypes that Zambia does not recognize.</P>\n";
    $additionalinfo="";
    $indicies="CONFLICTWANTS=1, REGWANTS=1";

    $query = <<<EOD
SELECT
    distinct(C.regtype)
  FROM
      CongoDump C 
    LEFT JOIN RegTypes R on C.regtype=R.regtype
  WHERE
    R.regtype is NULL and
    C.regtype is not NULL
  ORDER BY
    C.Regtype
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$class_array);
