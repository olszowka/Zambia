<?php
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $reportid=1;
//    $_SESSION['return_to_page']="allassignedreport.php";
    $title="General Report Generator";
    $description="<P>If you are seeing this, something failed.</P>\n";
    $additionalinfo="";

    $query = <<<EOD
SELECT
    reportname,
    reporttitle,
    reportdescription,
    reportadditionalinfo,
    reportquery
  FROM
      Reports
  WHERE
    reportid = '$reportid'
EOD;

    ## Retrieve query
    list($returned_reports,$unused_array,$report_array)=queryreport($query,$link,$title,$description);

    ## Retrieve secondary query
    list($rows,$header_array,$class_array)=queryreport($report_array[1]['reportquery'],$link,$report_array[1]['reporttitle'],$report_array[1]['reportdescription']);
    $report_array[1]['reportadditionalinfo'].="<P><A HREF=\"".$report_array[1]['reportname']."report.php?csv=y\" target=_blank>csv</A> file</P>\n";
    if ($returned_reports > 1) {$report_array[1]['reportadditionalinfo'].="<P>Number of matches: $rows</P>\n";}

    ## Page Rendering
    if ($_GET["csv"]=="y") {
      topofpagecsv($report_array[1]['reportname'].".csv");
      rendercsvreport($rows,$header_array,$class_array);
      } else {
      topofpagereport($report_array[1]['reporttitle'],$report_array[1]['reportdescription'],$report_array[1]['reportadditionalinfo']);
      renderhtmlreport($rows,$header_array,$class_array);
      }
