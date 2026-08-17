<?php
// Copyright (c) 2022-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka 2022-10-08
$report = [];
$report['name'] = 'Panel ideas from participant survey';
$report['description'] = 'Shows all panels ideas entered into the participant survey.';
$report['categories'] = array(
    'Participant Info Reports' => 300,
);
$report['columns'] = array(
    array(),
    array("width" => "13em"),
    array("width" => "11em"),
    array(),
    array(),
    array(),
);
$report['queries'] = [];
$report['queries']['ideas'] =<<<'EOD'
SELECT
        JSON_VALUE(PSR.answers, '$.ideas.value') AS ideas, PSR.lastupdate, P.badgeid, P.pubsname, CD.firstname,
        CD.lastname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantSurveyResponses PSR USING (badgeid)
    WHERE
        IFNULL(JSON_VALUE(PSR.answers, '$.ideas.value'), '') != ''
    ORDER BY
        lastupdate;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='ideas']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Ideas</th>
                            <th class="report">Badge ID</th>
                            <th class="report">Pubs Name</th>
                            <th class="report">First Name</th>
                            <th class="report">Last Name</th>
                            <th class="report">Date Updated</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="/doc/query[@queryName='ideas']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='ideas']/row">
        <tr>
            <td class="report"><xsl:value-of select="@ideas"/></td>
            <td class="report"><xsl:value-of select="@badgeid"/></td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:value-of select="@firstname"/></td>
            <td class="report"><xsl:value-of select="@lastname"/></td>
            <td class="report"><xsl:value-of select="@lastupdate"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
