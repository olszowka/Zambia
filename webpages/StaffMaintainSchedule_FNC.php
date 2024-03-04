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
