<?php
// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
global $participant, $message, $message_error, $message2, $congoinfo, $title;
$title="My Photo";
require ('PartCommonCode.php'); // initialize db; check login;
//                                  set $badgeid from session
$queryArray["participant_info"] = <<<EOD
SELECT
	P.uploadedphotofilename, P.approvedphotofilename, P.photodenialreasonothertext, P.photodenialreasonid,
	CASE WHEN ISNULL(P.photouploadstatus) THEN 0 ELSE P.photouploadstatus END AS photouploadstatus,
	R.statustext, D.reasontext
FROM Participants P
LEFT OUTER JOIN PhotoDenialReasons D USING (photodenialreasonid)
LEFT OUTER JOIN PhotoUploadStatus R USING (photouploadstatus)
WHERE
    P.badgeid=?;
EOD;
$param_array["participant_info"] = array($badgeid);
$type_array["participant_info"] = "s";
if (($resultXML=mysql_prepare_query_XML($queryArray, $type_array, $param_array))===false) {
	RenderError($message_error);
	exit();
	}
$paramArray = array();
$paramArray['conName'] = CON_NAME;
$paramArray['defaultPhotoName'] = PHOTO_DEFAULT_IMAGE;
$paramArray['approvedPhotoURL'] = PHOTO_PUBLIC_DIRECTORY;
$paramArray['enablePhotos'] = PARTICIPANT_PHOTOS ? 1 : 0;
participant_header($title, false, 'Normal', true);
$resultXML = appendCustomTextArrayToXML($resultXML);
RenderXSLT('my_photo.xsl', $paramArray, $resultXML);
participant_footer();
?>
