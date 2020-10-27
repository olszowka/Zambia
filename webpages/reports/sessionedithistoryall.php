<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Edit History Report - All';
$report['description'] = 'For each session, show the entire edit history.';
$report['categories'] = array(
//  'Events Reports' => 85,
    'Programming Reports' => 85,
);
$report['queries'] = [];
$report['queries']['edits'] =<<<'EOD'
SELECT
        T.trackname, S.sessionid, S.title, SS.statusname, SEH.timestamp, 
        SEH.badgeid, SEH.name, SEH.email_address, SEC.description, SEH.editdescription
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionEditHistory SEH USING (sessionid)
        JOIN SessionEditCodes SEC USING (sessioneditcode)
        JOIN SessionStatuses SS ON SEH.statusid = SS.statusid
    ORDER BY
        T.trackname, S.sessionid, SEH.timestamp DESC;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='edits']/row">
                <table class="report">
                    <tr>
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Status (at that time)</th>
                        <th class="report">Edit Timestamp</th>
                        <th class="report">Badge ID</th>
                        <th class="report">Who</th>
                        <th class="report">Edit Type</th>
                        <th class="report">Notes</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='edits']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    <xsl:template match="doc/query[@queryName='edits']/row">
        <tr>
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
            <td class="report"><xsl:value-of select="@statusname" /></td>
            <td class="report"><xsl:value-of select="@timestamp" /></td>
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:value-of select="@name" /> (<xsl:value-of select="@email_address" />)
            </td>
            <td class="report"><xsl:value-of select="@description" /></td>
            <td class="report"><xsl:value-of select="@editdescription" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
