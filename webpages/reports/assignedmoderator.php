<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Assigned Moderator by Session';
$report['multi'] = 'true';
$report['output_filename'] = 'assignedModerator.csv';
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
                <table id="reportTable" class="table table-sm table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th>Track Name</th>
                            <th>Session ID</th>
                            <th>Title</th>
                            <th>Badgeid</th>
                            <th>Pubsname</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Room</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                        </tr>
                    </thead>
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
            <td><xsl:value-of select="@trackname" /></td>
            <td><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td>
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td>
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
            <td>
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
            <td><xsl:value-of select="@typename" /></td>
            <td><xsl:value-of select="@statusname" /></td>
            <td>
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
            <td>
                <xsl:choose>
                    <xsl:when test="@starttime">
                        <xsl:value-of select="@starttime" />
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
            <td>
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
