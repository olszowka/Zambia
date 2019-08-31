<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Schedule but not';
$report['description'] = 'These are sessions that are either in the grid and not set as scheduled or they are set as scheduled and not in the grid.';
$report['categories'] = array(
    'Conflict Reports' => 460,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, SS.statusname, R.roomname, SCH.roomid, 
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
                  Sessions S
             JOIN SessionStatuses SS USING (statusid)
        LEFT JOIN Schedule SCH USING (sessionid)
        LEFT JOIN Rooms R USING (roomid)
    WHERE
            (   S.statusid = 3 ## Scheduled
            AND SCH.scheduleid IS NULL)
        OR (S.statusid != 3 ## Scheduled
            AND SCH.scheduleid IS NOT NULL)
    ORDER BY
        S.sessionid;
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
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Room</th>
                        <th class="report">StartTime</th>
                        <th class="report">Status</th>
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
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@starttime" /></td>
             <td class="report"><xsl:value-of select="@statusname" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
