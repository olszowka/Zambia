<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Back-to-back Sessions';
$report['description'] = 'Show all cases where a participant is scheduled for two sessions with 15 minutes or fewer between sessions. (Also includes actual overlaps)';
$report['categories'] = array(
    'Conflict Reports' => 1090,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        Subq1.badgeid, P.pubsname, Subq1.sessionid AS sessionid1, S.title AS title1, R.roomname AS roomname1,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',Subq1.starttime),'%a %l:%i %p') AS starttime1,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',ADDTIME(Subq1.starttime, S.duration)),'%l:%i %p') AS endtime1,
        Subq2.sessionid AS sessionid2, S2.title AS title2, R2.roomname AS roomname2,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',Subq2.starttime),'%a %l:%i %p') AS starttime2
    FROM
                   (SELECT
                          POS.badgeid, SCH.sessionid, SCH.starttime, SCH.roomid
                      FROM
                              `Schedule` SCH
                         JOIN ParticipantOnSession POS USING (sessionid)
                   ) Subq1
              JOIN Sessions S ON Subq1.sessionid = S.sessionid
              JOIN Rooms R ON Subq1.roomid = R.roomid
              JOIN Participants P ON Subq1.badgeid = P.badgeid
              JOIN CongoDump CD ON Subq1.badgeid = CD.badgeid
        JOIN (SELECT
                           POS.badgeid, SCH.sessionid, SCH.starttime, SCH.roomid
                        FROM
                                 `Schedule` SCH
                            JOIN ParticipantOnSession POS USING (sessionid)
                    ) Subq2 ON Subq1.badgeid = Subq2.badgeid
              JOIN Sessions S2 ON Subq2.sessionid = S2.sessionid
              JOIN Rooms R2 ON Subq2.roomid = R2.roomid
    WHERE
            Subq1.sessionid != Subq2.sessionid
        AND Subq2.starttime > Subq1.starttime
        AND SUBTIME(Subq2.starttime, ADDTIME(Subq1.starttime, S.duration)) < '00:16:00'
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname,
        Subq1.starttime;
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
                        <th class="report">Pubs Name</th>
                        <th class="report">Session ID 1</th>
                        <th class="report">Title 1</th>
                        <th class="report">Room 1</th>
                        <th class="report">Start Time 1</th>
                        <th class="report">End Time 1</th>
                        <th class="report">Session ID 2</th>
                        <th class="report">Title 2</th>
                        <th class="report">Room 2</th>
                        <th class="report">Start Time 2</th>
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
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@sessionid1" /></td>
            <td class="report"><xsl:value-of select="@title1" /></td>
            <td class="report"><xsl:value-of select="@roomname1" /></td>
            <td class="report"><xsl:value-of select="@starttime1" /></td>
            <td class="report"><xsl:value-of select="@endtime1" /></td>
            <td class="report"><xsl:value-of select="@sessionid2" /></td>
            <td class="report"><xsl:value-of select="@title2" /></td>
            <td class="report"><xsl:value-of select="@roomname2" /></td>
            <td class="report"><xsl:value-of select="@starttime2" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
