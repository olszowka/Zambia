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
        SELECT t.shortname, d.display_order, JSON_OBJECT(
            'ordinal', -d.ordinal,
            'value', TO_BASE64(d.value),
            'display_order', d.display_order,
            'optionshort', TO_BASE64(d.optionshort),
            'optionhover', TO_BASE64(d.optionhover),
            'allowothertext', d.allowothertext
            ) AS config
        FROM SurveyQuestionTypeDefaults d
		JOIN SurveyQuestionTypes t USING (typeid)
		ORDER BY t.typeid, d.display_order;
EOD;
        $result = mysqli_query_exit_on_error($sql);
		echo '<script type="text/javascript">' . "\n";
		echo "defaultOptions = {\n";

		$cur_typename = "";
		$cur_config = "[";

        while ($row = mysqli_fetch_assoc($result)) {
			$typename = $row["shortname"];
            $config = $row["config"];

			if ($typename != $cur_typename) {
                if ($cur_typename != "") {
                    echo $cur_typename . ': "' . base64_encode($cur_config . "]") . '",' . "\n";
                }
				$cur_config = "[";
                $cur_typename = $typename;
            }
			$cur_config = $cur_config . $config . ",\n";
        }
        mysqli_free_result($result);
		echo $typename . ': "' . base64_encode(mb_substr($cur_config, 0, -2) . "]") . '"' . "\n};\n";

// json of current questions and question options
	$paramArray = array();

	$query=<<<EOD
		SELECT JSON_OBJECT(
			'questionid', d.questionid,
			'shortname', d.shortname,
			'description', d.description,
			'prompt', prompt,
			'hover', hover,
			'display_order', d.display_order,
			'typeid', d.typeid,
			'typename', t.shortname,
			'required', required,
			'publish', publish,
			'privacy_user', privacy_user,
			'searchable', searchable,
			'ascending', ascending,
			'display_only', display_only,
			'min_value', min_value,
			'max_value', max_value,
			'options', ""
			) AS config
		FROM SurveyQuestionConfig d
		JOIN SurveyQuestionTypes t USING (typeid)
		ORDER BY d.display_order ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$Config = "var survey = [\n\t";
    while ($row = mysqli_fetch_assoc($result)) {
        $Config = $Config . "\t" . $row["config"] . ",\n";
    }
	$Config = $Config . "\n];\n";
	mysqli_free_result($result);
	echo $Config;

    $query = <<<EOD
	SELECT questionid, display_order, JSON_OBJECT(
		'questionid', questionid,
        'ordinal', ordinal,
        'value', TO_BASE64(value),
		'optionshort', TO_BASE64(optionshort),
		'optionhover', TO_BASE64(optionhover),
		'allowothertext', allowothertext,
		'display_order', display_order
		) AS optionconfig
	FROM SurveyQuestionOptionConfig
	GROUP BY questionid, ordinal
	ORDER BY questionid, display_order;
EOD;
	$result = mysqli_query_exit_on_error($query);
	echo "var survey_options = {\n";

	$cur_qid = "";
    $cur_config = "[";

    while ($row = mysqli_fetch_assoc($result)) {
        $qid = $row["questionid"];
        $config = $row["optionconfig"];

        if ($qid != $cur_qid) {
            if ($cur_qid != "") {
                echo $cur_qid . ': "' . base64_encode(mb_substr($cur_config, 0, -2) . "]") . '",' . "\n";
            }
            $cur_config = "[";
            $cur_qid = $qid;
        }
        $cur_config = $cur_config . $config . ",\n";
    }
    mysqli_free_result($result);

	echo $cur_qid . ': "' . base64_encode(mb_substr($cur_config, 0, -2) . "]") . '"' . "\n";
	echo "};\n</script>\n";

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