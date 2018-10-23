<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function RenderSearchSessionResults($trackidlist, $statusidlist, $typeidlist, $sessionid, $divisionid, $searchtitle) {
    require_once('retrieve.php');
    require_once('render_functions.php');
    $title = 'Precis Search Results';
    // retrieve_select_from_db() will exit on error
    if ($result = retrieve_select_from_db($trackidlist, $statusidlist, $typeidlist, $sessionid, $divisionid, $searchtitle)) {
        staff_header($title);
        $showlinks = true; // Show links to edit sessions
        RenderPrecis($result, $showlinks);
        staff_footer();
        exit();
    }
}
?>
