<?php
// Copyright (c) 2015-2024 Peter Olszowka. All rights reserved. See copyright document for more details.

function encodeURIComponent($str) {
    $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
    return strtr(rawurlencode($str), $revert);
}

function getRoomsForScheduler() {
    global $message_error;
    $query=<<<EOD
SELECT
        roomid, roomname, display_order
    FROM
        Rooms
    WHERE
        is_scheduled = 1
    ORDER BY
        display_order;
EOD;
    if (($result = mysqli_query_exit_on_error($query)) === false) {
        RenderError($message_error);
        exit();
    }
    $roomsArr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $roomsArr[] = $row;
    }
    return encodeURIComponent(json_encode($roomsArr));
}

function getSessionSearchInfoForScheduler() {
    global $message_error;
    $query=<<<EOD
SELECT
        trackid, trackname, display_order
    FROM
        Tracks
    ORDER BY
        display_order;
EOD;
    if (($result = mysqli_query_exit_on_error($query)) === false) {
        RenderError($message_error);
        exit();
    }
    $tracksArr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $tracksArr[] = $row;
    }
    $query=<<<EOD
SELECT
        tagid, tagname, display_order
    FROM
        Tags
    ORDER BY
        display_order;
EOD;
    if (($result = mysqli_query_exit_on_error($query)) === false) {
        RenderError($message_error);
        exit();
    }
    $tagsArr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $tagsArr[] = $row;
    }
    $query=<<<EOD
SELECT
        typeid, typename, display_order
    FROM
        Types
    ORDER BY
        display_order;
EOD;
    if (($result = mysqli_query_exit_on_error($query)) === false) {
        RenderError($message_error);
        exit();
    }
    $typesArr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $typesArr[] = $row;
    }
    $query=<<<EOD
SELECT
        divisionid, divisionname, display_order
    FROM
        Divisions
    ORDER BY
        display_order;
EOD;
    if (($result = mysqli_query_exit_on_error($query)) === false) {
        RenderError($message_error);
        exit();
    }
    $divisionsArr = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $divisionsArr[] = $row;
    }

    $sessionSearchInfo = array();
    $sessionSearchInfo['tracks'] = $tracksArr;
    $sessionSearchInfo['tags'] = $tagsArr;
    $sessionSearchInfo['types'] = $typesArr;
    $sessionSearchInfo['divisions'] = $divisionsArr;
    return encodeURIComponent(json_encode($sessionSearchInfo));
}

function getConfigurationForScheduler() {
    $configuration = array();
    $configuration['trackTagUsage'] = TRACK_TAG_USAGE;
    $date = new DateTimeImmutable(CON_START_DATIM);
    $configuration['conStartDateTime'] = $date->format('c');
    return encodeURIComponent(json_encode($configuration));
}
