<?php
//	Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $linki, $participant, $message_error, $message2, $title;
$title = "Show Search Session Results";
require('BrainstormCommonCode.php'); // initialize db; check login;
$ConStartDatim = CON_START_DATIM;
$trackid = getInt("track", 0);
$titlesearch = isset($_POST["title"]) ? stripslashes($_POST["title"]) : "";
$query = <<<EOD
SELECT
        sessionid, trackname, null typename, title, 
        CONCAT( IF(LEFT(duration,2)=00, '', 
                IF(LEFT(duration,1)=0, CONCAT(RIGHT(LEFT(duration,2),1),'hr '), CONCAT(LEFT(duration,2),'hr '))),
                IF(DATE_FORMAT(duration,'%i')=00, '', 
                IF(LEFT(DATE_FORMAT(duration,'%i'),1)=0, CONCAT(RIGHT(DATE_FORMAT(duration,'%i'),1),'min'), 
            CONCAT(DATE_FORMAT(duration,'%i'),'min')))) Duration,
        estatten, progguiddesc, persppartinfo, roomname,
		DATE_FORMAT(ADDTIME('$ConStartDatim',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
    	          Sessions S
    	     JOIN Tracks TR USING (trackid)
    	     JOIN SessionStatuses SS USING (statusid)
		LEFT JOIN Schedule SCH USING (sessionid)
		LEFT JOIN Rooms R USING (roomid)
    WHERE
            SS.statusname IN ('Edit Me','Brainstorm','Vetted','Assigned','Scheduled')
        AND S.invitedguest=0
EOD;
if ($trackid != 0) {
    $query .= " and S.trackid=" . $trackid;
}
if ($titlesearch != "") {
    $query .= " AND title LIKE \"%" . mysqli_real_escape_string($linki, $titlesearch) . "%\" ";
}
$query .= <<<EOD
    ORDER BY
        trackname, title
EOD;
if (!$result = mysqli_query_with_error_handling($query)) {
    exit(); // Should have exited already
}
brainstorm_header($title);
echo "<p This list includes ALL ideas that have been submitted.   Some may require Peril Sensitive Sunglasses.</p>";
echo "<p>We are in the process of sorting through these suggestions: combining duplicates; splitting big ones into pieces; ";
echo "checking general feasability; finding needed people to present; looking for an appropiate time and location; ";
echo "rewritting for clarity and proper english; and hoping to find a time machine so we can do it all.</p>";
echo "<p>If you want to help, email us at ";
echo "<a href=\"mailto:" . PROGRAM_EMAIL . "\">" . PROGRAM_EMAIL . "</a> </p>\n";
echo "<p>This list is sorted by Track and then Title.</p>";
RenderPrecis($result, $showlinks);
brainstorm_footer();
exit();
?>
