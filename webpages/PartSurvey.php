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
        //foreach ($priorValues as $key => $value) {
        //        echo "$key => '$value'<br/>";
        //    }

		if ($priorValues["getSessionID"] !=  session_id()) {
            $message = "Session expired, survey not updated";
        } else {
			$shortname_types = json_decode($priorValues["shortname_types"]);
			//var_dump($shortname_types);
            }
		// find the data to insert/update
			//echo "<h1>Submitted data</h1>";
			//var_dump($_POST);

            $sql = <<<EOD
INSERT INTO ParticipantSurveyAnswers(participantid, questionid, privacy_setting, value, othertext)
VALUES (?, ?, ?, ?, ?)
ON DUPLICATE KEY UPDATE
	privacy_setting = ?,
	value = ?,
	othertext = ?;
EOD;
			$parms = [];
			$types = "";
			$inserted = 0;
			$updated = 0;
			$errors = 0;
			foreach ($shortname_types as $obj) {
				if ($obj->typename != "heading") {
                    $separator = ',';
                    switch ($obj->typename) {
                        case "monthyear":
                            $separator = ' ';
                        case "multi-select list":
                        case "multi-checkbox list":
                        case "multi-display":
                            //echo "processing " . $obj->typename . "<br/>";
                            //echo "shortname = '" . $obj->shortname . "', questionid = " . $obj->questionid . ", id = '" . $obj->id . "'<br/>";
                            $ans = implode($separator, $_POST[$obj->id]);
                            $parms= array($badgeid, $obj->questionid, 0, $ans, null, 0, $ans, null);
                            //var_dump($parms);
                            $types = "siississ";
                            break;
                        default:
                            //echo "processing default for " . $obj->typename . "<br/>";
                            //echo "shortname = '" . $obj->shortname . "', questionid = " . $obj->questionid . ", id = '" . $obj->id . "'<br/>";
                            $parms= array($badgeid, $obj->questionid, 0, $_POST[$obj->id], null, 0, $_POST[$obj->id], null);
                            $types = "siississ";
                    }
                    //var_dump($parms);
                    $rows_modified = mysql_cmd_with_prepare($sql, $types, $parms);
                    //echo "status = $rows_modified<br/><br/>";
                    if ($rows_modified == 1)
                        $inserted = $inserted + 1;
                    else if ($rows_modified == 2)
                        $updated = $updated + 1;
                    else if ($rows_modified < 0) {
                        echo("Error description: " . mysqli_error($linki) . "<br/><br/>");
                        $errors = $errors + 1;
                        break;
                    }
                }
            }
			$message = "";
			if ($inserted > 0)
				$message = $message . $inserted . " answers inserted, ";
			if ($updated > 0)
				$message = $message . $updated . " answers updated, ";
			if ($message == "")
				$message = "No changes made to survey";
			else
				$message = "Survey updated: " . $message;

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
function fadditems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    for (i = 0; i < source.length; i++) {
        if (source.options[i].selected == true) {
            itemtext = source.options[i].text;
            itemvalue = source.options[i].value;
            dest.options[dest.options.length] = new Option(text = itemtext, value = itemvalue);
            source.options[i] = null;
            i--
        }
    }
}

function fdropitems(source, dest) {
    var i;
    var itemtext;
    var itemvalue;
    for (i = 0; i < dest.length; i++) {
        if (dest.options[i].selected == true) {
            itemtext = dest.options[i].text;
            itemvalue = dest.options[i].value;
            source.options[source.options.length] = new Option(text = itemtext, value = itemvalue);
            dest.options[i] = null;
            i--
        }
    }
}

function UpdateSurvey() {
	tinyMCE.triggerSave();
    var i;
    $('[data-multidisplay="yes"]').each(function() {
		console.log("saving" + this.getAttribute("id"));
		for ( i = 0 ; i <this.length ; i++ ) {
			console.log("setting " + i + " selected");
			this.options[i].selected=true;
        }
	});
	return true;
}
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

	// get any questions that need programically create options as well as build array for the 'save'
	$sql = <<<EOD
		SELECT questionid, d.shortname, t.shortname as typename, min_value, max_value, ascending
		FROM SurveyQuestionConfig d
		JOIN SurveyQuestionTypes t USING (typeid)
        WHERE t.shortname != "heading" AND d.display_only = 0;
EOD;
	$result = mysqli_query_exit_on_error($sql);
	$shortname_types = [];
	while ($row = mysqli_fetch_assoc($result)) {
		$obj = new stdClass();
		$obj->questionid = $row["questionid"];
		$obj->shortname = $row["shortname"];
		$obj->id = str_replace(' ', '_', $row["shortname"]);
		$obj->typename = $row["typename"];
		$shortname_types[] = $obj;
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
	$PriorArray["shortname_types"] = json_encode($shortname_types);

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