<?php
//	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function staff_header($title, $is_report = false, $reportColumns = false, $reportAdditionalOptions = false) {
    global $fullPage, $header_used;
    $header_used = HEADER_STAFF;
    html_header($title);
    $isLoggedIn = isLoggedIn();
    if ($fullPage) {
?>
<body class ="fullPage">
    <div id="fullPageContainer" class="container-fluid">
        <div id="myhelper"></div><!-- used for drag-and-drop operations -->
        <div id="headerContainer">
<?php
        commonHeader('Staff', $isLoggedIn, false, 'Normal');
?>
        </div>
<?php
    } else {
?>
<body>
    <div class="container-fluid">
<?php
        commonHeader('Staff', $isLoggedIn, false, 'Normal');
    }
}
