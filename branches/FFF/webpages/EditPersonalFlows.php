<?php
    require_once('StaffCommonCode.php');
    global $link;

    ## LOCALIZATIONS
    $_SESSION['return_to_page']="EditPersonalFlows.php";
    $title="Edit Personal Flow Reports";
    $description="<P>Edit the order of your personal flow, generally and for each phase.</P>\n";
    $additionalinfo="<P><A HREF=personalflow.php>Return</A> to your Personal Flow.</P>";
    $mybadgeid=$_SESSION['badgeid'];

    if (isset($_POST['addto'])) {
      add_flow_report($_POST['addto'],$_POST['addphase'],"Personal","",$title,$description);
    }

    if (isset($_POST['unrank'])) {
      remove_flow_report($_POST['unrank'],"Personal",$title,$description);
    }

    if (isset($_POST['upfrom'])) {
      deltarank_flow_report($_POST['upfrom'],"Personal","Up",$title,$description);
    }

    if (isset($_POST['downfrom'])) {
      deltarank_flow_report($_POST['downfrom'],"Personal","Down",$title,$description);
    }

    ## Forms inserted into the query
    $uprank_query ="concat('<FORM name=\"uprank\" method=POST action=\"EditPersonalFlows.php\">";
    $uprank_query.="<INPUT type=\"hidden\" name=\"upfrom\" value=\"',PF.pflowid,'\">";
    $uprank_query.="<INPUT type=submit value=\"Move Up\">";
    $uprank_query.="</FORM>') as Earlier,";
    $downrank_query ="concat('<FORM name=\"downrank\" method=POST action=\"EditPersonalFlows.php\">";
    $downrank_query.="<INPUT type=\"hidden\" name=\"downfrom\" value=\"',PF.pflowid,'\">";
    $downrank_query.="<INPUT type=submit value=\"Move down\">";
    $downrank_query.="</FORM>') as Later,";
    $addto_query ="concat('<FORM name=\"addto\" method=POST action=\"EditPersonalFlows.php\">";
    $addto_query.="<INPUT type=\"hidden\" name=\"addto\" value=\"',R.reportid,'\">";
    $addto_query.="<LABEL for=\"addphase\" ID=\"addphase\"></LABEL>";
    $addto_query.="<INPUT type=\"text\" name=\"addphase\" size=\"1\">";
    $addto_query.=" <INPUT type=submit value=\"Add\">";
    $addto_query.="</FORM>') as 'Add To<BR>Phaseid #',";
    $remove_query ="concat('<FORM name=\"unrank\" method=POST action=\"EditPersonalFlows.php\">";
    $remove_query.="<INPUT type=\"hidden\" name=\"unrank\" value=\"',PF.pflowid,'\">";
    $remove_query.="<INPUT type=submit value=\"Remove\">";
    $remove_query.="</FORM>') as Remove,";

    ## First table, list of phases and their phaseids
    $query = <<<EOD
SELECT
    phaseid,
    concat(phasename,if ((current=TRUE),' (c)',' ')) AS Phases
  FROM
    Phases  
  ORDER BY
    phaseid
EOD;

    ## Retrieve query
    list($phaserows,$phaseheader_array,$phasereport_array)=queryreport($query,$link,$title,$description,0);

    ## Add the "all" entry, just in case.
    $phaserows++;
    $phasereport_array[$phaserows]['Phases']="ALL";

    $query = <<<EOD
SELECT
    DISTINCT concat("<A HREF=genreport.php?reportid=",R.reportid,">",R.reporttitle,"</A> (<A HREF=genreport.php?reportid=",R.reportid,"&csv=y>csv</A>)") AS Title,
    $uprank_query
    $downrank_query
    $addto_query
    $remove_query
    PF.pfloworder,
    if((PF.phaseid IS NULL),'ALL',P.phasename) as Phase
  FROM
    PersonalFlow PF,
    Reports R,
    Phases P
  WHERE
    PF.badgeid=$mybadgeid AND
    PF.reportid=R.reportid AND
    (PF.phaseid is NULL OR (PF.phaseid = P.phaseid AND P.current = TRUE))
  ORDER BY
    P.phasename,PF.pfloworder
EOD;

    ## Retrieve query
    list($rows,$header_array,$report_array)=queryreport($query,$link,$title,$description,0);

    ## Page Rendering
    topofpagereport($title,$description,$additionalinfo);
    renderhtmlreport($phaserows,$phaseheader_array,$phasereport_array,0);
    renderhtmlreport($rows,$header_array,$report_array,1);
?>