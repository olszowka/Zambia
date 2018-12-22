<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Services by Service';
$report['description'] = 'Which Session needs which Services? (Sorted by service then time; Scheduled sessions only)';
$report['categories'] = array(
    'Events Reports' => 1060,
    'Programming Reports' => 1060,
    'Hotel Reports' => 1060,
    'Tech Reports' => 1060,
);
$report['queries'] = [];
$report['queries']['services'] =<<<'EOD'
SELECT DISTINCT
        SV.servicename, SHS.serviceid
    FROM
             Schedule SCH
        JOIN SessionHasService SHS USING (sessionid)
        JOIN Services SV USING (serviceid)
        JOIN Sessions S USING (sessionid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        SV.servicename;
EOD;
$report['queries']['sessions'] =<<<'EOD'
SELECT
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
        SCH.roomid, R.roomname, T.trackname, S.sessionid, S.title, SHS.serviceid
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN SessionHasService SHS USING (sessionid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='services']/row">
                <table class="report">
                    <tr>
                        <th class="report">Service</th>
                        <th class="report">Start Time</th>
                        <th class="report">Duration</th>
                        <th class="report">Room</th>
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='services']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='services']/row">
        <xsl:variable name="serviceid" select="@serviceid" />
        <xsl:variable name="servicename" select="@servicename" />
	    <xsl:for-each select="/doc/query[@queryName='sessions']/row[@serviceid=$serviceid]">
            <tr class="report">
                <xsl:choose>
                    <xsl:when test="position() = 1 and position() = last()">
                        <td class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:value-of select="$servicename" />
                        </td>
                        <td class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:value-of select="@starttime" />
                        </td>
                        <td class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:call-template name="showDuration">
                                <xsl:with-param name="durationhrs" select = "@durationhrs" />
                                <xsl:with-param name="durationmin" select = "@durationmin" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:call-template name="showRoomName">
                                <xsl:with-param name="roomid" select = "@roomid" />
                                <xsl:with-param name="roomname" select = "@roomname" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:value-of select="@trackname" />
                        </td>
                        <td class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:call-template name="showSessionid">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:call-template name="showSessionTitle">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                                <xsl:with-param name="title" select = "@title" />
                            </xsl:call-template>
                        </td>
                    </xsl:when>
                    <xsl:when test="position() = 1">
                        <td rowspan="{last()}" class="report" style="border-top:2px solid black;border-bottom:2px solid black">
                            <xsl:value-of select="$servicename" />
                        </td>
                        <td class="report" style="border-top:2px solid black;">
                            <xsl:value-of select="@starttime" />
                        </td>
                        <td class="report" style="border-top:2px solid black;">
                            <xsl:call-template name="showDuration">
                                <xsl:with-param name="durationhrs" select = "@durationhrs" />
                                <xsl:with-param name="durationmin" select = "@durationmin" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black;">
                            <xsl:call-template name="showRoomName">
                                <xsl:with-param name="roomid" select = "@roomid" />
                                <xsl:with-param name="roomname" select = "@roomname" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black;">
                            <xsl:value-of select="@trackname" />
                        </td>
                        <td class="report" style="border-top:2px solid black;">
                            <xsl:call-template name="showSessionid">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black;">
                            <xsl:call-template name="showSessionTitle">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                                <xsl:with-param name="title" select = "@title" />
                            </xsl:call-template>
                        </td>
                    </xsl:when>
                    <xsl:when test="position() = last()">
                        <td class="report" style="border-bottom:2px solid black;">
                            <xsl:value-of select="@starttime" />
                        </td>
                        <td class="report" style="border-bottom:2px solid black;">
                            <xsl:call-template name="showDuration">
                                <xsl:with-param name="durationhrs" select = "@durationhrs" />
                                <xsl:with-param name="durationmin" select = "@durationmin" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black;">
                            <xsl:call-template name="showRoomName">
                                <xsl:with-param name="roomid" select = "@roomid" />
                                <xsl:with-param name="roomname" select = "@roomname" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black;">
                            <xsl:value-of select="@trackname" />
                        </td>
                        <td class="report" style="border-bottom:2px solid black;">
                            <xsl:call-template name="showSessionid">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black;">
                            <xsl:call-template name="showSessionTitle">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                                <xsl:with-param name="title" select = "@title" />
                            </xsl:call-template>
                        </td>
                    </xsl:when>
                    <xsl:otherwise>
                        <td class="report">
                            <xsl:value-of select="@starttime" />
                        </td>
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
                        <td class="report">
                            <xsl:value-of select="@trackname" />
                        </td>
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
                    </xsl:otherwise>
                </xsl:choose>
            </tr>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
EOD;
