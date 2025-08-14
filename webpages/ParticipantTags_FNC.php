<?php
// Created by Peter Olszowka on 2023-07-03;
// Copyright (c) 2023 Peter Olszowka. All rights reserved. See copyright document for more details.
// If $forSearch is false or omitted,
//     the results are written to the (ajax) output and are rendered as read only if user doesn't have edit permissions
//     also, initial checkbox state will correspond to query param badgeid
// If $forSearch is true
//     the results are returned and always rendered to be writeable and initial checkbox state always false
function fetch_participant_tags($forSearch = false) {
    global $message_error;
    if ($forSearch) {
        $fetchedUserBadgeId = '-1';
    } else {
        $fetchedUserBadgeId = getString('badgeid');
        if (empty($fetchedUserBadgeId)) {
            $message_error = "Internal error.";
            RenderErrorAjax($message_error);
            exit();
        }

    }
    $query = <<<EOD
SELECT
        PT.participanttagname, PT.participanttagid, PHT.badgeid
    FROM
                  ParticipantTags PT
        LEFT JOIN ParticipantHasTag PHT ON
                PHT.badgeid = ?
            AND PT.participanttagid = PHT.participanttagid
    ORDER BY
        IF(ISNULL(PHT.badgeid), 1, 0), PT.display_order;
EOD;
    $resultXML = mysql_prepare_query_XML(
        array("participant_tags" => $query),
        array("participant_tags" => "s"),
        array("participant_tags" => array($fetchedUserBadgeId))
    );
    if (!$resultXML) {
        RenderErrorAjax($message_error);
        exit();
    }
    //error_log(__FILE__.":".__LINE__.": ".$resultXML->saveXML());
    $paramArray = array();
    if ($forSearch) {
        $paramArray['edit_participant_tags'] = true;
        return RenderXSLT('FetchParticipantTags.xsl', $paramArray, $resultXML, true);
    } else {
        $paramArray['edit_participant_tags'] = may_I('edit_participant_tags');
        RenderXSLT('FetchParticipantTags.xsl', $paramArray, $resultXML);
    }
}
