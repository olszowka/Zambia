<?php
// Copyright (c) 2021-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Interested Participants missing Survey Responses';
$report['description'] = 'List all interested participants who did not respond to the survey.';
$report['categories'] = array(
    'Participant Info Reports' => 702,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        CD.badgeid, CD.firstname, CD.lastname, P.pubsname
    FROM
                  CongoDump CD
             JOIN Participants P USING (badgeid)
        LEFT JOIN ParticipantSurveyResponses PSR USING (badgeid)
    WHERE
            IFNULL(JSON_LENGTH(PSR.answers), 0) = 0
        AND P.interested = 1;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table class="report">
                    <tr>
                        <th class="report">Badge ID</th>
                        <th class="report">Name for Publications</th>
                        <th class="report">First Name</th>
                        <th class="report">Last Name</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No participants have not responded to the survey.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td class="report"><xsl:value-of select="@badgeid" /></td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@lastname" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
