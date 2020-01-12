<?php
//	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function commonHeader($headerVersion, $isLoggedIn, $noUserRequired, $loginPageStatus) {
    if ($isLoggedIn && $loginPageStatus = 'Normal' && !may_I("Participant") && !may_I("Staff")) {
        $loginPageStatus = 'No_Permission';
    }
    $xml = new DomDocument("1.0", "UTF-8");
    $emptyDoc = $xml->createElement("doc");
    $xml->appendChild($emptyDoc);
    $xsl = new DomDocument;
    $xsl->load('xsl/GlobalHeader.xsl');
    $xslt = new XsltProcessor();
    $xslt->importStylesheet($xsl);
    $xslt->setParameter('', 'header_version', $headerVersion);
    $xslt->setParameter('', 'logged_in', $isLoggedIn);
    $xslt->setParameter('', 'login_page_status', $loginPageStatus);
    $xslt->setParameter('', 'no_user_required', $noUserRequired);
    $xslt->setParameter('', 'CON_NAME', CON_NAME);
    $xslt->setParameter('', 'badgename', isset($_SESSION['badgename']) ? $_SESSION['badgename'] : '');
    $xslt->setParameter('', 'USER_ID_PROMPT', USER_ID_PROMPT);
    $html = $xslt->transformToXML($xml);
    echo(mb_ereg_replace("<(div|iframe|script|textarea)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
}

