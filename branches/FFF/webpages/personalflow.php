<?php
    require_once('StaffCommonCode.php');
    global $link;

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="personalflow.php";
    $title="Personal Flow Reports";
    $description="<P>Here is a list of all the reports, that you have added to your personal flow, that are available to be generated during this phase.</P>\n";
    $additionalinfo="<P><A HREF=EditPersonalFlows.php>Change</A> the ordering of this page.";

    $query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",R.reportid,">",R.reporttitle,"</A> (<A HREF=genreport.php?reportid=",R.reportid,"&csv=y>csv</A>)") AS Title,
    R.reportdescription AS Description
  FROM
    PersonalFlow PF,
    Reports R,
    Phases P
  WHERE
    PF.reportid=R.reportid and
    (PF.phaseid is null or (PF.phaseid = P.phaseid and P.current = TRUE))
  ORDER BY
    PF.pfloworder
EOD;

    ## Retrieve query
    list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($rows,$header_array,$report_array,1);
?>
