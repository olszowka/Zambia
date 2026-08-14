<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka 13 Aug 2026
$report = [];
$report['name'] = 'Participant Survey Responses by Participant';
$report['description'] = 'Show survey responses by question for each participant.';
$report['categories'] = array(
    'Participant Info Reports' => 706,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, CD.firstname, CD.lastname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantSurveyResponses PSR USING (badgeid)
    WHERE
        JSON_LENGTH(PSR.answers) > 0;
EOD;
$report['queries']['answers'] =<<<'EOD'
SELECT
        PSR.badgeid, SQC.shortname,
        JSON_UNQUOTE(JSON_EXTRACT(PSR.answers, CONCAT('$.', SQC.shortname, '.value'))) AS value,
        JSON_UNQUOTE(JSON_EXTRACT(PSR.answers, CONCAT('$.', SQC.shortname, '.othertext'))) AS othertext
    FROM
        SurveyQuestionConfig SQC, ParticipantSurveyResponses PSR
    WHERE
        SQC.display_only = 0;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="/doc/query[@queryName='participants']/row">
                <table class="report">
                    <col/><col/><col/><col/><col style="width:65%"/><col/>
                    <thead>
                        <tr>
                            <th class="report" rowspan="2">Badge ID</th>
                            <th class="report">Pubs Name</th>
                            <th class="report">First Name</th>
                            <th class="report">Last Name</th>
                            <th class="report">&#160;</th>
                            <th class="report">&#160;</th>
                        </tr>
                        <tr>
                            <th class="report">Question</th>
                            <th class="report" colspan="3">Answer</th>
                            <th class="report">Other Text</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="/doc/query[@queryName='participants']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No survey results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='participants']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <tr>
            <td class="report" rowspan="{1 + count(/doc/query[@queryName='answers']/row[@badgeid = $badgeid]/@value)}">
                <xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template>
            </td>
            <td class="report" ><xsl:value-of select="@pubsname" /></td>
            <td class="report" ><xsl:value-of select="@firstname" /></td>
            <td class="report" ><xsl:value-of select="@lastname" /></td>
            <td class="report" colspan="2">&#160;</td>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='answers']/row[@badgeid = $badgeid]" />
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='answers']/row">
        <xsl:if test="@value">
            <tr>
                <td class="report"><xsl:value-of select="@shortname" /></td>
                <td class="report" colspan="3"><xsl:value-of select="@value" /></td>
                <td class="report"><xsl:value-of select="@othertext" /></td>
            </tr>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
EOD;
