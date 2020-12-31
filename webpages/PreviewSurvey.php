<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-12-29

global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Preview Survey";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;

staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Administrator")) {
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
            CASE WHEN SUM(o.allowothertext) > 0 THEN 1 ELSE 0 END AS allowothertext
		FROM SurveyQuestionConfig d
		JOIN SurveyQuestionTypes t USING (typeid)
        LEFT OUTER JOIN SurveyQuestionOptionConfig o USING (questionid)
        GROUP BY d.questionid
		ORDER BY d.display_order ASC;
EOD;

	$query["options"] = <<<EOD
		SELECT questionid, display_order, ordinal, value, optionshort, optionhover, allowothertext, display_order
		FROM SurveyQuestionOptionConfig
		ORDER BY questionid, display_order;
EOD;
	$resultXML = mysql_query_XML($query);

	// add javascript to enable tooltips and mce editing
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
  $('[data-othertextselect="1"]').each(function () {
        SelectChangeOthertext(this);
    });
});

</script>
EOD;
	// get any questions that need programically create options
	$sql = <<<EOD
		SELECT d.questionid, t.shortname as typename, min_value, max_value, ascending
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

	$paramArray["buttons"] = "refresh";

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	// following line for debugging only
	//echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('RenderSurvey.xsl', $paramArray, $resultXML);
}
staff_footer();
?>