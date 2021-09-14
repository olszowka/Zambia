<?php
// Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Interested Participants missing Survey Responses';
$report['description'] = 'List all interested participants who did not respond to the survey.';
$report['categories'] = array(
    'Participant Info Reports' => 702,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT CD.badgeid, P.pubsname
FROM CongoDump CD
JOIN Participants P ON (P.badgeid = CD.badgeid)
LEFT OUTER JOIN (
SELECT participantid, count(*) as answercount
FROM ParticipantSurveyAnswers
GROUP BY participantid
) a ON (a.participantid = CD.badgeid)
WHERE answercount IS NULL AND IFNULL(P.interested, 0) = 1;
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
                        <th class="report">No Survey Response</th>
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
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
