<?php
    global $participant,$message,$message_error,$message2,$congoinfo;
    $title="My Profile";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    $queryArray["participant_info"]=<<<EOD
SELECT
		CD.badgeid, CD.firstname, CD.lastname, CD.badgename, CD.phone, CD.email,
			CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip,
			CD.postcountry, P.pubsname, P.password, P.bestway, P.interested, P.bio, 
			P.share_email 
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
	    RenderError($title,$message_error);
        exit();
        }
	$optionsNode = $resultXML->createElement("options");
	$docNode = $resultXML->getElementsByTagName("doc")->item(0);
	$optionsNode = $docNode->appendChild($optionsNode);
	$optionsNode->setAttribute("conName",CON_NAME);
	if (ENABLE_SHARE_EMAIL_QUESTION)
		$optionsNode->setAttribute("enable_share_email_question",1);
	if (ENABLE_BESTWAY_QUESTION)
		$optionsNode->setAttribute("enable_bestway_question",1);
	$optionsNode->setAttribute("maxBioLen",MAX_BIO_LEN);
	$optionsNode->setAttribute("enableBioEdit",may_I('EditBio'));
	participant_header($title);
	$resultXML = appendCustomTextArrayToXML($resultXML);
	echo($resultXML->saveXML());
	$xsl = new DomDocument;
	$xsl->load('xsl/my_profile.xsl');
	$xslt = new XsltProcessor();
	$xslt->importStylesheet($xsl);
	$html = $xslt->transformToXML($resultXML);
	echo(mb_ereg_replace("!<(div|iframe|script|textarea)([^>]*?)/>!s", "<$1$2></$1>", $html));
	// some browsers does not support empty div, iframe, script and textarea tags
	participant_footer();
?>
