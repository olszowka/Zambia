<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('StaffCommonCode.php');
$title = "Precis";
staff_header($title, true);
$showlinks = getInt("showlinks", 0);
$_SESSION['return_to_page'] = "ViewPrecis.php?showlinks=$showlinks";
$ConStartDatim = CON_START_DATIM; // make it a variable so it can be substituted
$queryArray = array();
$queryArray["schedule"] =<<<EOD
SELECT
		S.sessionid, TR.trackname, TY.typename, S.title,
		concat( if(left(S.duration,2)=00, '', if(left(S.duration,1)=0, concat(right(left(S.duration,2),1),'hr '), 
			concat(left(S.duration,2),'hr '))), if(date_format(S.duration,'%i')=00, '', if(left(date_format(S.duration,'%i'),1)=0, 
			concat(right(date_format(S.duration,'%i'),1),'min'), concat(date_format(S.duration,'%i'),'min')))) AS duration,
		S.estatten, S.progguiddesc, S.persppartinfo,
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime,
		R.roomname, SS.statusname, GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist
	FROM 
                  Sessions S
             JOIN Tracks TR USING (trackid)
             JOIN Types TY USING (typeid)
             JOIN SessionStatuses SS USING (statusid)
        LEFT JOIN Schedule SCH USING (sessionid)
        LEFT JOIN Rooms R USING (roomid)
        LEFT JOIN SessionHasTag SHT USING (sessionid)
        LEFT JOIN Tags TA USING (tagid)
	WHERE 
		S.statusid IN (1, 6, 2) /* Brainstorm, Edit Me, Vetted (Respectively)  */
    GROUP BY
        S.sessionid, TR.trackname, TY.typename, S.title, SCH.starttime, S.estatten, S.progguiddesc, S.persppartinfo, S.duration, R.roomname, SS.statusname;
EOD;
if (($resultXML = mysql_query_XML($queryArray)) === false) {
    $message="Error querying database. Unable to continue.<br>";
    echo "<p class\"alert alert-error\">$message</p>\n";
    staff_footer();
    exit();
}
$paramArray = array();
$paramArray["showLinks"] = ($showlinks === 1);
$paramArray["now"] = date('d-M-Y h:i A');
$paramArray["trackIsPrimary"] = TRACK_TAG_USAGE === "TRACK_ONLY" || TRACK_TAG_USAGE === "TRACK_OVER_TAG";
$paramArray["showTrack"] = TRACK_TAG_USAGE !== "TAG_ONLY";
$paramArray["showTags"] = TRACK_TAG_USAGE !== "TRACK_ONLY";
// echo(mb_ereg_replace("<(row|query)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i")); //for debugging only
RenderXSLT('StaffListSessions.xsl', $paramArray, $resultXML);
staff_footer();
?>
