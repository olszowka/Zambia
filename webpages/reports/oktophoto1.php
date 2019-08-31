<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'OK to Photograph Report -- Participants';
$report['description'] = 'List of all participants appearing on sessions in which all participants give permission for photos to be taken sorted by participants.';
$report['categories'] = array(
    'Programming Reports' => 115,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        S.sessionid, S.title, DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        R.roomid, R.roomname
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
    WHERE NOT EXISTS (
    	SELECT *
    	    FROM
    	             Sessions S2
    	        JOIN Schedule SCH2 USING (sessionid)
    	        JOIN ParticipantOnSession POS2 USING (sessionid)
    	        JOIN Participants P2 USING (badgeid)
    	    WHERE
    	            S2.sessionid = S.sessionid
    	        AND IFNULL(P2.use_photo, 0) = 0
    	);
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        S.sessionid, P.badgeid, P.pubsname
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
    WHERE NOT EXISTS (
    	SELECT *
    	    FROM
    	             Sessions S2
    	        JOIN Schedule SCH2 USING (sessionid)
    	        JOIN ParticipantOnSession POS2 USING (sessionid)
    	        JOIN Participants P2 USING (badgeid)
    	    WHERE
    	            S2.sessionid = S.sessionid
    	        AND IFNULL(P2.use_photo, 0) = 0
    	)
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname,
        SCH.starttime;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table class="report">
                    <tr>
                        <th class="report">Pubsname</th>
                        <th class="report">Badgeid</th>
                        <th class="report">Title</th>
                        <th class="report">Room Name</th>
                        <th class="report">Start Time</th>
                    </tr>
                    <xsl:for-each select="doc/query[@queryName='participants']/row">
                        <xsl:variable name="badgeid" select="@badgeid" />
                        <xsl:variable name="sessionid" select="@sessionid" />
                        <xsl:variable name="scheduleRow" select="/doc/query[@queryName='schedule']/row[@sessionid=$sessionid]" />
                        <xsl:variable name="prevBadgeId" select="preceding-sibling::row[1]/@badgeid" />
                        <xsl:variable name="follBadgeId" select="following-sibling::row[1]/@badgeid" />
                        <xsl:variable name="schedRowCount" select="count(/doc/query[@queryName='participants']/row[@badgeid=$badgeid])" />
                        <tr class="report">
                            <xsl:choose>
                                <xsl:when test="not($prevBadgeId) or $badgeid != $prevBadgeId">
                                    <td rowspan="{$schedRowCount}" class="report za-report-firstRowCell za-report-lastRowCell za-report-firstColCell">
                                        <xsl:call-template name="showPubsname">
                                            <xsl:with-param name="badgeid" select = "@badgeid" />
                                            <xsl:with-param name="pubsname" select = "@pubsname" />
                                        </xsl:call-template>
                                    </td>
                                    <td rowspan="{$schedRowCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                                        <xsl:call-template name="showBadgeid">
                                            <xsl:with-param name="badgeid" select = "@badgeid" />
                                        </xsl:call-template>
                                    </td>
                                    <xsl:choose>
                                        <xsl:when test="not($follBadgeId) or $badgeid != $follBadgeId">
                                            <td class="report za-report-firstRowCell za-report-lastRowCell">
                                                <xsl:call-template name="showSessionTitle">
                                                    <xsl:with-param name="sessionid" select = "@sessionid" />
                                                    <xsl:with-param name="title" select = "$scheduleRow/@title" />
                                                </xsl:call-template>
                                            </td>
                                            <td class="report za-report-firstRowCell za-report-lastRowCell">
                                                <xsl:call-template name="showRoomName">
                                                    <xsl:with-param name="roomid" select = "$scheduleRow/@roomid" />
                                                    <xsl:with-param name="roomname" select = "$scheduleRow/@roomname" />
                                                </xsl:call-template>
                                            </td>
                                            <td class="report za-report-firstRowCell za-report-lastRowCell za-report-lastColCell">
                                                <xsl:value-of select="$scheduleRow/@starttime" />
                                            </td>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <td class="report za-report-firstRowCell">
                                                <xsl:call-template name="showSessionTitle">
                                                    <xsl:with-param name="sessionid" select = "@sessionid" />
                                                    <xsl:with-param name="title" select = "$scheduleRow/@title" />
                                                </xsl:call-template>
                                            </td>
                                            <td class="report za-report-firstRowCell">
                                                <xsl:call-template name="showRoomName">
                                                    <xsl:with-param name="roomid" select = "$scheduleRow/@roomid" />
                                                    <xsl:with-param name="roomname" select = "$scheduleRow/@roomname" />
                                                </xsl:call-template>
                                            </td>
                                            <td class="report za-report-firstRowCell za-report-lastColCell">
                                                <xsl:value-of select="$scheduleRow/@starttime" />
                                            </td>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:when>
                                <xsl:when test="not($follBadgeId) or $badgeid != $follBadgeId">
                                    <td class="report za-report-lastRowCell">
                                        <xsl:call-template name="showSessionTitle">
                                            <xsl:with-param name="sessionid" select = "@sessionid" />
                                            <xsl:with-param name="title" select = "$scheduleRow/@title" />
                                        </xsl:call-template>
                                    </td>
                                    <td class="report za-report-lastRowCell">
                                        <xsl:call-template name="showRoomName">
                                            <xsl:with-param name="roomid" select = "$scheduleRow/@roomid" />
                                            <xsl:with-param name="roomname" select = "$scheduleRow/@roomname" />
                                        </xsl:call-template>
                                    </td>
                                    <td class="report za-report-lastRowCell za-report-lastColCell">
                                        <xsl:value-of select="$scheduleRow/@starttime" />
                                    </td>
                                </xsl:when>
                                <xsl:otherwise>
                                    <td class="report">
                                        <xsl:call-template name="showSessionTitle">
                                            <xsl:with-param name="sessionid" select = "@sessionid" />
                                            <xsl:with-param name="title" select = "$scheduleRow/@title" />
                                        </xsl:call-template>
                                    </td>
                                    <td class="report">
                                        <xsl:call-template name="showRoomName">
                                            <xsl:with-param name="roomid" select = "$scheduleRow/@roomid" />
                                            <xsl:with-param name="roomname" select = "$scheduleRow/@roomname" />
                                        </xsl:call-template>
                                    </td>
                                    <td class="report za-report-lastColCell">
                                        <xsl:value-of select="$scheduleRow/@starttime" />
                                    </td>
                                </xsl:otherwise>
                            </xsl:choose>
                        </tr>
                    </xsl:for-each>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
EOD;
