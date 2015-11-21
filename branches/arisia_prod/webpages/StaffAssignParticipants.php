<?php
//$Header$
$title="Assign Participants";
require_once('db_functions.php');
require_once('StaffHeader.php');
require_once('StaffFooter.php');
require_once('StaffCommonCode.php');
require_once('StaffAssignParticipants_FNC.php');

staff_header($title);

$topsectiononly=true; // no room selected -- flag indicates to display only the top section of the page
if (isset($_POST["numrows"])) {
    SubmitAssignParticipants();
    }

if (isset($_POST["selsess"])) { // room was selected by this form
        $selsessionid=filter_var($_POST["selsess"], FILTER_VALIDATE_INT);
        if ($selsessionid != 0) {
          $topsectiononly=false;
          //unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.          
        }
      }
    elseif (isset($_GET["selsess"])) { // room was select by external page such as a report
        $selsessionid=filter_var($_GET["selsess"], FILTER_VALIDATE_INT);
        $topsectiononly=false;
        }
    else {
        $selsessionid=0; // room was not yet selected.
        unset($_SESSION['return_to_page']); // since edit originated with this page, do not return to another.
        }

$query="SELECT T.trackname, S.sessionid, S.title FROM Sessions AS S ";
$query.="JOIN Tracks AS T USING (trackid) ";
$query.="JOIN SessionStatuses AS SS USING (statusid) ";
$query.="WHERE SS.may_be_scheduled=1 ";
$query.="ORDER BY T.trackname, S.sessionid, S.title";
if (!$Sresult=mysql_query($query,$link)) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"alert alert-error\">".$message."\n";
    staff_footer();
    exit();
    }
echo "<FORM id=\"selsesformtop\" name=\"selsesform\" class=\"form-inline\" method=POST action=\"StaffAssignParticipants.php\">\n";
echo "<DIV><LABEL for=\"selsess\">Select Session:</LABEL>\n";
echo "<SELECT id=\"sessionDropdown\" class=\"span6\"name=\"selsess\">\n";
echo "     <OPTION value=0".(($selsessionid==0)?"selected":"").">Select Session</OPTION>\n";
while (list($trackname,$sessionid,$title)= mysql_fetch_array($Sresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$sessionid."\" ".(($selsessionid==$sessionid)?"selected":"");
    echo ")>".htmlspecialchars($trackname)." - ";
    echo htmlspecialchars($sessionid)." - ".htmlspecialchars($title)."</OPTION>\n";
    }
echo "</SELECT>\n";
echo "<BUTTON id=\"sessionBtn\" type=\"submit\" name=\"submit\" class=\"btn btn-primary\">Select Session</BUTTON>\n";
if (isset($_SESSION['return_to_page'])) {
    echo "<A HREF=\"".$_SESSION['return_to_page']."\">Return to report</A>";
    }
echo "</DIV></FORM>\n";
if ($topsectiononly) {
    staff_footer();
    exit();
    }
$queryArray["sessionInfo"]=<<<EOD
SELECT
		sessionid, title, progguiddesc, persppartinfo, notesforpart, notesforprog
	FROM
		Sessions
	WHERE
		sessionid=$selsessionid
EOD;
$queryArray["participantInterest"]=<<<EOD
SELECT
		POS.badgeid AS posbadgeid,
		COALESCE(POS.moderator, 0) AS moderator,
		P.badgeid,
		P.pubsname,
		P.staff_notes,
		IFNULL(PSI.rank, 99) AS rank,
		PSI.willmoderate,
		PSI.comments,
		P.bio,
		PHR.roleid
	FROM
					Participants AS P
			JOIN
					(SELECT DISTINCT badgeid, sessionid FROM
						(SELECT badgeid, sessionid FROM ParticipantOnSession WHERE sessionid=$selsessionid
						UNION
						SELECT badgeid, sessionid FROM ParticipantSessionInterest WHERE sessionid=$selsessionid) AS R2
						) AS R USING (badgeid)
		LEFT JOIN	ParticipantSessionInterest AS PSI ON R.badgeid = PSI.badgeid AND R.sessionid = PSI.sessionid
		LEFT JOIN	ParticipantOnSession AS POS ON R.badgeid = POS.badgeid AND R.sessionid = POS.sessionid
		LEFT JOIN	ParticipantHasRole AS PHR ON P.badgeid = PHR.badgeid and PHR.roleid = 10 /* moderator */
	WHERE
			POS.sessionid=$selsessionid
		OR	POS.sessionid is null
	ORDER BY
		moderator DESC,
		IFNULL(POS.badgeid, "~") ASC,
		rank ASC,
		P.pubsname ASC;
EOD;
$queryArray["otherParticipants"]=<<<EOD
SELECT
        P.pubsname,
        P.badgeid,
        CD.lastname
    FROM
        Participants P
    JOIN
        CongoDump CD USING(badgeid)
    WHERE
            P.interested=1
        AND NOT EXISTS (
			SELECT *
				FROM
					ParticipantSessionInterest
				WHERE
						sessionid=$selsessionid
					AND badgeid = P.badgeid
            )
    ORDER BY
            IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),CD.firstname
EOD;
if (($resultXML=mysql_query_XML($queryArray))===false) {
    $message=$query."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"alert alert-error\">".$message."\n";
    staff_footer();
    exit();
    }
$parametersNode = $resultXML->createElement("parameters");
$docNode = $resultXML->getElementsByTagName("doc")->item(0);
$parametersNode = $docNode->appendChild($parametersNode);
if (may_I('EditSesNtsAsgnPartPg')) {
	$parametersNode->setAttribute("editSessionNotes", "true");
}
echo($resultXML->saveXML()); //for debugging only
$xsl = new DomDocument;
$xsl->load('xsl/StaffAssignParticipants_sessionInfo.xsl');
$xslt = new XsltProcessor();
$xslt->importStylesheet($xsl);
$html = $xslt->transformToXML($resultXML);
echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
staff_footer();
?>
