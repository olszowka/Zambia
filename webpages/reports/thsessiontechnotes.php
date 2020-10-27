<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Tech and Hotel notes';
$report['description'] = 'What notes are in on this panel for tech and hotel? (sorted by room then time)';
$report['categories'] = array(
//  'Events Reports' => 1070,
    'Programming Reports' => 1070,
    'Hotel Reports' => 1070,
    'Tech Reports' => 1070,
);
$report['columns'] = array(
    array("width" => "11em"),
    array("width" => "7em", "orderData" => 2),
    array("visible" => false),
    array("width" => "6em", "orderData" => 4),
    array("visible" => false),
    array("width" => "9em"),
    array("width" => "5em"),
    array("width" => "27em"),
    array()
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        SCH.roomid,
	R.roomname,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') as startTime,
        ADDTIME('$ConStartDatim$',SCH.starttime) as startTimeSort,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
        S.duration AS durationSort,
        TR.trackname, 
        S.sessionid,
        S.title,
        S.servicenotes 
    FROM
             Sessions S
        JOIN Tracks TR USING (trackid)
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
    WHERE
            S.servicenotes IS NOT NULL
        AND S.servicenotes!=' '
        AND S.servicenotes!=''
        AND S.statusid NOT IN (4, 5, 10 ) ##dropped, cancelled, duplicate
    ORDER BY
        Roomname, Starttime;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr>
                            <th class="report">Room name</th>
                            <th class="report">Start time</th>
                            <th></th>
                            <th class="report">Duration</th>
                            <th></th>
                            <th class="report">Track name</th>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Service Notes</th>
                        </tr>
                    </thead>
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
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@startTime"/></td>
            <td class="report"><xsl:value-of select="@startTimeSort"/></td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@durationSort"/></td>
            <td class="report"><xsl:value-of select="@trackname"/></td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@servicenotes"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
