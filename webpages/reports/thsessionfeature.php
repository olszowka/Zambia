<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Features';
$report['description'] = 'Which Session needs which Features? (Sorted by track)';
$report['categories'] = array(
    'Events Reports' => 1030,
    'Programming Reports' => 1030,
    'Hotel Reports' => 1030,
    'Tech Reports' => 1030,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT DISTINCT
        S.title, S.sessionid, SCH.roomid, R.roomname, T.trackname,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs
    FROM 
                  Sessions S
             JOIN Tracks T USING (trackid)
        LEFT JOIN SessionHasFeature USING (sessionid)
        LEFT JOIN Features F USING (featureid)
        LEFT JOIN Schedule SCH USING (sessionid)
        LEFT JOIN Rooms R USING (roomid)
    WHERE
            F.featurename IS NOT NULL
        AND (S.statusid IN (2, 3, 7) ## Vetted, Scheduled, Assigned
            OR SCH.scheduleid IS NOT NULL)
    ORDER BY
           T.trackname,
           S.sessionid;
EOD;
$report['queries']['features'] =<<<'EOD'
SELECT
        S.sessionid, F.featurename
    FROM 
                  Sessions S
        LEFT JOIN SessionHasFeature USING (sessionid)
        LEFT JOIN Features F USING (featureid)
        LEFT JOIN Schedule SCH USING (sessionid)
    WHERE
            F.featurename IS NOT NULL
        AND (S.statusid IN (2, 3, 7) ## Vetted, Scheduled, Assigned
            OR SCH.scheduleid IS NOT NULL)
    ORDER BY
           S.sessionid;
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
                    <col style="width:12em" />
                    <col style="width:6em" />
                    <col />
                    <col style="width:8em" />
                    <col style="width:16em" />
                    <col style="width:10em" />
                    <col style="width:12em" />
                    <tr>
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Duration</th>
                        <th class="report">Room</th>
                        <th class="report">StartTime</th>
                        <th class="report">Features</th>
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
        <tr>
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
            <td class="report"><xsl:value-of select="@starttime" /></td>
            <td class="report">
                <xsl:variable name="sessionid" select="@sessionid" />
                <xsl:for-each select="/doc/query[@queryName='features']/row[@sessionid=$sessionid]">
                    <div><xsl:value-of select="@featurename" /></div>
                </xsl:for-each>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
