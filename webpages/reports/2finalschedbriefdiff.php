<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Schedule - recent edits';
$report['description'] = 'The most recent edit to every session if it was within the last 10 days. (Sorted by track, then session id)';
$report['categories'] = array(
    'Programming Reports' => 70,
    'Publication Reports' => 70,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        SEH.timestamp, S.sessionid, S.title, T.trackname, SS.statusname, SEC.description, SEH.editdescription,
        SEH.name
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
        LEFT JOIN (SELECT
                    SEH2.sessionid, MAX(SEH2.timestamp) AS timestamp
                FROM
                    SessionEditHistory SEH2
                GROUP BY
                    SEH2.sessionid
            ) AS SUBQ1 ON S.sessionid = SUBQ1.sessionid
        LEFT JOIN SessionEditHistory SEH ON S.sessionid = SEH.sessionid AND SUBQ1.timestamp = SEH.timestamp
        LEFT JOIN SessionEditCodes SEC USING (sessioneditcode)
    WHERE
        DATEDIFF (NOW(), SEH.timestamp) < 10
    ORDER BY
        T.trackname, S.sessionid;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table class="report">
                    <tr>
                        <th class="report">When</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Track</th>
                        <th class="report">
                            <div>Current</div>
                            <div>Status</div>
                        </th>
                        <th class="report">Who</th>
                        <th class="report">What</th>
                        <th class="report">Notes</th>
                        <th class="report">Full History</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    <xsl:template match="doc/query[@queryName='sessions']/row">
        <tr>
            <td class="report"><xsl:value-of select="@timestamp" /></td>
            <td class="report">
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                    <xsl:with-param name="title" select="@title" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@trackname" /></td>
            <td class="report"><xsl:value-of select="@statusname" /></td>
            <td class="report"><xsl:value-of select="@name" /></td>
            <td class="report"><xsl:value-of select="@description" /></td>
            <td class="report"><xsl:value-of select="@editdescription" /></td>
            <td class="report"><a href="SessionHistory.php?selsess={@sessionid}">History</a></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
