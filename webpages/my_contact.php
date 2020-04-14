<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
    global $participant, $message, $message_error, $message2, $congoinfo, $title;
    $title="My Profile";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    $queryArray["participant_info"]=<<<EOD
SELECT
		CD.badgeid, CD.firstname, CD.lastname, CD.badgename, CD.phone, CD.email,
			CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
			CD.postcountry, P.pubsname, P.password, P.bestway, P.interested, P.bio, 
			P.share_email, P.use_photo 
	FROM
			CongoDump CD
	   JOIN Participants P USING (badgeid) 
    WHERE
        CD.badgeid="$badgeid";
EOD;
	$queryArray["credentials"]=<<<EOD
SELECT
		CR.credentialid, CR.credentialname, CR.display_order, PHC.badgeid
	FROM
			Credentials CR
	   LEFT JOIN ParticipantHasCredential PHC ON CR.credentialid = PHC.credentialid
			AND PHC.badgeid="$badgeid";
EOD;
	if (($resultXML=mysql_query_XML($queryArray))===false) {
	    RenderError($message_error);
        exit();
        }
	$optionsNode = $resultXML->createElement("options");
	$docNode = $resultXML->getElementsByTagName("doc")->item(0);
	$optionsNode = $docNode->appendChild($optionsNode);
	$optionsNode->setAttribute("conName", CON_NAME);
	if (ENABLE_SHARE_EMAIL_QUESTION === TRUE)
		$optionsNode->setAttribute("enable_share_email_question", 1);
	if (ENABLE_USE_PHOTO_QUESTION === TRUE)
		$optionsNode->setAttribute("enable_use_photo_question", 1);
	if (ENABLE_BESTWAY_QUESTION === TRUE)
		$optionsNode->setAttribute("enable_bestway_question", 1);
	$optionsNode->setAttribute("maxBioLen", MAX_BIO_LEN);
	$optionsNode->setAttribute("enableBioEdit", may_I('EditBio'));
	$optionsNode->setAttribute("reg_url", REG_URL);
	participant_header($title);
	$resultXML = appendCustomTextArrayToXML($resultXML);
	RenderXSLT('my_profile.xsl', array(), $resultXML);
	participant_footer();
?>
