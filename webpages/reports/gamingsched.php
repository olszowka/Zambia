<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Gaming Schedule';
$report['multi'] = 'true';
$report['output_filename'] = 'gamingsched.csv';
$report['description'] = 'Full schedule of everything in track gaming (with participants).';
$report['categories'] = array(
    'Gaming Reports' => 550,
);
$report['columns'] = array(
    array("width" => "11em"),
    array("orderData" => 2, "width" => "8em"),
    array("visible" => false),
    array("orderData" => 4, "width" => "7em"),
    array("visible" => false),
    array("width" => "5em"),
    array("width" => "10em"),
    array("width" => "6em"),
    array("width" => "25em"),
    array()
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
        SCH.starttime as starttimeraw, DATE_FORMAT(S.duration,'%i') as durationmin, DATE_FORMAT(S.duration,'%k') as durationhrs,
        S.duration, TY.typename, S.sessionid, S.title, SCH.roomid,
        GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS taglist
    FROM
                  Schedule SCH
             JOIN Sessions S USING (sessionid)
             JOIN Rooms R USING (roomid)
             JOIN Types TY USING (typeid)
        LEFT JOIN SessionHasTag SHT USING (sessionid)
        LEFT JOIN Tags TA USING (tagid)
    WHERE
        S.trackid = 7 /* Gaming */
    GROUP BY
         SCH.scheduleid
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
            <xsl:when test="doc/query[@queryName='schedule']/row">
                <table id="reportTable" class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Room name</th>
                            <th>Start time</th>
                            <th></th>
                            <th>Duration</th>
                            <th></th>
                            <th>Type</th>
                            <th>Tags</th>
                            <th>Session ID</th>
                            <th>Title</th>
                            <th>Participants</th>
                        </tr>

                    </thead>
                    <tbody>
                        <xsl:apply-templates select="doc/query[@queryName='schedule']/row"/>
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">>No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='schedule']/row">
        <xsl:variable name="sessionid" select="@sessionid" />
        <tr>
            <td>
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@starttime" /></td>
            <td><xsl:value-of select="@starttimeraw" /></td>
            <td>
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@duration" /></td>
            <td><xsl:value-of select="@typename" /></td>
            <td><xsl:value-of select="@taglist" /></td>
            <td><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template></td>
            <td>
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td>
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
