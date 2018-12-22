<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Participant Number of Sessions';
$report['description'] = 'Compare number of sessions participants requested with the number of which they were assigned';
$report['categories'] = array(
    'Conflict Reports' => 420,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, PA.maxprog, IFNULL(PA.maxprog, 0) AS nsmaxprog,
        subqa.fridaymaxprog, IFNULL(subqa.fridaymaxprog, 0) AS nsfridaymaxprog,
        subqa.saturdaymaxprog, IFNULL(subqa.saturdaymaxprog, 0) AS nssaturdaymaxprog,
        subqa.sundaymaxprog, IFNULL(subqa.sundaymaxprog, 0) AS nssundaymaxprog,
        subqa.mondaymaxprog, IFNULL(subqa.mondaymaxprog, 0) AS nsmondaymaxprog,
        IFNULL(subqb.frisched, 0) AS frisched, IFNULL(subqb.satsched, 0) AS satsched,
        IFNULL(subqb.sunsched, 0) AS sunsched, IFNULL(subqb.monsched, 0) AS monsched,
        IFNULL(subqb.totsched, 0) AS totsched
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN ParticipantAvailability PA USING (badgeid)
        LEFT JOIN (SELECT
                            badgeid,
                            SUM(IF(day=1,maxprog,0)) AS fridaymaxprog,
                            SUM(IF(day=2,maxprog,0)) AS saturdaymaxprog,
                            SUM(IF(day=3,maxprog,0)) AS sundaymaxprog,
                            SUM(IF(day=4,maxprog,0)) AS mondaymaxprog
                        FROM
                            ParticipantAvailabilityDays
                        GROUP BY
                            badgeid
                    ) AS subqa USING (badgeid)
        LEFT JOIN (SELECT
                            POS.badgeid, 
                            SUM(IF(SCH.starttime<'24:00:00',1,0)) AS frisched, 
                            SUM(IF((SCH.starttime>='24:00:00' 
                                 && SCH.starttime<'48:00:00'),1,0)) AS satsched, 
                            SUM(IF((SCH.starttime>='48:00:00'
                                 && SCH.starttime<'72:00:00'),1,0)) AS sunsched, 
                            SUM(IF(SCH.starttime>='72:00:00',1,0)) AS monsched, 
                            COUNT(*) AS totsched 
                        FROM
                                 ParticipantOnSession POS
                            JOIN Sessions S USING (sessionid)
                            JOIN Schedule SCH USING (sessionid)
                        WHERE
                            S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
                        GROUP BY
                            badgeid
                    ) AS subqb USING (badgeid)
    WHERE
        P.interested = 1
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
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
                        <th rowspan="2" class="report">Badge ID</th>
                        <th rowspan="2" class="report">Participant</th>
                        <th colspan="2" class="report">Friday</th>
                        <th colspan="2" class="report">Saturday</th>
                        <th colspan="2" class="report">Sunday</th>
                        <th colspan="2" class="report">Monday</th>
                        <th colspan="2" class="report">Total</th>
                    </tr>
                    <tr>
                        <th class="report">Avail.</th>
                        <th class="report">Sched.</th>
                        <th class="report">Avail.</th>
                        <th class="report">Sched.</th>
                        <th class="report">Avail.</th>
                        <th class="report">Sched.</th>
                        <th class="report">Avail.</th>
                        <th class="report">Sched.</th>
                        <th class="report">Avail.</th>
                        <th class="report">Sched.</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select="@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@pubsname" /></td>
            <td class="report"><xsl:value-of select="@fridaymaxprog" /></td>
            <td class="report">
                <xsl:if test="@frisched > @nsfridaymaxprog">
                    <xsl:attribute name="style">
                        background-color:#ff7070;font-weight:bold;
                    </xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@frisched" />
            </td>
            <td class="report"><xsl:value-of select="@saturdaymaxprog" /></td>
            <td class="report">
                <xsl:if test="@satsched > @nssaturdaymaxprog">
                    <xsl:attribute name="style">
                        background-color:#ff7070;font-weight:bold;
                    </xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@satsched" />
            </td>
            <td class="report"><xsl:value-of select="@sundaymaxprog" /></td>
            <td class="report">
                <xsl:if test="@sunsched > @nssundaymaxprog">
                    <xsl:attribute name="style">
                        background-color:#ff7070;font-weight:bold;
                    </xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@sunsched" />
            </td>
            <td class="report"><xsl:value-of select="@mondaymaxprog" /></td>
            <td class="report">
                <xsl:if test="@monsched > @nsmondaymaxprog">
                    <xsl:attribute name="style">
                        background-color:#ff7070;font-weight:bold;
                    </xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@monsched" />
            </td>
            <td class="report"><xsl:value-of select="@maxprog" /></td>
            <td class="report">
                <xsl:if test="@totsched > @nsmaxprog">
                    <xsl:attribute name="style">
                        background-color:#ff7070;font-weight:bold;
                    </xsl:attribute>
                </xsl:if>
                <xsl:value-of select="@totsched" />
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
