<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Assigned Session by Participant';
$report['description'] = 'Shows who has been assigned to each session ordered by participant. Includes scheduled and unscheduled sessions.';
$report['categories'] = array(
//  'Events Reports' => 150,
    'Programming Reports' => 150,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT DISTINCT
        P.badgeid
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;
$report['queries']['sessions'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, S.sessionid, S.title, POS.moderator
    FROM
             Participants P
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        P.badgeid
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
                        <th class="report">Badgeid</th>
                        <th class="report">Pubsname</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Moderator ?</th>
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
        <xsl:variable name="rowCount" select="count(/doc/query[@queryName='sessions']/row[@badgeid=$badgeid])" />
	    <xsl:for-each select="/doc/query[@queryName='sessions']/row[@badgeid=$badgeid]">
            <tr class="report">
                <xsl:choose>
                    <xsl:when test="position() = 1">
                        <td rowspan="{$rowCount}" class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showBadgeid">
                                <xsl:with-param name="badgeid" select = "@badgeid" />
                            </xsl:call-template>
                        </td>
                        <td rowspan="{$rowCount}" class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showPubsname">
                                <xsl:with-param name="badgeid" select = "@badgeid" />
                                <xsl:with-param name="pubsname" select = "@pubsname" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showSessionid">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showSessionTitle">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                                <xsl:with-param name="title" select = "@title" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:if test="@moderator='1'">Yes</xsl:if>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </td>
                    </xsl:when>
                    <xsl:when test="position() = last()">
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:call-template name="showSessionid">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:call-template name="showSessionTitle">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                                <xsl:with-param name="title" select = "@title" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:if test="@moderator='1'">Yes</xsl:if>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </td>
                    </xsl:when>
                    <xsl:otherwise>
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
                            <xsl:if test="@moderator='1'">Yes</xsl:if>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </td>
                    </xsl:otherwise>
                </xsl:choose>
            </tr>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
EOD;
