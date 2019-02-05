<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Schedule';
$report['description'] = 'Below is the Panel, Events, Film, Anime, Video and Arisia TV schedule.';
$report['categories'] = array(
    'Programming Reports' => 60,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') as starttime,
        DATE_FORMAT(S.duration,'%i') as durationmin,
        DATE_FORMAT(S.duration,'%k') as durationhrs,
        R.roomname, 
        T.trackname,
        S.sessionid,
        S.title
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN Tracks T USING (trackid)
        JOIN Rooms R USING (roomid)
    WHERE
        S.pubstatusid = 2 ## public
    ORDER BY
        SCH.starttime, T.trackname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='schedule']/row">
                <table class="report">
                    <tr>
                        <th class="report">Start Time</th>
                        <th class="report">Duration</th>
                        <th class="report">Room Name</th>
                        <th class="report">Track Name</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
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
            <td class="report"><xsl:value-of select="@starttime" /></td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@trackname" /></td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
