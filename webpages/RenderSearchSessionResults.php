<?php
// Copyright (c) 2005-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
function RenderSearchSessionResults($sessionSearchArray) {
    require_once('retrieve.php');
    require_once('render_functions.php');
    $title = 'Precis Search Results';
    // retrieveSessions() will exit on error
    if ($result = retrieveSessions($sessionSearchArray)) {
        staff_header($title, true);
        $showlinks = true; // Show links to edit sessions
        RenderPrecis($result, $showlinks);
        staff_footer();
        exit();
    }
}
?>
