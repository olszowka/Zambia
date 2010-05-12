<?php
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $reportid=$_GET["reportid"];
    $_SESSION['return_to_page']="genindex.php";
    $title="General Report Generator";
    $description="<P>If you are seeing this, something failed trying to get report: $reportid.</P>\n";
    $additionalinfo="";

    ## No reportid, load the all-reports page
    if (!$reportid) {
      $title="List of all reports";
      $description="<P>Here is a list of all the reports that are available to be generated.</P>\n";
      $query = <<<EOD
SELECT
    concat("<A HREF=genreport.php?reportid=",reportid,">",reporttitle,"</A> (<A HREF=genreport.php?reportid=",reportid,"&csv=y>csv</A>)") AS Title,
    reportdescription AS Description
  FROM
      Reports
  ORDER BY
    reportname
EOD;

      ## Retrieve query
      list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description);

      ## Page Rendering
      topofpagereport($title,$description,$additionalinfo);
      renderhtmlreport($rows,$header_array,$report_array);
      } else {

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

      ## Fix reference problem
      $report_array[1]['reportquery']=str_replace('$ConStartDatim',$ConStartDatim,$report_array[1]['reportquery']);

      ## Retrieve secondary query
      list($rows,$header_array,$class_array)=queryreport($report_array[1]['reportquery'],$link,$report_array[1]['reporttitle'],$report_array[1]['reportdescription']);
      $report_array[1]['reportadditionalinfo'].="<P><A HREF=\"genreport.php?reportid=$reportid&csv=y\" target=_blank>csv</A> file</P>\n";
      if ($returned_reports > 1) {$report_array[1]['reportadditionalinfo'].="<P>Number of matches: $rows</P>\n";}

      ## Page Rendering
      if ($_GET["csv"]=="y") {
        topofpagecsv($report_array[1]['reportname'].".csv");
        rendercsvreport($rows,$header_array,$class_array);
        } else {
        topofpagereport($report_array[1]['reporttitle'],$report_array[1]['reportdescription'],$report_array[1]['reportadditionalinfo']);
        renderhtmlreport($rows,$header_array,$class_array);
        }
      }

?>