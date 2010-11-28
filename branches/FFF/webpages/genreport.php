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

      $basereportid=$report_array[1]['reportid'];
      $mybadgeid=$_SESSION['badgeid'];

      ## Get the personal flow previous and next
      
      $query = <<<EOD
SELECT
    DISTINCT reportid
  FROM
      PersonalFlow
      LEFT JOIN Phases USING (phaseid)
  WHERE
    badgeid=$mybadgeid AND
    phaseid is null OR
    current = TRUE
  ORDER BY
    pfloworder
EOD;

      ## Retrieve query
      list($pflowrows,$pflowheader_array,$pflow_array)=queryreport($query,$link,$title,$description,0);

      ## Start with a blank $personal, walk the array, set the previous and next
      $personal="Personal Flow: ";
      for ($i=1; $i<=$pflowrows; $i++) {
        if ($pflow_array[$i]['reportid']==$basereportid) {
	  if ($i > 1) {
	    $personal.="<A HREF=genreport.php?reportid=".$pflow_array[$i-1]['reportid'].">Prev</A> ";
	  }
	  if ($i < $pflowrows) {
	    $personal.="<A HREF=genreport.php?reportid=".$pflow_array[$i+1]['reportid'].">Next</A>";
	  }
	}
      }
      if ($personal=="Personal Flow: ") {$personal="";}

      ## Get the Groups that this report is part of
      $query = <<<EOD
SELECT
    GF.gflowname
  FROM
      Reports R,
      GroupFlow GF
  WHERE
    R.reportid=GF.reportid AND
    R.reportid=$basereportid
EOD;

      ## Retrieve query
      list($grouplistrows,$grouplistheader_array,$grouplistreport_array)=queryreport($query,$link,$title,$description,0);

      $groups="";

      ## Iterate accross the Groups getting the previous and next
      for ($i=1; $i<=$grouplistrows; $i++) {
	$cgroup=$grouplistreport_array[$i]['gflowname'];
        $query = <<<EOD
SELECT
    DISTINCT reportid
  FROM
      GroupFlow
      LEFT JOIN Phases USING (phaseid)
  WHERE
    gflowname='$cgroup' AND
    phaseid is null OR
    current = TRUE
  ORDER BY
    gfloworder
EOD;

        ## Retrieve query
        list($gflowrows,$gflowheader_array,$gflow_array)=queryreport($query,$link,$title,$description,0);

        ## Start with a blank $personal, walk the array, set the previous and next
        $cgroups=" $cgroup Flow: ";
	for ($j=1; $j<=$gflowrows; $j++) {
	  if ($gflow_array[$j]['reportid']==$basereportid) {
	    if ($j > 1) {
	      $cgroups.="<A HREF=genreport.php?reportid=".$gflow_array[$j-1]['reportid'].">Prev</A> ";
	    }
	    if ($j < $gflowrows) {
	      $cgroups.="<A HREF=genreport.php?reportid=".$gflow_array[$j+1]['reportid'].">Next</A>";
	    }
	  }
	}
	if ($cgroups==" $cgroup Flow: ") {$cgroups="";}
	$groups.=$cgroups;
      }

      for ($i=1; $i<=$returned_reports; $i++) {
	## Fix reference problem
	$report_array[$i]['reportquery']=str_replace('$ConStartDatim',CON_START_DATIM,$report_array[$i]['reportquery']);
	$report_array[$i]['reportquery']=str_replace('$GohBadgeList',GOH_BADGE_LIST,$report_array[$i]['reportquery']);
	$report_array[$i]['reportquery']=str_replace('$_SESSION[\'badgeid\']',$_SESSION['badgeid'],$report_array[$i]['reportquery']);

        ## Retrieve secondary query
        list($rows,$header_array,$class_array)=queryreport($report_array[$i]['reportquery'],$link,$report_array[$i]['reporttitle'],$report_array[$i]['reportdescription'],$reportid);
        $report_array[$i]['reportadditionalinfo'].="<P><A HREF=\"genreport.php?reportid=".$report_array[$i]['reportid']."&csv=y\" target=_blank>csv</A> file</P>\n";
        $report_array[$i]['reportadditionalinfo'].="<P><FORM name=\"addto\" method=POST action=\"genreport.php?reportid=".$report_array[$i]['reportid']."\">";
        $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"hidden\" name=\"addto\" value=\"".$report_array[$i]['reportid']."\">";
        $report_array[$i]['reportadditionalinfo'].=" <INPUT type=submit value=\"Add\">";
        $report_array[$i]['reportadditionalinfo'].=" this report to your Personal Flow. (If you wish, put in the phase number: ";
        $report_array[$i]['reportadditionalinfo'].="<LABEL for=\"addphase\" ID=\"addphase\"></LABEL>";
        $report_array[$i]['reportadditionalinfo'].="<INPUT type=\"text\" name=\"addphase\" size=\"1\">.)";
        $report_array[$i]['reportadditionalinfo'].="</FORM>\n";
        $report_array[$i]['reportadditionalinfo'].="<P>".$personal.$groups."</P>";
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
