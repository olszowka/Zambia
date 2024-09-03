<?php
// Created by Peter Olszowka on 2022-10-01;
// Copyright (c) 2022-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
$title = "Declined to Invite";
require_once('PartCommonCode.php');
populateCustomTextArray(); // title changed above, reload custom text with the proper page title
participant_header($title, false, 'No_Menu', 'bs4');
echo "<div style=\"margin-top: 2rem\">&nbsp;</div>\n";
echo fetchCustomText('declined_particpant');
participant_footer();
