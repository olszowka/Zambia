<?php
// Copyright (c) 2018-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant Survey Responses by Question';
$report['description'] = 'Show survey responses by particpant for each question.';
$report['categories'] = array(
    'Participant Info Reports' => 705,
);
$report['queries'] = [];
$report['queries']['questions'] =<<<'EOD'
SELECT
        SQC.shortname, SQT.shortname as typename
    FROM
             SurveyQuestionConfig SQC
        JOIN SurveyQuestionTypes SQT USING (typeid)
    ORDER BY
        SQC.display_order;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, CD.firstname, CD.lastname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid);
EOD;
$report['queries']['answers'] =<<<'EOD'
SELECT
        PSR.badgeid, SQC.shortname,
        JSON_UNQUOTE(JSON_EXTRACT(PSR.answers, CONCAT('$.', SQC.shortname, '.value'))) AS value,
        JSON_UNQUOTE(JSON_EXTRACT(PSR.answers, CONCAT('$.', SQC.shortname, '.othertext'))) AS othertext
    FROM
                   SurveyQuestionConfig SQC
        CROSS JOIN ParticipantSurveyResponses PSR
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
            <xsl:when test="/doc/query[@queryName='questions']/row">
                <table class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Question</th>
                            <th class="report">Count</th>
                            <th class="report">Badge ID</th>
                            <th class="report">Pubs Name</th>
                            <th class="report">First Name</th>
                            <th class="report">Last Name</th>
                            <th class="report">Answer</th>
                            <th class="report">Other Text</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="/doc/query[@queryName='questions']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No survey results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='questions']/row">
        <xsl:variable name="shortname" select="@shortname" />
        <tr>
            <td class="report"><xsl:value-of select="$shortname" /></td>
            <td class="report"><xsl:value-of select="count(/doc/query[@queryName='answers']/row[@shortname=$shortname]/@value)" /></td>
            <td class="report" ><xsl:value-of select="@typename" /></td>
            <td class="report" colspan="5">&#160;</td>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='answers']/row[@shortname = $shortname]" />
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='answers']/row">
        <xsl:if test="@value">
            <xsl:variable name="badgeid" select="@badgeid" />
            <tr>
                <td class="report" colspan="2">&#160;</td>
                <xsl:apply-templates select="/doc/query[@queryName='participants']/row[@badgeid = $badgeid]" />
                <td class="report"><xsl:value-of select="@value" /></td>
                <td class="report"><xsl:value-of select="@othertext" /></td>
            </tr>
        </xsl:if>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='participants']/row">
        <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
        <td class="report"><xsl:value-of select="@pubsname" /></td>
        <td class="report"><xsl:value-of select="@firstname" /></td>
        <td class="report"><xsl:value-of select="@lastname" /></td>
    </xsl:template>
</xsl:stylesheet>
EOD;
