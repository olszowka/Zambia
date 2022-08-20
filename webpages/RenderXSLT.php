<?php
// Created by Peter Olszowka on 2020-04-12;
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.

// function RenderXSLT($xslFilename, $paramArray, $xmlDoc)
// $xslFilename is a string which is a file in xsl directory containing an xsl stylesheet for transformation
// $paramArray(optional) array of name value pairs to be set as global parameters for xsl stylesheet
// $xmlDoc(optional) a Dom Document (XML) to be transformed.  If missing, an empty one will be transformed.
// Results of transformation are written immediately to the output.
function RenderXSLT($xslFilename, $paramArray = [], $xmlDoc = false, $noecho = false) {
    if (!$xmlDoc) {
        $xmlDoc = new DomDocument("1.0", "UTF-8");
        $emptyDoc = $xmlDoc->createElement("doc");
        $xmlDoc->appendChild($emptyDoc);
    }
    $xsl = new DomDocument;
    $xsl->load("xsl/$xslFilename");
    $xslt = new XsltProcessor();
    $xslt->importStylesheet($xsl);
    if (is_array($paramArray) && count($paramArray) > 0) {
        foreach ($paramArray as $paramName => $paramValue) {
            if (!is_null($paramValue)) {
                $xslt->setParameter('', $paramName, $paramValue);
            }
        }
    }
    $html = $xslt->transformToXML($xmlDoc);
    if ($noecho)
        return mb_ereg_replace("<(div|span|b|textarea|script)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i");

    echo(mb_ereg_replace("<(div|span|b|textarea|script)([^>]*/[ ]*)>", "<\\1\\2></\\1>", $html, "i"));
}
