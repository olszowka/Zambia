<?php
// Copyright (c) 2020-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// File created by Syd Weinstein on 2020-09-03
global $message_error, $title, $linki, $session;
$title = "Edit Configuration Tables";
require_once('StaffCommonCode.php');

$paramArray = array();

$mayEditAll = may_I('EditAnyConfigurationTable');

// $mayEditBioEditStatuses = may_I('ce_BioEditStatuses') || $mayEditAll; // BioEditStatuses not currently implemented
$mayEditCredentials = may_I('ce_Credentials') || $mayEditAll;
$mayEditParticipantTags = may_I('ce_ParticipantTags') || $mayEditAll;
$mayEditPhotoDenialReasons = may_I('ce_PhotoDenialReasons') || $mayEditAll;
$mayEditRoles = may_I('ce_Roles') || $mayEditAll;
$mayEditTimes = may_I('ce_Times') || $mayEditAll;
$mayEditAnyParticipantTables = /* $mayEditBioEditStatuses || */ $mayEditCredentials || $mayEditParticipantTags ||
    $mayEditPhotoDenialReasons || $mayEditRoles || $mayEditTimes;

$mayEditDivisions = may_I('ce_Divisions') || $mayEditAll;
$mayEditKidsCategories = may_I('ce_KidsCategories') || $mayEditAll;
// $mayEditLanguageStatuses = may_I('ce_LanguageStatuses') || $mayEditAll; // LanguageStatuses not currently implemented
$mayEditPubStatuses = may_I('ce_PubStatuses') || $mayEditAll;
$mayEditServices = may_I('ce_Services') || $mayEditAll;
$mayEditSessionStatuses = may_I('ce_SessionStatuses') || $mayEditAll;
$mayEditTags = may_I('ce_Tags') || $mayEditAll;
$mayEditTracks = may_I('ce_Tracks') || $mayEditAll;
$mayEditTypes = may_I('ce_Types') || $mayEditAll;
$mayEditAnySessionTables = $mayEditDivisions || $mayEditKidsCategories || /* $mayEditLanguageStatuses || */
    $mayEditPubStatuses || $mayEditServices || $mayEditSessionStatuses || $mayEditTags ||
    $mayEditTracks || $mayEditTypes;

$mayEditEmailCC = may_I('ce_EmailCC') || $mayEditAll;
$mayEditEmailFrom = may_I('ce_EmailFrom') || $mayEditAll;
$mayEditEmailTo = may_I('ce_EmailTo') || $mayEditAll;
$mayEditAnyEmailTables = $mayEditEmailCC || $mayEditEmailFrom || $mayEditEmailTo;

$mayEditFeatures = may_I('ce_Features') || $mayEditAll;
$mayEditRooms = may_I('ce_Rooms') || $mayEditAll;
$mayEditRoomSets = may_I('ce_RoomSets') || $mayEditAll;
$mayEditRoomHasSet = may_I('ce_RoomHasSet') || $mayEditAll;
$mayEditAnyFacilityTables = $mayEditFeatures || $mayEditRooms || $mayEditRoomSets || $mayEditRoomHasSet;

$mayEditRegTypes = may_I('ce_RegTypes') || $mayEditAll;
$mayEditAnyOtherTables = $mayEditRegTypes;

$editAnyTable = $mayEditAnyParticipantTables || $mayEditAnySessionTables || $mayEditAnyEmailTables ||
    $mayEditAnyFacilityTables || $mayEditAnyOtherTables;

if (!$editAnyTable) {
    $message_error = "You do not currently have permission to view this page.<br>\n";
    StaffRenderErrorPage($title, $message_error, 'bs5');
    exit();
}
staff_header($title, 'bs5');

$PriorArray["getSessionID"] = session_id();

$ControlStrArray = generateControlString($PriorArray);
$paramArray = array();
$paramArray["control"] = $ControlStrArray["control"];
$paramArray["controliv"] = $ControlStrArray["controliv"];

$paramArray["mayEditAll"] = $mayEditAll;

// $paramArray["mayEditBioEditStatuses"] = $mayEditBioEditStatuses; // BioEditStatuses not currently implemented
$paramArray["mayEditCredentials"] = $mayEditCredentials;
$paramArray["mayEditParticipantTags"] = $mayEditParticipantTags;
$paramArray["mayEditPhotoDenialReasons"] = $mayEditPhotoDenialReasons;
$paramArray["mayEditRoles"] = $mayEditRoles;
$paramArray["mayEditTimes"] = $mayEditTimes;
$paramArray["mayEditAnyParticipantTables"] = $mayEditAnyParticipantTables;

$paramArray["mayEditDivisions"] = $mayEditDivisions;
$paramArray["mayEditKidsCategories"] = $mayEditKidsCategories;
// $paramArray["mayEditLanguageStatuses"] = $mayEditLanguageStatuses; // LanguageStatuses not currently implemented
$paramArray["mayEditPubStatuses"] = $mayEditPubStatuses;
$paramArray["mayEditServices"] = $mayEditServices;
$paramArray["mayEditSessionStatuses"] = $mayEditSessionStatuses;
$paramArray["mayEditTags"] = $mayEditTags;
$paramArray["mayEditTracks"] = $mayEditTracks;
$paramArray["mayEditTypes"] = $mayEditTypes;
$paramArray["mayEditAnySessionTables"] = $mayEditAnySessionTables;

$paramArray["mayEditEmailCC"] = $mayEditEmailCC;
$paramArray["mayEditEmailFrom"] = $mayEditEmailFrom;
$paramArray["mayEditEmailTo"] = $mayEditEmailTo;
$paramArray["mayEditAnyEmailTables"] = $mayEditAnyEmailTables;

$paramArray["mayEditFeatures"] = $mayEditFeatures;
$paramArray["mayEditRooms"] = $mayEditRooms;
$paramArray["mayEditRoomSets"] = $mayEditRoomSets;
$paramArray["mayEditRoomHasSet"] = $mayEditRoomHasSet;
$paramArray["mayEditAnyFacilityTables"] = $mayEditAnyFacilityTables;

$paramArray["mayEditRegTypes"] = $mayEditRegTypes;
$paramArray["mayEditAnyOtherTables"] = $mayEditAnyOtherTables;

// echo(mb_ereg_replace("<(query|row)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $xmlDoc->saveXML(), "i"));
RenderXSLT('ConfigTableEditor.xsl', $paramArray);

staff_footer();
?>
