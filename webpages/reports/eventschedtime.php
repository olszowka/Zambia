<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Event Schedule by time then room';
$report['description'] = 'Lists all Events (as determined by division on session) Scheduled in all Rooms (includes "Public", "Do Not Print" and "Staff Only").';
$report['categories'] = array(
    'Events Reports' => 5,
);
$report['columns'] = array(
    array("orderData" => 1, "width" => "7em"),
    array("visible" => false),
    array("width" => "6em", "orderData" => 3),
    array("visible" => false),
    array("width" => "14em"),
    array("width" => "12em"),
    array("width" => "9em"),
    array("width" => "6em"),
    array("width" => "17em"),
    array("width" => "6.5em"),
    array()
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        DATE_FORMAT(ADDTIME('$ConStartDatim$',starttime),'%a %l:%i %p') as 'starttime',
        starttime as starttimeraw,
        S.duration,
        DATE_FORMAT(S.duration,'%i') as durationmin,
        DATE_FORMAT(S.duration,'%k') as durationhrs,
        R.roomid,
        R.roomname,
        R.function,
        T.trackname,
        S.sessionid,
        S.title, 
        PS.pubstatusname
    FROM
            Schedule SCH
       JOIN Sessions S USING (sessionid)
       JOIN Tracks T USING (trackid)
       JOIN Rooms R USING (roomid)
       JOIN PubStatuses PS USING (pubstatusid)
    WHERE
        S.divisionid=3 # Events
    ORDER BY
        SCH.starttime,
        R.roomname
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        S.sessionid,
        IFNULL(P.pubsname,CONCAT(CD.firstname, ' ', CD.lastname)) AS pubsname,
        P.badgeid
    FROM
            Schedule SCH
       JOIN Sessions S USING (sessionid)
  LEFT JOIN ParticipantOnSession POS USING (sessionid)
  LEFT JOIN Participants P USING (badgeid)
  LEFT JOIN CongoDump CD USING (badgeid)
    WHERE
            S.divisionid=3 # Events
        AND P.badgeid IS NOT NULL
    ORDER BY
        S.sessionid, P.pubsname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table id="reportTable" class="report" >
                    <thead>
                        <tr>
                            <th class="report">Start Time</th>
                            <th></th>
                            <th class="report">Duration</th>
                            <th></th>
                            <th class="report">Room Name</th>
                            <th class="report">Room Function</th>
                            <th class="report">Track Name</th>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Publication Status</th>
                            <th class="report">Participants</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
        <tr>
            <td class="report"><xsl:value-of select="@starttime" /></td>
            <td><xsl:value-of select="@starttimeraw" /></td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@duration" /></td>
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
                <xsl:call-template name="participants">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                </xsl:call-template>
            </td>
        </tr>
    </xsl:template>

    <xsl:template name="participants">
        <xsl:param name="sessionid" />
        <xsl:variable name="participantCount" select="count(/doc/query[@queryName='participants']/row[@sessionid=$sessionid])" />
        <xsl:choose>
            <xsl:when test="$participantCount=0">
                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
                    <xsl:value-of select="@pubsname" /> (<xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template>)<xsl:if test="position() != last()"><xsl:text>, </xsl:text></xsl:if>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
EOD;
