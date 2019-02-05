<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Participant Double Booked ';
$report['description'] = 'Find all instances where a participant is scheduled to be in two or more places at once.';
$report['categories'] = array(
    'Conflict Reports' => 400,
);
$report['queries'] = [];
$report['queries']['conflict'] =<<<'EOD'
SELECT
		P.pubsname,
		CNFLC.badgeid,
		CD.firstname,
		CD.lastname,
		CNFLC.titlea,
		TA.trackname as tracknamea,
		RA.roomname as roomnamea,
		CNFLC.roomida,
		CNFLC.sessionida,
		DATE_FORMAT(ADDTIME('$ConStartDatim$',CNFLC.starttimea),'%a %l:%i %p') as starttimea,
		DATE_FORMAT(CNFLC.durationa,'%l:%i') as durationa,
		CNFLC.titleb,
		TB.trackname as tracknameb,
		RB.roomname as roomnameb,
		CNFLC.roomidb,
		CNFLC.sessionidb,
		DATE_FORMAT(ADDTIME('$ConStartDatim$',CNFLC.starttimeb),'%a %l:%i %p') as starttimeb,
		DATE_FORMAT(CNFLC.durationb,'%l:%i') as durationb
	FROM
		(SELECT
				POSA.badgeid, 
				SCHA.roomid AS roomida, 
				SCHA.sessionid AS sessionida, 
				SCHA.starttime AS starttimea, 
				ADDTIME(SCHA.starttime, SA.duration) AS endtimea, 
				SA.trackid AS trackida, 
				SA.duration AS durationa,
				SA.title AS titlea,
				SCHB.sessionid AS sessionidb, 
				SCHB.roomid AS roomidb, 
				SCHB.starttime AS starttimeb, 
				ADDTIME(SCHB.starttime, SB.duration) AS endtimeb, 
				SB.trackid AS trackidb,
				SB.duration AS durationb,
				SB.title AS titleb
			FROM
					Schedule SCHA
			   JOIN Sessions SA ON SCHA.sessionid = SA.sessionid
			   JOIN ParticipantOnSession POSA ON SA.sessionid = POSA.sessionid
		 	   JOIN ParticipantOnSession POSB ON POSA.badgeid = POSB.badgeid
		 	   JOIN Schedule SCHB ON POSB.sessionid = SCHB.sessionid
			   JOIN Sessions SB ON SCHB.sessionid = SB.sessionid
			WHERE
					SCHA.sessionid < SCHB.sessionid
				AND SCHA.starttime < ADDTIME(SCHB.starttime, SB.duration)
				AND ADDTIME(SCHA.starttime, SA.duration) > SCHB.starttime
			) AS CNFLC
		JOIN Rooms RA ON CNFLC.roomida = RA.roomid 
		JOIN Rooms RB ON CNFLC.roomidb = RB.roomid 
		JOIN Tracks TA ON CNFLC.trackida = TA.trackid
		JOIN Tracks TB ON CNFLC.trackidb = TB.trackid
		JOIN Participants P ON CNFLC.badgeid = P.badgeid
		JOIN CongoDump CD ON CNFLC.badgeid = CD.badgeid
	ORDER BY
		CD.lastname
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='conflict']/row">
                <table class="report">
                    <col />
                    <col style="width:20em;" />
                    <col />
                    <col />
                    <col style="width:8em;" />
                    <col />
                    <col />
                    <col style="width:20em;" />
                    <col />
                    <col />
                    <col />
                    <col style="width:8em;" />
                    <col />
                    <tr>
                        <th class="report">Participant</th>
                        <th class="report">Title A</th>
                        <th class="report">Track A</th>
                        <th class="report">Room ID A</th>
                        <th class="report">Session ID A</th>
                        <th class="report">Start Time A</th>
                        <th class="report">Duration A</th>
                        <th class="report">Title B</th>
                        <th class="report">Track B</th>
                        <th class="report">Room ID B</th>
                        <th class="report">Session ID B</th>
                        <th class="report">Start Time B</th>
                        <th class="report">Duration B</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='conflict']/row" /> 
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='conflict']/row" >
        <tr>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@pubsname">
                        <xsl:value-of select="@pubsname" />
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="@firstname" /> <xsl:value-of select="@lastname" />
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionida" />
                    <xsl:with-param name="title" select = "@titlea" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:value-of select="@tracknamea" />
            </td>
            <td class="report">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomida" />
                    <xsl:with-param name="roomname" select = "@roomnamea" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionida" /></xsl:call-template>
            </td>
            <td class="report" style="white-space:nowrap"><xsl:value-of select="@starttimea" /></td>
            <td class="report">
                <xsl:value-of select="@durationa" />
            </td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionidb" />
                    <xsl:with-param name="title" select = "@titleb" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:value-of select="@tracknameb" />
            </td>
            <td class="report">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomidb" />
                    <xsl:with-param name="roomname" select = "@roomnameb" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionidb" /></xsl:call-template>
            </td>
            <td class="report" style="white-space:nowrap"><xsl:value-of select="@starttimeb" /></td>
            <td class="report">
                <xsl:value-of select="@durationb" />
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
