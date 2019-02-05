<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant Suggestions';
$report['description'] = 'What did each participant suggest?';
$report['categories'] = array(
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, PS.paneltopics, PS.otherideas, PS.suggestedguests 
	FROM
	              Participants P
	         JOIN CongoDump CD USING (badgeid)
	    LEFT JOIN ParticipantSuggestions PS USING (badgeid)
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
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
                        <th class="report" style="white-space:nowrap;">Badge ID</th>
                        <th class="report">Name for Publications</th>
                        <th class="report">Panel Topics</th>
                        <th class="report">Other Ideas</th>
                        <th class="report">Guests</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <tr>
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report" style="white-space:nowrap;"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:value-of select="@paneltopics"/></td>
            <td class="report"><xsl:value-of select="@otherideas"/></td>
            <td class="report"><xsl:value-of select="@suggestedguests"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
