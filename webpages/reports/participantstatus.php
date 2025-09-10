<?php
// Copyright (c) 2025 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Schedule with participant statuses';
$report['description'] = 'List all sessions in all rooms.  Include full description and list of participants.';
$report['categories'] = array(
    'Tech Reports' => 880,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, R.roomname, SCH.roomid,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        SCH.sessionid, P.pubsname, P.badgeid, POS.moderator, C.firstname, C.lastname,
        C.badgename, C.phone, PSA.value, PSA.othertext
    FROM
                 Schedule SCH
             JOIN ParticipantOnSession POS USING (sessionid)
             JOIN Participants P USING (badgeid)
             JOIN CongoDump C USING (badgeid)
        LEFT JOIN ParticipantSurveyAnswers PSA ON P.badgeid = PSA.participantid AND PSA.questionid = 8
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
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table class="report">
                    <tr>
                        <th class="report" style="white-space:nowrap;">Session ID</th>
                        <th colspan = "4" class="report">Title</th>
                        <th colspan = "2" class="report">Room</th>
                        <th class="report">StartTime</th>
                        <th class="report">Duration</th>
                    </tr>
                    <tr>
                        <th class="report"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></th>
                        <th class="report">Mod</th>
                        <th class="report">Pubs Name</th>
                        <th class="report">Badge Name</th>
                        <th class="report">First Name</th>
                        <th class="report">Last Name</th>
                        <th class="report">Phone</th>
                        <th class="report">Status</th>
                        <th class="report">Note</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
    <xsl:variable name="sessionid" select="@sessionid" />
        <tr>
            <td class="report border2111">
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                </xsl:call-template>
            </td>
            <td colspan="4" class="report border2111">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                    <xsl:with-param name="title" select="@title" />
                </xsl:call-template>
            </td>
            <td colspan="2" class="report border2111">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select="@roomid" />
                    <xsl:with-param name="roomname" select="@roomname" />
                </xsl:call-template>
            </td>
            <td class="report border2111" style="white-space:nowrap;"><xsl:value-of select="@starttime" /></td>
            <td class="report border2111" style="white-space:nowrap;">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
        </tr>

        <xsl:for-each select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
            <tr>
                <td class="report"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
                <td class="report">
                    <xsl:choose>
                        <xsl:when test="@moderator=1">
                            <xsl:text>MOD</xsl:text>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </td>
                <td class="report">
                    <xsl:call-template name="showPubsnameWithBadgeid">
                        <xsl:with-param name="badgeid" select = "@badgeid" />
                        <xsl:with-param name="pubsname" select = "@pubsname" />
                    </xsl:call-template>
                </td>
                <td class="report"><xsl:value-of select="@badgename" /></td>
                <td class="report"><xsl:value-of select="@firstname" /></td>
                <td class="report"><xsl:value-of select="@lastname" /></td>
                <td class="report"><xsl:value-of select="@phone" /></td>
                <td class="report"><xsl:value-of select="@value" /></td>
                <td class="report"><xsl:value-of select="@othertext" /></td>
            </tr>
        </xsl:for-each>
        <tr>
            <td colspan="9"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
