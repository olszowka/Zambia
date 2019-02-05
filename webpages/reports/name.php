<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Name Report';
$report['description'] = 'Maps badgeid, pubsname, badgename and first and last name together (includes every record in the database regardless of status)';
$report['categories'] = array(
    'Events Reports' => 670,
    'Participant Info Reports' => 670,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        C.badgeid, P.pubsname, C.badgename, C.lastname, C.firstname, C.badgeid, P.interested,
        IF(EXISTS(
            SELECT SCH.scheduleid
                FROM
                         Schedule SCH
                    JOIN Sessions S USING (sessionid)
                    JOIN ParticipantOnSession POS USING (sessionid)
                WHERE
                        POS.badgeid = C.badgeid
                    AND S.pubstatusid = 2 /* Public */
            ), 1, 0) AS participantIsScheduled
    FROM
             CongoDump C
        JOIN Participants P USING (badgeid)
    ORDER BY
        IF(instr(P.pubsname, C.lastname) > 0, C.lastname, substring_index(P.pubsname, ' ', -1)), C.firstname;
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
                        <th class="report">Badge ID</th>
                        <th class="report">Name for Publications</th>
                        <th class="report">Last Name</th>
                        <th class="report">First Name</th>
                        <th class="report">Badge Name</th>
                        <th class="report">Interested</th>
                        <th class="report">Scheduled</th>
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
        <tr>
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@pubsname" /></td>
            <td class="report"><xsl:value-of select="@lastname" /></td>
            <td class="report"><xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@badgename" /></td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@interested='0'">Didn't respond</xsl:when>
                    <xsl:when test="@interested='1'">Yes</xsl:when>
                    <xsl:when test="@interested='2'">No</xsl:when>
                    <xsl:otherwise>Didn't log in</xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@participantIsScheduled='1'">Yes</xsl:when>
                    <xsl:otherwise>No</xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
