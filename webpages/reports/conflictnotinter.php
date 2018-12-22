<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - people on panels they are not interested in';
$report['description'] = 'Participants appear on this report only if they have deleted their interest after being assigned to the panel.  Note, this report includes only "Panels".';
$report['categories'] = array(
    'Conflict Reports' => 370,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, 
        P.pubsname, 
        S.sessionid,
        S.title,
        T.trackname
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN ParticipantOnSession POS USING (sessionid)
             JOIN Participants P USING (badgeid)
        LEFT JOIN ParticipantSessionInterest PSI USING (sessionid, badgeid)
    WHERE
            PSI.sessionid IS NULL
        AND S.typeid = 1 ## Panel
    ORDER BY
        T.trackname, S.sessionid;
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
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Pubsname</th>
                        <th class="report">Badge ID</th>
                    </tr>
                    <xsl:apply-templates select="/doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='participants']/row">
        <tr>
            <td class="report"><xsl:value-of select="@trackname"/></td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select="@sessionid"/></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
