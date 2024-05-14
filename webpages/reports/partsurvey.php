<?php
// Copyright (c) 2018-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant Survey Responses by Question';
$report['description'] = 'Show survey responses by question for each participant.';
$report['categories'] = array(
    'Participant Info Reports' => 705,
);
$report['columns'] = array(
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false)
);
$report['queries'] = [];
$report['queries']['questions'] =<<<'EOD'
SELECT questionid, shortname, prompt, typename, SUM(AnswerFound) AS Answered
	FROM (
	SELECT d.display_order, d.questionid, d.shortname, d.prompt, t.shortname as typename,
	CASE WHEN TRIM(IFNULL(a.value, "")) = "" THEN 0 ELSE 1 END AS AnswerFound
	FROM SurveyQuestionConfig d
	JOIN SurveyQuestionTypes t USING (typeid)
	LEFT OUTER JOIN ParticipantSurveyAnswers a ON (a.questionid = d.questionid)
) A
GROUP BY questionid, shortname, prompt, shortname
ORDER BY display_order ASC;
EOD;
$report['queries']['options'] =<<<'EOD'
SELECT questionid, ordinal, optionshort, SUM(AnswerFound) AS Answered
FROM (
	SELECT d.questionid, o.ordinal, o.value as optionshort,
		CASE WHEN TRIM(IFNULL(a.value, "")) = "" THEN 0 ELSE 1 END AS AnswerFound
	FROM SurveyQuestionConfig d
	JOIN SurveyQuestionOptionConfig o ON (d.questionid = o.questionid)
	LEFT OUTER JOIN ParticipantSurveyAnswers a ON
		(a.questionid = d.questionid AND a.value = o.value)
) A
GROUP BY questionid, ordinal, optionshort
ORDER BY questionid, ordinal ASC;
EOD;
$report['queries']['answers'] =<<<'EOD'
SELECT questionid, answer, othertext, participantid, badgename,
    CASE
        WHEN IFNULL(lastname, '') = '' THEN firstname
        WHEN IFNULL(firstname, '') = '' THEN lastname
        ELSE CONCAT(lastname, ', ', firstname)
    END AS FullName
FROM (
	SELECT d.questionid, a.value as answer, a.othertext, a.participantid, c.badgename, c.lastname, c.firstname,
		CASE WHEN TRIM(IFNULL(a.value, "")) = "" THEN 0 ELSE 1 END AS AnswerFound
	FROM SurveyQuestionConfig d
	LEFT OUTER JOIN ParticipantSurveyAnswers a ON (a.questionid = d.questionid)
    JOIN CongoDump c ON (c.badgeid = a.participantid)
) A
WHERE AnswerFound = 1
ORDER BY questionid ASC, badgename ASC;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="/doc/query[@queryName='questions']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Question</th>
                            <th class="report">Count</th>
                            <th class="report">Question/Respondent</th>
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
        <tr>
            <td class="report"><xsl:value-of select="@shortname" /></td>
            <td class="report"><xsl:value-of select="@Answered" /></td>
            <td class="report"><xsl:value-of select="@typename" /></td>
        </tr>
        <xsl:choose>
            <xsl:when test="contains(',hor-radio,vert-radio,single-pulldown,monthnum,monthabv,', concat(',',@typename,','))">
                <xsl:variable name="questionido" select="@questionid" />
                <xsl:apply-templates select="/doc/query[@queryName='options']/row[@questionid = $questionido]" />
            </xsl:when>
            <xsl:otherwise>
                <xsl:variable name="questionida" select="@questionid" />
                <xsl:apply-templates select="/doc/query[@queryName='answers']/row[@questionid = $questionida]" />
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='options']/row">
        <tr>
            <td class="report">&#160;</td>
            <td class="report"><xsl:value-of select="@Answered" /></td>
            <td class="report"><xsl:value-of select="@optionshort" /></td>
        </tr>
        <xsl:variable name="questionidoa" select="@questionid" />
        <xsl:apply-templates select="/doc/query[@queryName='answers']/row[@questionid = $questionidoa]" >
            <xsl:with-param name="answer" select="@optionshort" />
        </xsl:apply-templates>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='answers']/row">
        <xsl:param name="answer"/>
        <xsl:if test="@answer = $answer or $answer=''">
            <tr>
                <td class="report" colspan="2">&#160;</td>
                <td class="report"><xsl:value-of select="@badgename" /> (<xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@participantid"/></xsl:call-template>) <xsl:value-of select="@FullName" /></td>
                <td class="report"><xsl:value-of select="@answer" /></td>
                <td class="report"><xsl:value-of select="@othertext" /></td>
            </tr>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
EOD;
