<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Services';
$report['description'] = 'Which Session needs which Services? (Sorted by room then time; Scheduled sessions only)';
$report['categories'] = array(
    'Events Reports' => 1050,
    'Programming Reports' => 1050,
    'Hotel Reports' => 1050,
    'Tech Reports' => 1050,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT DISTINCT
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
        SCH.roomid, R.roomname, T.trackname, S.sessionid, S.title
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN SessionHasService SHS USING (sessionid)
        JOIN Services SV USING (serviceid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        R.roomname, 
        SCH.starttime;
EOD;
$report['queries']['services'] =<<<'EOD'
SELECT
        S.sessionid, SV.servicename
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN SessionHasService SHS USING (sessionid)
        JOIN Services SV USING (serviceid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
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
                        <th class="report">Room</th>
                        <th class="report">Start Time</th>
                        <th class="report">Duration</th>
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Services</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='sessions']/row">
        <xsl:variable name="sessionid" select="@sessionid" />
        <tr class="report">
            <td class="report" >
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td class="report" >
                <xsl:value-of select="@starttime" />
            </td>
            <td class="report" >
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td class="report" >
                <xsl:value-of select="@trackname" />
            </td>
            <td class="report" >
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                </xsl:call-template>
            </td>
            <td class="report" >
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report" >
                <xsl:for-each select="/doc/query[@queryName='services']/row[@sessionid=$sessionid]">
                    <div>
                        <xsl:value-of select="@servicename" />
                    </div>
                </xsl:for-each>    
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
