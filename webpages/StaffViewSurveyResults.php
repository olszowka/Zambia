<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-12-29
// File created by Syd Weinstein on 2021-01-20

global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "View Survey";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;

staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Staff")) {
	// Start of display portion

	// json of current questions and question options
	$badgeid = getString('badgeid');
	if ($badgeid) {
		$paramArray = array();
		$query = [];
		$query["questions"]=<<<EOD
		SELECT d.questionid, d.shortname, d.description, prompt, hover, d.display_order, d.typeid, t.shortname as typename,
			required, publish, privacy_user, searchable, ascending, 1 AS display_only, min_value, max_value,
			CASE
				WHEN t.shortname = "openend" THEN
					CASE
						WHEN max_value > 100 THEN 100
						WHEN max_value < 50 THEN 50
						ELSE max_value
					END
				WHEN t.shortname = "text" OR t.shortname = "html-text" THEN
					CASE
							WHEN max_value > 400 THEN 100
							WHEN max_value < 200 THEN 50
							ELSE max_value / 4
					END
				ELSE ""
			END AS size,
			CASE
				WHEN t.shortname = "text" OR t.shortname = "html-text" THEN
					CASE WHEN max_value > 500 THEN 8 ELSE 4 END
				ELSE ""
			END as `rows`,
			CASE WHEN ISNULL(a.value) THEN "" ELSE a.value END AS answer,
			CASE WHEN ISNULL(a.othertext) THEN "" ELSE a.othertext END AS othertext,
			CASE WHEN ISNULL(a.privacy_setting) THEN publish ELSE a.privacy_setting END AS privacy_setting,
            CASE WHEN SUM(o.allowothertext) > 0 THEN 1 ELSE 0 END AS allowothertext
		FROM SurveyQuestionConfig d
		JOIN SurveyQuestionTypes t USING (typeid)
		JOIN ParticipantSurveyAnswers a ON (a.questionid = d.questionid and a.participantid = "$badgeid")
        LEFT OUTER JOIN SurveyQuestionOptionConfig o ON (d.questionid = o.questionid)
        GROUP BY d.questionid
		ORDER BY d.display_order ASC;
EOD;
		$resultXML = mysql_query_XML($query);

		$query = <<<EOD
WiTH AnsweredSurvey(participantid, answercount) AS (
    SELECT participantid, COUNT(*) AS answercount
    FROM ParticipantSurveyAnswers
)
SELECT CD.firstname, CD.lastname, CD.badgename, P.pubsname, IFNULL(A.answercount, 0) AS answercount
FROM Participants P
JOIN CongoDump CD USING (badgeid)
LEFT OUTER JOIN AnsweredSurvey A ON (A.participantid = P.badgeid)
WHERE P.badgeid = '$badgeid';
EOD;
		$result = mysqli_query_exit_on_error($query);
		while ($row = mysqli_fetch_assoc($result)) {
			$pubsname = $row["pubsname"];
			if ($pubsname == '' || $pubsname === null) {
				$pubsname = $row['firstname'];
				if ($row['lastname'] != '' && $row['lastname'] !== null)
					$pubsname .= " " . $row['lastname'];
			}
			if ($row['badgename'] != '' && $row['badgename'] !== null)
				$pubsname .= ' (' . $row['badgename'] . ')';

			$paramArray['pubsname'] = $pubsname;
			$paramArray['answercount'] = $row['answercount'];
        }

		$paramArray["buttons"] = "close";

		if ($message != "") {
			$paramArray["UpdateMessage"] = $message;
		}

		// following line for debugging only
		//echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
		RenderXSLT('RenderSurvey.xsl', $paramArray, $resultXML);
    }
}
staff_footer();
?>