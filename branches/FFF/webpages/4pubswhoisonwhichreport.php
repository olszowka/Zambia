<?php
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    global $link;
    $ConStartDatim=CON_START_DATIM; // make it a variable so it can be substituted

    ## LOCALIZATIONS
    $strScript=$_SERVER['SCRIPT_NAME'];
    $intLastSlash = strrpos($strScript, "/");
    $scriptname = substr($strScript, $intLastSlash+1, strlen($strScript));
    $_SESSION['return_to_page']="$scriptname";
    $title="Pubs - Who is on Which Session";
    $description="<P>Show the badgeid, pubsname and session info for each participant that are on at least one scheduled session.</P>\n";
    $additionalinfo="<P><a href=\"$scriptname?csv=y\" target=_blank>csv file</a></P>\n";
    $indicies="PUBSWANTS=1, GENCSV=1";

    # First query sets the max length, second the actual program description query.
    $query="SET group_concat_max_len=25000";
    if (!$result=mysql_query($query,$link)) {
	$message=$query."<BR>Error querying database. Unable to continue.<BR>";
        $message.="<P class\"errmsg\">".$query."\n";
	RenderError($title,$message);
        exit();
        }

    $query = <<<EOD
SELECT
    P.badgeid, 
    P.pubsname, 
    s as Sessions 
  FROM
      (SELECT 
           distinct(badgeid), 
           group_concat(' ',POS.sessionid, if (moderator=1,' (m)','')) as s
         FROM
             ParticipantOnSession POS, 
             Schedule SCH 
         WHERE
             POS.sessionid=SCH.sessionid 
         GROUP BY
             badgeid) as X, 
      Participants P
  WHERE
    P.badgeid=X.badgeid
EOD;

    ## Retrieve query
    list($rows,$header_array,$class_array)=queryreport($query,$link,$title,$description);

    ## Page Rendering
    if ($_GET["csv"]=="y") {
      topofpagecsv(str_replace('report.php','.csv',$scriptname));
      rendercsvreport($rows,$header_array,$class_array);
      } else {
      topofpagereport($title,$description,$additionalinfo);
      renderhtmlreport($rows,$header_array,$class_array);
      }
