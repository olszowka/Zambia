<?php
    require_once('StaffCommonCode.php');
    global $link;

    ## LOCALIZATIONS
    $showreport=0;
    $reportid=$_GET["reportid"];
    $reportname=$_GET["reportname"];
    $title="General Report Generator";
    $additionalinfo="";

    ## Switch on which way this is called
    if (!$reportname) {
      $_SESSION['return_to_page']="genreport.php?reportid=$reportid";
      $description="<P>If you are seeing this, something failed trying to get report: $reportid.</P>\n";
    } else {
      $_SESSION['return_to_page']="genreport.php?reportname=$reportname";
      $description="<P>If you are seeing this, something failed trying to get report: $reportname.</P>\n";
      $showreport++;
    }
    if ($reportid) {$showreport++;}

    ## No reportid, load the all-reports page
    if ($showreport==0) {
      $_SESSION['return_to_page']="genreport.php";
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
      list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

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
EOD;

      if (!$reportid) {
	$query.="\n    reportname = '$reportname'";
      } else {
	$query.="\n    reportid = '$reportid'";
      }

      ## Retrieve query
      list($returned_reports,$unused_array,$report_array)=queryreport($query,$link,$title,$description,0);

      ## Fix reference problem
      $report_array[1]['reportquery']=str_replace('$ConStartDatim',CON_START_DATIM,$report_array[1]['reportquery']);
      $report_array[1]['reportquery']=str_replace('$GohBadgeList',GOH_BADGE_LIST,$report_array[1]['reportquery']);

      ## Retrieve secondary query
      list($rows,$header_array,$class_array)=queryreport($report_array[1]['reportquery'],$link,$report_array[1]['reporttitle'],$report_array[1]['reportdescription'],$reportid);
      $report_array[1]['reportadditionalinfo'].="<P><A HREF=\"genreport.php?reportid=$reportid&csv=y\" target=_blank>csv</A> file</P>\n";
      $report_array[1]['reportadditionalinfo'].="<P>Add this report to your Personal Flow.</P>\n";
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
