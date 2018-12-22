<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'All Sessions that are assigned';
$report['description'] = 'Who is assigned to what; shows scheduled and unscheduled sessions.';
$report['categories'] = array(
    'Programming Reports' => 140,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        T.trackname,
        S.sessionid,
        S.title,
        DATE_FORMAT(S.duration,'%i') as durationmin,
        DATE_FORMAT(S.duration,'%k') as durationhrs,
        SS.statusname
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
        LEFT JOIN Schedule SCH USING (sessionid)
    WHERE
           SCH.scheduleid IS NOT NULL
        OR S.statusid in (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        Trackname, S.sessionid;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        S.sessionid,
        P.pubsname,
        P.badgeid,
	POS.moderator
    FROM
             Sessions S
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
    ORDER BY
        S.sessionid,
        POS.moderator DESC,
        P.pubsname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table class="report">
                    <tr>
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report" style="width:6em">Duration</th>
                        <th class="report">Status</th>
                        <th class="report">Participants</th>
                    </tr>
                    <xsl:apply-templates select="/doc/query[@queryName='sessions']/row"/>
				</table>	
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='sessions']/row">
        <tr>
            <td class="report"><xsl:value-of select="@trackname"/></td>
            <td class="report">
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@statusname" /></td>
            <td class="report">
                <xsl:call-template name="participants">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                </xsl:call-template>
            </td>
        </tr>
    </xsl:template>
    <xsl:template name="participants">
        <xsl:param name="sessionid" />
        <xsl:variable name="participantCount" select="count(/doc/query[@queryName='participants']/row[@sessionid=$sessionid])" />
        <xsl:choose>
            <xsl:when test="$participantCount=0">
                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
                    <xsl:value-of select="@pubsname" />
                    <xsl:if test="@moderator='1'"> *MOD*</xsl:if>
                    (<xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template>)<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
EOD;
