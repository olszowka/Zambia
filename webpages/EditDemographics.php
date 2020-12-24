<?php
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-09-03
global $message_error, $title, $linki, $session;
$bootstrap4 = true;
$title = "Edit Demographics";
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;

staff_header($title, $bootstrap4);
if (isLoggedIn() && may_I("Administrator")) {
// get default data javascript
        $sql = <<<EOD
        SELECT t.shortname, JSON_ARRAYAGG(JSON_OBJECT(
            'ordinal', -d.ordinal,
            'value', d.value,
            'display_order', d.display_order,
            'optionshort', d.optionshort,
            'optionhover', d.optionhover,
            'allowothertext', d.allowothertext
            )) AS config
        FROM DemographicTypeDefaults d
		JOIN DemographicTypes t USING (typeid)
        GROUP BY d.typeid;
EOD;
        $result = mysqli_query_exit_on_error($sql);
		echo '<script type="text/javascript">' . "\n";
		echo "defaultOptions = {\n";

        while ($row = mysqli_fetch_assoc($result)) {
			$typename = $row["shortname"];
            $Config = $row["config"];
			echo $typename . ': "' . base64_encode($Config) . '",' . "\n";
        }
        mysqli_free_result($result);

        echo "};\n</script>\n";

// Start of display portion
	$paramArray = array();

	$query=<<<EOD
	WITH doc AS (
	SELECT demographicid, JSON_ARRAYAGG(JSON_OBJECT(
			'demographicid', demographicid,
            'ordinal', ordinal,
            'value', TO_BASE64(value),
			'optionshort', TO_BASE64(optionshort),
			'optionhover', TO_BASE64(optionhover),
			'allowothertext', allowothertext,
			'display_order', display_order
			)) AS optionconfig
		FROM DemographicOptionConfig
		GROUP BY demographicid
)
SELECT JSON_ARRAYAGG(JSON_OBJECT(
			'demographicid', d.demographicid,
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
			'min_value', min_value,
			'max_value', max_value,
            'options', TO_BASE64(CASE WHEN c.optionconfig IS NULL THEN "[]" ELSE c.optionconfig END)
			)) AS config
		FROM DemographicConfig d
		JOIN DemographicTypes t USING (typeid)
        LEFT OUTER JOIN doc c USING (demographicid)
		ORDER BY d.display_order ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$Config = "[]";
    while ($row = mysqli_fetch_assoc($result)) {
        $Config = $row["config"];
    }
	mysqli_free_result($result);

	if ($Config == "") {
		$Config = "[]";
    }

	$query=<<<EOD
	SELECT
		typeid, shortname, description
	FROM DemographicTypes
	WHERE current = 1
	ORDER BY display_order ASC;
EOD;

	$result = mysqli_query_exit_on_error($query);
	$resultXML = mysql_result_to_XML("demographictypes", $result);
	mysqli_free_result($result);

	$PriorArray["getSessionID"] = session_id();

	$ControlStrArray = generateControlString($PriorArray);
	$paramArray["control"] = $ControlStrArray["control"];
	$paramArray["controliv"] = $ControlStrArray["controliv"];
	$paramArray["config"] = $Config;

	if ($message != "") {
		$paramArray["UpdateMessage"] = $message;
    }

	// following line for debugging only
	// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
	RenderXSLT('EditDemographics.xsl', $paramArray, $resultXML);
}
staff_footer();
?>