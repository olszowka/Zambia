<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-12-15
global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Edit Survey";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;

staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Administrator")) {
// get default data options javascript
        $sql = <<<EOD
        SELECT t.shortname, -d.ordinal, d.value, d.display_order, d.optionshort, d.optionhover, d.allowothertext
        FROM SurveyQuestionTypeDefaults d
        JOIN SurveyQuestionTypes t USING (typeid)
        ORDER BY t.typeid, d.display_order;
EOD;
    $result = mysqli_query_exit_on_error($sql);

    $default_options = [];
    $cur_shortname = "";
    $cur_defaults = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $shortname = $row["shortname"];

        if ($shortname != $cur_shortname) {
            if ($cur_shortname != "") {
                $default_options[$cur_shortname] = $cur_defaults;
            }
            $cur_defaults = [];
            $cur_shortname = $shortname;
        }
        unset($row['shortname']);
        $cur_defaults[] = $row;
    }
    mysqli_free_result($result);

    $default_options[$cur_shortname] = $cur_defaults;
		echo "<script type=\"text/javascript\">\n";
    echo "var defaultOptions = " . json_encode($default_options) . ";\n";


// json of current questions and question options
	$paramArray = array();

    $query=<<<EOD
        SELECT d.questionid, d.shortname, d.description, prompt, hover, d.display_order, d.typeid, t.shortname AS typename, required, publish, privacy_user, searchable, ascending, display_only, min_value, max_value
        FROM SurveyQuestionConfig d
        JOIN SurveyQuestionTypes t USING (typeid)
        ORDER BY d.display_order ASC;
EOD;

    $result = mysqli_query_exit_on_error($query);
    $Config = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $Config[] = $row;
    }
    mysqli_free_result($result);
    echo "var survey = " . json_encode($Config) . ";\n";

    $query = <<<EOD
        SELECT questionid, display_order, ordinal, value, optionshort, optionhover, allowothertext, display_order
        FROM SurveyQuestionOptionConfig
        GROUP BY questionid, ordinal
        ORDER BY questionid, display_order;
EOD;
    $result = mysqli_query_exit_on_error($query);

    $survey_options = [];
    $cur_qid = "";
    $cur_config = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $qid = $row["questionid"];

        if ($qid != $cur_qid) {
            if ($cur_qid != "") {
                $survey_options[$cur_qid] = $cur_config;
            }
            $cur_config = [];
            $cur_qid = $qid;
        }
        $cur_config[] = $row;
    }
    mysqli_free_result($result);

    $survey_options[$cur_qid] = $cur_config;
    echo "var survey_options = " . json_encode($survey_options) . ";\n";
    echo "</script>";


	// start of display portion
	$query=<<<EOD
	SELECT
		typeid, shortname, description
	FROM SurveyQuestionTypes
	WHERE current = 1
	ORDER BY display_order ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$resultXML = mysql_result_to_XML("questiontypes", $result);
	mysqli_free_result($result);

	$PriorArray["getSessionID"] = session_id();

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	// following line for debugging only
	// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('EditSurvey.xsl', $paramArray, $resultXML);
}
staff_footer();
?>