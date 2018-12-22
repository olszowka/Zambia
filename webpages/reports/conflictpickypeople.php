<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Picky people ';
$report['description'] = 'Show who the picky people do not want to be on a panel with and who they are on panels with.';
$report['categories'] = array(
    'Conflict Reports' => 430,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, PI.nopeople, S.sessionid, S.title, T.trackname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantInterests PI USING (badgeid)
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid)
        JOIN Tracks T USING (trackid)
    WHERE
            S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
        AND P.interested = 1
        AND IFNULL(PI.nopeople, "") != ""
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;
$report['queries']['otherParticipants'] =<<<'EOD'
SELECT
        P.badgeid, POS.sessionid, POS2.badgeid AS obadgeid, P2.pubsname
    FROM
             Participants P
        JOIN ParticipantInterests PI ON P.badgeid = PI.badgeid
        JOIN ParticipantOnSession POS ON P.badgeid = POS.badgeid
        JOIN Sessions S ON POS.sessionid = S.sessionid
        JOIN ParticipantOnSession POS2 ON S.sessionid = POS2.sessionid
        JOIN Participants P2 ON POS2.badgeid = P2.badgeid
    WHERE
            S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
        AND P.interested = 1
        AND IFNULL(PI.nopeople, "") != ""
        AND P.badgeid != P2.badgeid;
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
                    <col style="width:6em;" />
                    <col style="width:12em;" />
                    <col />
                    <col style="width:7em;" />
                    <col style="width:6em;" />
                    <col style="width:15em;" />
                    <col />
                    <tr>
                        <th class="report">Badge ID</th>
                        <th class="report">Name for publications</th>
                        <th class="report">People to avoid</th>
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Other participants</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@nopeople" /></td>
            <td class="report"><xsl:value-of select="@trackname" /></td>
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
                <xsl:variable name="badgeid" select="@badgeid" />
                <xsl:variable name="sessionid" select="@sessionid" />
                <xsl:apply-templates select="/doc/query[@queryName='otherParticipants']/row[@badgeid = $badgeid and @sessionid = $sessionid]" />      
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='otherParticipants']/row">
        <xsl:if test="position() != 1"><xsl:text>, </xsl:text></xsl:if>
        <xsl:call-template name="showPubsnameWithBadgeid">
            <xsl:with-param name="badgeid" select = "@obadgeid" />
            <xsl:with-param name="pubsname" select = "@pubsname" />
        </xsl:call-template>
    </xsl:template>
</xsl:stylesheet>
EOD;
