<?php
    require_once('StaffCommonCode.php');
    global $link;

    ## LOCALIZATIONS
    $showreport=0;
    $reportid=$_GET["reportid"];
    $reportname=$_GET["reportname"];
    $title="General Report Generator";
    $additionalinfo="";

    ## Check for addtion
    if (isset($_POST['addto'])) {
      add_flow_report($_POST['addto'],$_POST['addphase'],"Personal","",$title,$description);
    }

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
      renderhtmlreport($rows,$header_array,$report_array,1);
      } else {

      $query = <<<EOD
SELECT
    reportname,
    reportid,
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
      list($returned_reports,$reportnumber_array,$report_array)=queryreport($query,$link,$title,$description,0);

      for ($i=1; $i<=$returned_reports; $i++) {
	## Fix reference problem
	$report_array[$i]['reportquery']=str_replace('$ConStartDatim',CON_START_DATIM,$report_array[$i]['reportquery']);
	$report_array[$i]['reportquery']=str_replace('$GohBadgeList',GOH_BADGE_LIST,$report_array[$i]['reportquery']);

        ## Retrieve secondary query
        list($rows,$header_array,$class_array)=queryreport($report_array[$i]['reportquery'],$link,$report_array[$i]['reporttitle'],$report_array[$i]['reportdescription'],$reportid);
        $report_array[$i]['reportadditionalinfo'].="<P><A HREF=\"genreport.php?reportid=".$report_array[$i]['reportid']."&csv=y\" target=_blank>csv</A> file</P>\n";
        $report_array[$i]['reportadditionalinfo'].="<P><FORM name=\"addto\" method=POST action=\"genreport.php?reportid=".$report_array[$i]['reportid']."\">";
        $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"hidden\" name=\"addto\" value=\"".$report_array[$i]['reportid']."\">";
        $report_array[$i]['reportadditionalinfo'].=" <INPUT type=submit value=\"Add\">";
        $report_array[$i]['reportadditionalinfo'].=" this report to your Personal Flow. (If you wish, put in the phase number: ";
        $report_array[$i]['reportadditionalinfo'].="<LABEL for=\"addphase\" ID=\"addphase\"></LABEL>";
        $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"text\" name=\"addphase\" size=\"1\">.)";
        $report_array[$i]['reportadditionalinfo'].="</FORM></P>\n";
        if ($returned_reports > 1) {$report_array[$i]['reportadditionalinfo'].="<P>Report $i of $returned_reports</P>\n";}

        ## Page Rendering
        if ($_GET["csv"]=="y") {
          topofpagecsv($report_array[$i]['reportname'].".csv");
          rendercsvreport($rows,$header_array,$class_array);
          } else {
          topofpagereport($report_array[$i]['reporttitle'],$report_array[$i]['reportdescription'],$report_array[$i]['reportadditionalinfo']);
	  if ($i==$returned_reports) {
            renderhtmlreport($rows,$header_array,$class_array,1);
	    } else {
            renderhtmlreport($rows,$header_array,$class_array,0);
          }
	}
      }
    }
?>
