<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Gaming Schedule';
$report['description'] = 'All Gaming and Gaming Panels. All these reports include both.';
$report['categories'] = array(
    'Gaming Reports' => 550,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        S.sessionid, S.title, POS.badgeid, P.pubsname, POS.moderator
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
    WHERE
        S.trackid = 7 /* Gaming */
    ORDER BY
        S.sessionid, POS.moderator DESC, IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1));
EOD;
$report['queries']['schedule'] =<<<'EOD'
SELECT
        R.roomname, DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') as durationmin, DATE_FORMAT(S.duration,'%k') as durationhrs,
        TY.typename, S.sessionid, S.title, SCH.roomid
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN Types TY USING (typeid)
    WHERE
        S.trackid = 7 /* Gaming */
    ORDER BY
        R.roomname, SCH.starttime;
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
                    <col style="width:11em;" />
                    <col style="width:8em;" />
                    <col style="width:7em;" />
                    <col style="width:5em;" />
                    <col style="width:6em;" />
                    <col style="width:25em;" />
                    <col />
                    <tr>
                        <th class="report">Room name</th>
                        <th class="report">Start time</th>
                        <th class="report">Duration</th>
                        <th class="report">Type</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Participants</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='schedule']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='schedule']/row">
        <xsl:variable name="sessionid" select="@sessionid" />
        <tr>
            <td class="report">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@starttime" /></td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@typename" /></td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
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
                                <xsl:text> (MOD)</xsl:text>
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>NULL</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
