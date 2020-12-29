<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-12-29

global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Participant Survey";
require_once('PartCommonCode.php');
$message = "";
$rows = 0;

participant_header($title, false, 'Normal', $bootstrap4);
if (isLoggedIn()) {
	if (isset($_POST["PostCheck"])) {
		$priorValues = interpretControlString($_POST["control"], $_POST["controliv"]);

		if ($priorValues["getSessionID"] !=  session_id()) {
            $message = "Session expired, survey not updated";
        } else {
		// find the data to insert/update
			echo "<h1>Submitted data</h1>";
			foreach ($_POST as $key => $value) {
				echo "$key => '$value'<br/>";
			}
			//        if ($rows == 1) {
			//            $message = "Survey Updated";
			//        } else {
			//            $message = "No chages to update-rows";
			//        }
			//    } else {
			//        $message = "No chages to update-select";
			//    }
			//} else {
			//    $message = "No chages to update-unchanged";
        }
    }

    // Start of display portion
    // add javascript to enable tooltips
	echo <<<EOD
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').each(function() {
	this.title= '<span class="text-left" style="white-space: nowrap;">' + this.title + '</span>';
	})
  $('[data-toggle="tooltip"]').tooltip();
  $('[data-mce="yes"]').each(function() {
	fieldname = this.getAttribute("id");
	height = this.getAttribute("rows") * 50;
	maxlength = this.getAttribute("maxlength");
	tinyMCE.init({
            selector: "textarea#" + fieldname,
            plugins: 'fullscreen lists advlist link preview searchreplace autolink charmap hr nonbreaking visualchars code ',
            browser_spellcheck: true,
            contextmenu: false,
            height: height,
            width: 900,
            min_height: 200,
            maxlength: maxlength,
            menubar: false,
            toolbar: [
                'undo redo searchreplace | styleselect | bold italic underline strikethrough removeformat | visualchars nonbreaking charmap hr | preview fullscreen ',
                'alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist checklist | forecolor backcolor | link'
            ],
            toolbar_mode: 'floating',
            content_style: 'body {font - family:Helvetica,Arial,sans-serif; font-size:14px }',
            placeholder: 'Type content here...'
        });
  });
});
</script>
EOD;
// json of current questions and question options
	$paramArray = array();
	$query = [];
	$query["questions"]=<<<EOD
		SELECT d.questionid, d.shortname, d.description, prompt, hover, d.display_order, d.typeid, t.shortname as typename,
			required, publish, privacy_user, searchable, ascending, display_only, min_value, max_value,
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
			CASE WHEN ISNULL(a.privacy_setting) THEN publish ELSE a.privacy_setting END AS privacy_setting
		FROM SurveyQuestionConfig d
		JOIN SurveyQuestionTypes t USING (typeid)
		LEFT OUTER JOIN ParticipantSurveyAnswers a ON (a.questionid = d.questionid and a.participantid = "$badgeid")
		ORDER BY d.display_order ASC;
EOD;

	$query["options"] = <<<EOD
		SELECT questionid, display_order, questionid, ordinal, value, optionshort, optionhover, allowothertext, display_order
		FROM SurveyQuestionOptionConfig
		ORDER BY questionid, display_order;
EOD;
	$resultXML = mysql_query_XML($query);

	// add javascript to enable tooltips
	echo <<<EOD
<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').each(function() {
	this.title= '<span class="text-left" style="white-space: nowrap;">' + this.title + '</span>';
	})
  $('[data-toggle="tooltip"]').tooltip();
});
</script>
EOD;
	// get any questions that need programically create options
	$sql = <<<EOD
		SELECT questionid, t.shortname as typename, min_value, max_value, ascending
		FROM SurveyQuestionConfig d
		JOIN SurveyQuestionTypes t USING (typeid)
		WHERE t.shortname IN ('numberselect', 'monthyear');
EOD;
	$result = mysqli_query_exit_on_error($sql);
	while ($row = mysqli_fetch_assoc($result)) {
		$numberquery = "years";
		switch ($row["typename"]) {
			case "numberselect":
                $numberquery = "options";   // fall into monthyear
            case "monthyear":
                // build xml array from begin to end
                $options = [];
                $question_id = $row["questionid"];
                if ($row["ascending"] == 1) {
                    $next = $row["min_value"];
                    $end = $row["max_value"];
                    while ($next <= $end) {
                        $ojson = new stdClass();
                        $ojson->questionid = $question_id;
                        $ojson->value = $next;
                        $ojson->optionshort = $next;
                        $options[] = $ojson;
                        $next = $next + 1;
                    }
                }
                else {
                    $next = $row["max_value"];
                    $end = $row["min_value"];
                    while ($next >= $end) {
                        $ojson = new stdClass();
                        $ojson->questionid = $question_id;
                        $ojson->value = $next;
                        $ojson->optionshort = $next;
                        $options[] = $ojson;
                        $next = $next - 1;
                    }
                }
                //var_error_log($options);
                $resultXML = ObjecttoXML($numberquery, $options, $resultXML);
                break;
        }
    }
	$sql = <<<EOD
		SELECT count(*) AS answers
		FROM ParticipantSurveyAnswers
		WHERE participantid = "$badgeid";
EOD;
	$result = mysqli_query_exit_on_error($sql);
	$rows = 0;
	while ($row = mysqli_fetch_assoc($result)) {
		$rows = $row["answers"];
    }

	$paramArray["buttons"] = $rows == 0 ?  "save" : "update";
	$PriorArray["getSessionID"] = session_id();

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	// following line for debugging only
	//echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('RenderSurvey.xsl', $paramArray, $resultXML);
}
participant_footer();
?>