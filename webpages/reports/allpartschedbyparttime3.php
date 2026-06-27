<?php
// Copyright (c) 2025 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Extended All Participant Schedule ';
$report['description'] = 'The schedule sorted by participant, then time';
$report['categories'] = array(
    'Boskone Central' => 200,
    'Programming Reports' => 23,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT DISTINCT
        P.badgeid, P.pubsname, CD.lastname, CD.firstname
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid)
        JOIN Schedule SCH USING (sessionid)
    ORDER BY
        IF(instr(P.pubsname,CD.lastname)>0,CD.lastname,substring_index(P.pubsname,' ',-1)),
        CD.firstname;
EOD;
$report['queries']['schedule'] =<<<'EOD'
WITH AP AS (
    SELECT
            SCH.sessionid, GROUP_CONCAT(P.pubsname SEPARATOR ", ") AS partlist
        FROM
                 Schedule SCH
            JOIN ParticipantOnSession POS USING (sessionid)
            JOIN Participants P USING (badgeid)
        GROUP BY
            SCH.sessionid
    )
SELECT
        P.pubsname, P.badgeid, POS.moderator, S.duration, R.roomname, R.function, TY.typename, 
        S.sessionid, S.title, DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime2,
        AP.partlist, S.progguiddesc
    FROM
             Participants P
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid)
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN Types TY USING (typeid)
        JOIN AP USING (sessionid)
    ORDER BY
        SCH.starttime;
EOD;
//    ORDER BY
//       SCH.starttime;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table class="report">
                    <col />
                    <col style="min-width:9rem"  />
                    <col />
                    <col />
                    <col />
                    <col />
                    <col style="width:4rem" />
                    <col style="width:6.5rem" />
                    <col />
                    <col />
                    <tr>
                        <th class="report">Badgeid</th>
                        <th class="report">Pubsname</th>
                        <th class="report">Type</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Moderator ?</th>
                        <th class="report">Room Name</th>
                        <th class="report">Start Time</th>
                        <th class="report">Description</th>
                        <th class="report">All Participants</th>
                    </tr>
                    <xsl:for-each select="/doc/query[@queryName='participants']/row">
                        <xsl:variable name="badgeid"><xsl:value-of select="@badgeid" /></xsl:variable>
                        <xsl:call-template name="usersSchedule">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                            <xsl:with-param name="rowdata" select = "/doc/query[@queryName='schedule']/row[@badgeid = $badgeid]" />
                        </xsl:call-template>
                    </xsl:for-each>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="usersSchedule">
        <xsl:param name="badgeid" />
        <xsl:param name="rowdata" />
        <xsl:for-each select="$rowdata">
            <tr class="report">
                <xsl:choose>
                    <xsl:when test="position() = 1">
                        <td rowspan="{last()}" class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showBadgeid">
                                <xsl:with-param name="badgeid" select = "@badgeid" />
                            </xsl:call-template>
                        </td>
                        <td rowspan="{last()}" class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showPubsname">
                                <xsl:with-param name="badgeid" select = "@badgeid" />
                                <xsl:with-param name="pubsname" select = "@pubsname" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:value-of select="@typename" />
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showSessionid">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showSessionTitle">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                                <xsl:with-param name="title" select = "@title" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:if test="@moderator='1'">Yes</xsl:if>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:call-template name="showRoomName">
                                <xsl:with-param name="roomid" select = "@roomid" />
                                <xsl:with-param name="roomname" select = "@roomname" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:value-of select="@starttime2" />
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:value-of select="@progguiddesc" />
                        </td>
                        <td class="report" style="border-top:2px solid black">
                            <xsl:value-of select="@partlist" />
                        </td>
                    </xsl:when>
                    <xsl:when test="position() = last()">
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:value-of select="@typename" />
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:call-template name="showSessionid">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:call-template name="showSessionTitle">
                                <xsl:with-param name="sessionid" select = "@sessionid" />
                                <xsl:with-param name="title" select = "@title" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:if test="@moderator='1'">Yes</xsl:if>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:call-template name="showRoomName">
                                <xsl:with-param name="roomid" select = "@roomid" />
                                <xsl:with-param name="roomname" select = "@roomname" />
                            </xsl:call-template>
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:value-of select="@starttime2" />
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:value-of select="@progguiddesc" />
                        </td>
                        <td class="report" style="border-bottom:2px solid black">
                            <xsl:value-of select="@partlist" />
                        </td>
                    </xsl:when>
                    <xsl:otherwise>
                        <td class="report">
                            <xsl:value-of select="@typename" />
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
                        <td class="report">
                            <xsl:if test="@moderator='1'">Yes</xsl:if>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </td>
                        <td class="report">
                            <xsl:call-template name="showRoomName">
                                <xsl:with-param name="roomid" select = "@roomid" />
                                <xsl:with-param name="roomname" select = "@roomname" />
                            </xsl:call-template>
                        </td>
                        <td class="report">
                            <xsl:value-of select="@starttime2" />
                        </td>
                        <td class="report">
                            <xsl:value-of select="@progguiddesc" />
                        </td>
                        <td class="report">
                            <xsl:value-of select="@partlist" />
                        </td>
                    </xsl:otherwise>
                </xsl:choose>
            </tr>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
EOD;
