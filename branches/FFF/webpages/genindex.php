<?php
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted
    $ProgramEmail=PROGRAM_EMAIL;

    ## LOCALIZATIONS
    $gflowname=$_GET["gflowname"];
    $_SESSION['return_to_page']="genindex.php";
    $title="General index Generator";
    $description="<P>If you are seeing this, something failed trying to get index: $gflowname.</P>\n";
    $additionalinfo="";

    ## No reportid, load the all-reports page
    if (!$gflowname) {
      $title="List of all indicies";
      $description="<P>Here is a list of all the indicies that are available to be generated.</P>\n";
      $additionalinfo="<P>If a Div/Area Head would like any of their reports tweaked, email to $ProgramEmail and let us know.</P>\n";
      $query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genindex.php?gflowname=",gflowname,">",gflowname," Reports</A>") AS Indicies
  FROM
      GroupFlow
  ORDER BY
    gflowname
EOD;
      ## Retrieve query
      list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

      ## Hand-add "All Reports", "Search", "My Flow", and "Grids" entry for now.
      $rows++;
      $report_array[$rows]['Indicies']="<A HREF=manualGRIDS.php>Grids</A>";
      $rows++;
      $report_array[$rows]['Indicies']="<A HREF=personalflow.php>My Flow</A>";
      $rows++;
      $report_array[$rows]['Indicies']="<A HREF=genreport.php>All Reports</A>";
      $rows++;
      $report_array[$rows]['Indicies']="<A HREF=searchreport.php>Search</A>";

      ## Page Rendering
      topofpagereport($title,$description,$additionalinfo);
      renderhtmlreport($rows,$header_array,$report_array);
      } else {

      $title="$gflowname Reports";
      $description="<P>Here is a list of all the $gflowname reports that are available to be generated during this phase.</P>\n";
      $query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",R.reportid,">",R.reporttitle,"</A> (<A HREF=genreport.php?reportid=",R.reportid,"&csv=y>csv</A>)") AS Title,
    R.reportdescription AS Description
  FROM
    GroupFlow GF,
    Reports R,
    Phases P
  WHERE
    GF.reportid=R.reportid and
    GF.gflowname='$gflowname' and
    (GF.phaseid is null or (GF.phaseid = P.phaseid and P.current = TRUE))
  ORDER BY
    GF.gfloworder
EOD;

      ## Retrieve query
      list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

      ## Page Rendering
      topofpagereport($title,$description,$additionalinfo);
      renderhtmlreport($rows,$header_array,$report_array);
      }
?>
