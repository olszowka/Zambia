<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Invited Guest Report';
$report['description'] = 'For each invited guest session, list the participants who have been invited. (and have not deleted the invitation.) Shows "NULL" if no one has been invited.';
$report['categories'] = array(
    'Programming Reports' => 620,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        T.trackname, S.sessionid, S.title
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
    WHERE
            T.selfselect = 1
        AND S.invitedguest = 1
        AND S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        T.display_order, S.sessionid;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        S.sessionid, PSI.badgeid, P.pubsname
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN ParticipantSessionInterest PSI USING (sessionid)
        JOIN Participants P USING (badgeid)
    WHERE
            T.selfselect = 1
        AND S.invitedguest = 1
        AND S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        S.sessionid, P.pubsname;
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
                        <th class="report">Participants</th>
                    </tr>
                    <xsl:apply-templates select="/doc/query[@queryName='schedule']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='schedule']/row">
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
            <xsl:when test="$participantCount=0">NULL</xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
                    <xsl:value-of select="@pubsname" />
                    (<xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template>)<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
EOD;
