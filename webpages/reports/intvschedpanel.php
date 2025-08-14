<?php
// Copyright (c) 2022 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Interest v Schedule - sorted by track, then title';
$report['description'] = 'Show who is interested in each panel and if they are assigned to it. Also show the scheduling information';
$report['categories'] = array(
    'Programming Reports' => 610,
);
$report['columns'] = array(
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array("orderData" => 9),
    array("visible" => false)
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        X.pubsname,
        X.badgeid,
        X.trackname, 
        X.sessionid,
        X.title,
        X.rank,
        X.assigned,
        IF(moderator IS NULL OR moderator=0,0,1) AS moderator,
        Y.roomid,
        Y.roomname,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',Y.starttime),'%a %l:%i %p') AS startTime,
        ADDTIME('$ConStartDatim$',Y.starttime) AS startTimeSort
    FROM (
        SELECT
                PI.badgeid,
                PI.pubsname,
                PI.sessionid,
                POS.sessionid AS assigned,
                moderator,
                title,
                trackname,
                `rank`
            FROM (
                        SELECT
                                T.trackname,
                                S.title,
                                S.sessionid,
                                P.badgeid,
                                P.pubsname,
                                PSI.`rank`
                            FROM
                                     Sessions S
                                JOIN Tracks T USING(trackid)
                                JOIN ParticipantSessionInterest PSI USING(sessionid)
                                JOIN Participants P USING(badgeid)
                            WHERE
                                P.interested=1 
                ) PI 
                LEFT JOIN ParticipantOnSession POS USING(badgeid, sessionid)
        ) AS X 
        LEFT JOIN (
                SELECT
                        SCH.starttime,
                        R.roomname,
                        R.roomid,
                        SCH.sessionid 
                    FROM
                             Schedule SCH
                        JOIN Rooms R USING(roomid)
                 ) AS Y USING(sessionid)
    ORDER BY
        X.trackname, X.title
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Pubsname</th>
                            <th class="report">Track Name</th>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Rank</th>
                            <th class="report">Assigned ?</th>
                            <th class="report">Moderator ?</th>
                            <th class="report">Room Name</th>
                            <th class="report">Start Time</th>
                            <th></th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
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
            <td class="report"><xsl:value-of select="@rank" /></td>
            <td class="report">
                <xsl:if test="@assigned">Yes</xsl:if>
            </td>
            <td class="report">
                <xsl:if test="@moderator='1'">Yes</xsl:if>
            </td>
            <td class="report">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:value-of select="@startTime" />
                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
            </td>
            <td class="report">
                <xsl:value-of select="@startTimeSort" />
                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text> 
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
