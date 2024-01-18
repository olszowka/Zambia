<?php
// Copyright (c) 2022-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka on 2022-12-15
$report = [];
$report['name'] = 'All Participants and Assignments';
$report['description'] = 'Shows who has been assigned to each session ordered by participant. Includes unassigned participants. Includes scheduled and unscheduled sessions.';
$report['categories'] = array(
    'Programming Reports' => 155,
);
$report['columns'] = array(
    array("width" => "4rem"),
    array("width" => "10rem"),
    array("orderable" => false)
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
    WHERE EXISTS ( SELECT *
                        FROM
                            UserHasPermissionRole UHPR
                        WHERE
                                UHPR.badgeid = P.badgeid
                            AND UHPR.permroleid IN (4) /* Participant (B61) */
                 )                   
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;
$report['queries']['sessions'] =<<<'EOD'
SELECT
        POS.badgeid, POS.moderator, S.sessionid, S.title
    FROM
             ParticipantOnSession POS
        JOIN Sessions S USING (sessionid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        POS.badgeid
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Badgeid</th>
                            <th class="report">Pubsname</th>
                            <th class="report">Sessions</th>
                        </tr>
                    </thead>
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
        <tr class="report">
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
            <td class="report">
                <xsl:choose>
                    <xsl:when test="$rowCount=0">
                        <xsl:text>No Sessions Assigned</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="/doc/query[@queryName='sessions']/row[@badgeid=$badgeid]" />
                    </xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='sessions']/row">
        <div>
            <span style="display:inline-block;width:4rem;text-align: right;padding-right:1rem;">
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                </xsl:call-template>
            </span>
            <span style="display:inline-block;width:2rem;">
                <xsl:choose>
                    <xsl:when test="@moderator=1">
                        <xsl:text>Mod</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </span>
            <xsl:call-template name="showSessionTitle">
                <xsl:with-param name="sessionid" select = "@sessionid" />
                <xsl:with-param name="title" select = "@title" />                
            </xsl:call-template>
        </div>
    </xsl:template>
</xsl:stylesheet>
EOD;
