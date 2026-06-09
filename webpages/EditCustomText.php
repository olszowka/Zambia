<?php
// Copyright (c) 2020-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-09-03
global $message_error, $title, $linki, $session;
$title = 'Edit Custom Text';
require_once('StaffCommonCode.php');
$message = "";
$rows = 0;
$textcontents = 'hidden-empty';
$selected = '';

staff_header($title, 'bs5');
if (isLoggedIn() && may_I('Administrator')) {
    if (isset($_POST['PostCheck'])) {
        $priorValues = interpretControlString($_POST['control'], $_POST['controliv']);

        if ($priorValues['getSessionID'] !=  session_id()) {
            $message = 'Session expired, no text updated';
        } else {
            $selected = $_POST['customtextid'];
            if ($selected == '-1') {
                $selected = '';
            }
            if ($selected != '') {

                $textcontents = $_POST['textcontents'];
                $active = $_POST['active'];
                $origcontents = $priorValues[$selected]['textcontents'];
                $origactive = $priorValues[$selected]['active'];

// scan for TinyMCE forcing addition of HTML wrapper and remove it
                if (($origcontents != $textcontents || $origactive != $active)) {
                    $query = <<<EOD
UPDATE CustomText
    SET textcontents = ?, active = ?
    WHERE customtextid = ?;
EOD;

                    $upd_array = array($textcontents, $active, $selected);
                    $rows = mysql_cmd_with_prepare($query, 'sii', $upd_array);
                    if (is_null($rows)) {
                        return;
                    }

                    if ($rows == 1) {
                        $message = 'Custom Text Updated';
                    } else {
                        $message = 'No changes to update-rows';
                    }
                } else {
                    $message = 'No changes to update-select';
                }
            } else {
                $message = 'No changes to update-unchanged';
            }
        }
    }

// Start of display portion
    $paramArray = array();

    $query=<<<EOD
SELECT
        customtextid, page, tag, textcontents, active, html_block_level
    FROM
            CustomText
    ORDER BY page ASC, tag ASC;
EOD;

    $result = mysqli_query_exit_on_error($query);
    $resultXML = mysql_result_to_XML('custom_text', $result);

    mysqli_data_seek($result, 0);
    $priorArray = array();
    $html_block_level = -1;
    while ($row = mysqli_fetch_assoc($result)) {
        $customTextRow = array();
        $customTextRow['textcontents'] = $row['textcontents'];
        $customTextRow['active'] = $row['active'];
        $PriorArray[$row['customtextid']] = $customTextRow;
        if ($selected == $row['customtextid']) {
            $html_block_level = $row['html_block_level'];
        }
    }
    mysqli_free_result($result);

    $PriorArray['getSessionID'] = session_id();

    $ControlStrArray = generateControlString($PriorArray);
    $paramArray['control'] = $ControlStrArray['control'];
    $paramArray['controliv'] = $ControlStrArray['controliv'];
    $paramArray['selected'] = $selected;
    $paramArray['initialtext'] = $textcontents;
    $paramArray['initialactive'] = $active;
    $paramArray['html_block_level'] = $html_block_level;

    if ($message != '') {
        $paramArray['UpdateMessage'] = $message;
    }

    // following line for debugging only
    // echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $resultXML->saveXML(), "i"));
    RenderXSLT('EditCustomText.xsl', $paramArray, $resultXML);
}
staff_footer();
?>
