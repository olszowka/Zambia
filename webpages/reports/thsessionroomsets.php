<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session roomsets';
$report['description'] = 'What roomsets are we using (Scheduled sessions only)';
$report['categories'] = array(
//  'Events Reports' => 1040,
    'Programming Reports' => 1040,
    'Hotel Reports' => 1040,
    'Tech Reports' => 1040,
);
$report['columns'] = array(
    array("width" => "11.5em"),
    array("width" => "8em", "orderData" => 2),
    array("visible" => false),
    array("width" => "6em", "orderData" => 4),
    array("visible" => false),
    array("width" => "8em"),
    array("width" => "5em"),
    array("width" => "20em"),
    array("width" => "9em"),
    array()
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.title, S.sessionid, SCH.roomid, R.roomname, T.trackname, RS.roomsetname, S.servicenotes,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        ADDTIME('$ConStartDatim$',SCH.starttime) AS startTimeSort,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
        S.duration AS durationSort
    FROM 
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN Tracks T USING (trackid)
        JOIN RoomSets RS USING (roomsetid)	  
    ORDER BY
           R.roomname,
           SCH.starttime;
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
                            <th class="report">Room</th>
                            <th class="report">StartTime</th>
                            <th></th>
                            <th class="report">Duration</th>
                            <th></th>
                            <th class="report">Track</th>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Room Set</th>
                            <th class="report">Notes for Tech and Hotel</th>
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
            <td class="report"><xsl:value-of select="@starttime" /></td>
            <td class="report"><xsl:value-of select="@startTimeSort" /></td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@durationSort" /></td>
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
            <td class="report"><xsl:value-of select="@roomsetname" /></td>
            <td class="report"><xsl:value-of select="@servicenotes" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
