<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Room Turns';
$report['description'] = 'List of all Sessions related to room turns';
$report['categories'] = array(
    'Hotel Reports' => 910,
);
$report['queries'] = [];
$report['queries']['report'] =<<<'EOD'
SELECT
		DATE_FORMAT(ADDTIME("$ConStartDatim$",SCH.starttime),"%a") AS startday,
		DATE_FORMAT(ADDTIME("$ConStartDatim$",SCH.starttime),"%H:%i") AS starttime,
		DATE_FORMAT(ADDTIME(ADDTIME("$ConStartDatim$",SCH.starttime),S.duration),"%H:%i") AS endtime,
		SCH.roomid, R.roomname, S.sessionid, S.title, RS.roomsetname, S.servicenotes, TY.typename
	FROM
			 Schedule SCH
		JOIN Sessions S USING (sessionid)
		JOIN Rooms R USING (roomid)
		JOIN RoomSets RS USING (roomsetid)
		JOIN Types TY USING (typeid)
	WHERE
			S.typeid IN (3, 21, 22)
		OR S.trackid=14
	ORDER BY
		SCH.starttime, R.display_order;
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
                        <th class="report" style="width:3.2em">Start Day</th>
                        <th class="report" style="width:3.2em">Start Time</th>
                        <th class="report" style="width:3.2em">End Time</th>
                        <th class="report" style="width:9em">Room</th>
                        <th class="report">Session</th>
                        <th class="report" style="min-width:20em">Title</th>
                        <th class="report" style="width:7em">Room Set Name</th>
                        <th class="report">Type</th>
                        <th class="report">Notes for Tech and Hotel</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='report']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='report']/row">
        <tr>
            <td class="report"><xsl:value-of select="@startday"/></td>
            <td class="report"><xsl:value-of select="@starttime"/></td>
            <td class="report"><xsl:value-of select="@endtime"/></td>
            <td class="report">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>    
            </td>
            <td class="report"><xsl:value-of select="@roomsetname"/></td>
            <td class="report"><xsl:value-of select="@typename"/></td>
            <td class="report"><xsl:value-of select="@servicenotes"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
