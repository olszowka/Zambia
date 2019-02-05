<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Assigned Moderator by Session';
$report['description'] = 'Shows who has been assigned to moderate each session (sorted by track then sessionid).';
$report['categories'] = array(
    'Events Reports' => 145,
    'Programming Reports' => 145,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        R.roomid, R.roomname, TR.trackname, S.sessionid, S.title, SS.statusname, P.pubsname,
        POS.badgeid, TY.typename, DATE_FORMAT(S.duration,'%i') AS durationmin,
        DATE_FORMAT(S.duration,'%k') AS durationhrs,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
                  Sessions S 
             JOIN Tracks TR USING (trackid)
             JOIN SessionStatuses SS USING (statusid)
             JOIN Types TY USING (typeid)
        LEFT JOIN ParticipantOnSession POS ON S.sessionid = POS.sessionid AND POS.moderator = 1
        LEFT JOIN Schedule SCH ON S.sessionid = SCH.sessionid
        LEFT JOIN Rooms R USING (roomid)
        LEFT JOIN Participants P USING (badgeid)
    WHERE
        S.statusid IN (2, 3, 7) /* Vetted, Scheduled, Assigned */
    ORDER BY
        TR.trackname, S.sessionid;
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
                    <col style="width:18em" />
                    <col style="width:5em" />
                    <col style="width:10em" />
                    <col style="width:7em" />
                    <col style="width:7em" />
                    <col style="width:8.5em" />
                    <col style="width:8em" />
                    <col style="width:7em" />
                    <tr>
                        <th class="report">Track Name</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Badgeid</th>
                        <th class="report">Pubsname</th>
                        <th class="report">Type</th>
                        <th class="report">Status</th>
                        <th class="report">Room</th>
                        <th class="report">Start Time</th>
                        <th class="report">Duration</th>
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
            <td class="report"><xsl:value-of select="@trackname" /></td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@badgeid">
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                        </xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@badgeid and @pubsname">
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                            <xsl:with-param name="pubsname" select = "@pubsname" />
                        </xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="report"><xsl:value-of select="@typename" /></td>
            <td class="report"><xsl:value-of select="@statusname" /></td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@roomname">
                        <xsl:call-template name="showRoomName">
                            <xsl:with-param name="roomid" select = "@roomid" />
                            <xsl:with-param name="roomname" select = "@roomname" />
                        </xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@starttime">
                        <xsl:value-of select="@starttime" />
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
