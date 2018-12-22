<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Pubs - Who is on Which Session';
$report['description'] = 'Show the badgeid, pubsname and session info for each participant that are on at least one scheduled session. (Limited to published sessions.)';
$report['categories'] = array(
    'Publication Reports' => 130,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
    WHERE
        EXISTS (
            SELECT SCH.sessionid
                FROM 
                         Schedule SCH
                    JOIN ParticipantOnSession POS USING (sessionid)
                    JOIN Sessions S USING (sessionid)
                WHERE
                        POS.badgeid = P.badgeid
                    AND S.pubstatusid = 2 /* Published */
            )
    ORDER BY
         IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
         CD.firstname;
EOD;
$report['queries']['sessions'] =<<<'EOD'
SELECT
        POS.badgeid, POS.moderator, POS.sessionid
    FROM
             ParticipantOnSession POS
        JOIN Schedule SCH USING (sessionid)
        JOIN Sessions S USING (sessionid)
    WHERE
        S.pubstatusid = 2 /* Published */
    ORDER BY
        POS.badgeid, POS.sessionid;
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
                        <th class="report" style="">Badge ID</th>
                        <th class="report" style="">Name for Publications</th>
                        <th class="report" style="">List of Sessions</th>
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
            <td class="report"><xsl:value-of select="@badgeid" /></td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:apply-templates select="/doc/query[@queryName='sessions']/row[@badgeid=$badgeid]" />
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
        <xsl:if test="position() > 1">
            <xsl:text>, </xsl:text>
        </xsl:if>
        <xsl:value-of select="@sessionid" />
        <xsl:if test="@moderator='1'">
            <xsl:text> MOD</xsl:text>
        </xsl:if>        
    </xsl:template>
</xsl:stylesheet>
EOD;
