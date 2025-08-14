<?php
// Copyright (c) 2018-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Sessions with no moderator';
$report['description'] = 'Lists all public sessions from the schedule which have at least one participant assigned, but no moderator.';
$report['categories'] = array(
    'Conflict Reports' => 350,
);
$report['columns'] = array(
    null,
    null,
    null,
    null,
    null
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
WITH ModSchedCount AS (
    SELECT
            sessionid, count(*) as partcount
        FROM
                 Schedule SCH
            JOIN ParticipantOnSession POS USING (sessionid)
        WHERE
            POS.moderator = 1
        GROUP BY
            sessionid
),
PartSchedList AS (
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
        T.trackname,
        S.sessionid,
        S.title,
        TY.typename,
        PartSchedList.partlist
    FROM
                  Schedule SCH
             JOIN Sessions S USING (sessionid)
             JOIN Tracks T USING (trackid)
             JOIN Types TY USING (typeid)
        LEFT JOIN ModSchedCount USING (sessionid)
        LEFT JOIN PartSchedList USING (sessionid)
    WHERE
            S.pubstatusid = 2 /* Public */
        AND IFNULL(ModSchedCount.partcount, 0) = 0
    ORDER BY
        T.trackname, S.sessionid;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Track</th>
                            <th class="report">Type</th>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Participants Assigned</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="/doc/query[@queryName='sessions']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='sessions']/row">
        <tr>
            <td class="report"><xsl:value-of select="@trackname"/></td>
            <td class="report"><xsl:value-of select="@typename"/></td>
            <td class="report"><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select="@sessionid"/></xsl:call-template></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@partlist"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
