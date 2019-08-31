<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Full Room Schedule by time then room';
$report['description'] = 'Lists all Sessions Scheduled in all Rooms (includes "Public", "Do Not Print" and "Staff Only").';
$report['categories'] = array(
    'Events Reports' => 30,
    'Programming Reports' => 30,
    'GOH Reports' => 30,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        DATE_FORMAT(S.duration,'%i') as durationmin, DATE_FORMAT(S.duration,'%k') as durationhrs,
	R.roomid, R.roomname, R.function, TR.trackname, S.sessionid, S.title, PS.pubstatusname, 
	DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
	     Sessions S
	JOIN Schedule SCH USING (sessionid)
	JOIN Rooms R USING (roomid)
	JOIN Tracks TR USING (trackid)
	JOIN PubStatuses PS USING (pubstatusid)
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        SCH.sessionid, P.pubsname, P.badgeid, POS.moderator
    FROM
			 Schedule SCH
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump C USING (badgeid)
    ORDER BY
		SCH.sessionid, POS.moderator DESC, 
        IF(instr(P.pubsname,C.lastname)>0,C.lastname,substring_index(P.pubsname,' ',-1)),
        C.firstname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='schedule']/row">
                <table class="report" style="table-layout:fixed">
                    <col style="width:7em" />
                    <col style="width:6em" />
                    <col style="width:12em" />
                    <col style="width:8em" />
                    <col style="width:7em" />
                    <col style="width:6em" />
                    <col style="width:18em" />
                    <col style="width:6.2em" />
                    <col />
                    <tr>
                        <th class="report">Start Time</th>
                        <th class="report">Duration</th>
                        <th class="report">Room Name</th>
                        <th class="report">Function</th>
                        <th class="report">Track Name</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Pub. Status</th>
                        <th class="report">Participants</th>
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
        <xsl:variable name="sessionid" select="@sessionid" />
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
            <td class="report"><xsl:value-of select="@function" /></td>
            <td class="report"><xsl:value-of select="@trackname" /></td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@pubstatusname" /></td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
                        <xsl:for-each select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
                            <xsl:if test="position() != 1">
                                <xsl:text>, </xsl:text>
                            </xsl:if>
                            <xsl:call-template name="showPubsnameWithBadgeid">
                                <xsl:with-param name="badgeid" select = "@badgeid" />
                                <xsl:with-param name="pubsname" select = "@pubsname" />
                            </xsl:call-template>
                            <xsl:if test="@moderator='1'">
                                (MOD)
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                        NULL
                    </xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
