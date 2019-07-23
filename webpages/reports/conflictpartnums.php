<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Participant Number of Sessions';
$report['description'] = 'Compare number of sessions participants requested with the number of which they were assigned';
$report['categories'] = array(
    'Conflict Reports' => 420,
);
$report['columns'] = array(
    null,
    array("orderData" => 2),
    array("visible" => false),
    array("orderData" => 4),
    array("visible" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false)
);
$report['queries'] = [];
$report['queries']['availability'] =<<<'EOD'
SELECT
        PAD.badgeid, PAD.day, PAD.maxprog
    FROM
             ParticipantAvailabilityDays PAD
        JOIN Participants P USING(badgeid)
    WHERE
        P.interested = 1 /* interested */;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, CONCAT(CD.firstname,' ',CD.lastname) AS name, CONCAT(CD.lastname, CD.firstname) AS nameSort,
        IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)) AS pubsnameSort,
        PA.maxprog
    FROM
             Participants P
        JOIN ParticipantAvailability PA USING(badgeid)
        JOIN CongoDump CD USING (badgeid)
    WHERE
            P.interested = 1 /* interested */
        AND EXISTS (SELECT *
                        FROM
                                 Schedule SCH
                            JOIN ParticipantOnSession POS USING (sessionid)
                        WHERE
                            POS.badgeid = P.badgeid
                    );
EOD;
$report['queries']['schedules'] =<<<'EOD'
SELECT
        P.badgeid, 1 + (hour(SCH.starttime) DIV 24) AS day, COUNT(*) AS sessionCount
    FROM
             Participants P
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Schedule SCH USING (sessionid)
    WHERE
        P.interested = 1 /* interested */
    GROUP BY
        P.badgeid, day;
EOD;
$report['queries']['days'] =<<<'EOD'
SELECT DISTINCT
        PAD.day
    FROM
        ParticipantAvailabilityDays PAD
    ORDER BY
        PAD.day;
EOD;
$headerRow1Days = "";
$headerRow2Days = "";
for ($day=1; $day<=CON_NUM_DAYS; $day++) {
    $headerRow1Days .= "<th colspan=\"2\" class=\"report\">{$daymap["long"][$day]}</th>";
    $headerRow2Days .= "<th class=\"report\">Avail.</th><th class=\"report\">Sched.</th>";
}
$report['xsl'] =<<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr>
                            <th rowspan="2" class="report">Badge ID</th>
                            <th rowspan="2" class="report">Name for Publications</th>
                            <th rowspan="2">X</th>
                            <th rowspan="2" class="report">Name</th>
                            <th rowspan="2">Y</th>
$headerRow1Days
                            <th colspan="2" class="report">Total</th>
                        </tr>
                        <tr>
$headerRow2Days
                            <th class="report">Avail.</th>
                            <th class="report">Sched.</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <tr>
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select="@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@pubsname" /></td>
            <td class="report"><xsl:value-of select="@pubsnameSort" /></td>
            <td class="report"><xsl:value-of select="@name" /></td>
            <td class="report"><xsl:value-of select="@nameSort" /></td>
            <xsl:for-each select="/doc/query[@queryName='days']/row">
                <xsl:call-template name="showInfoForDay">
                    <xsl:with-param name="badgeid" select="\$badgeid" />
                    <xsl:with-param name="day" select="@day" />
                </xsl:call-template>
            </xsl:for-each>
            <xsl:call-template name="showTotalInfo">
                <xsl:with-param name="badgeid" select="\$badgeid" />
            </xsl:call-template>
        </tr>
    </xsl:template>
    <xsl:template name="showInfoForDay">
        <xsl:param name="badgeid" />
        <xsl:param name="day" />
        <xsl:variable name="availableInfo" select="/doc/query[@queryName='availability']/row[@day=\$day][@badgeid=\$badgeid]/@maxprog" />
        <xsl:variable name="scheduleInfo" select="/doc/query[@queryName='schedules']/row[@day=\$day][@badgeid=\$badgeid]/@sessionCount" />
        <xsl:variable name="availableCount">
            <xsl:choose>
                <xsl:when test="count(\$availableInfo) > 0">
                    <xsl:value-of select="\$availableInfo" />
                </xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="scheduledCount">
            <xsl:choose>
                <xsl:when test="count(\$scheduleInfo) > 0">
                    <xsl:value-of select="\$scheduleInfo" />
                </xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <td class="report"><xsl:value-of select="\$availableInfo" /></td>
        <td>
            <xsl:attribute name="class">
                <xsl:choose>
                    <xsl:when test="\$scheduledCount > \$availableCount">report highlight1</xsl:when>
                    <xsl:otherwise>report</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="\$scheduledCount" />
        </td>
    </xsl:template>
    <xsl:template name="showTotalInfo">
        <xsl:param name="badgeid" />
        <xsl:variable name="availableInfo" select="/doc/query[@queryName='participants']/row[@badgeid=\$badgeid]/@maxprog" />
        <xsl:variable name="scheduleInfo" select="/doc/query[@queryName='schedules']/row[@badgeid=\$badgeid]/@sessionCount" />
        <xsl:variable name="availableCount">
            <xsl:choose>
                <xsl:when test="count(\$availableInfo) > 0">
                    <xsl:value-of select="\$availableInfo" />
                </xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <xsl:variable name="scheduledCount">
            <xsl:choose>
                <xsl:when test="count(\$scheduleInfo) > 0">
                    <xsl:value-of select="sum(\$scheduleInfo)" />
                </xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:variable>
        <td class="report"><xsl:value-of select="\$availableInfo" /></td>
        <td>
            <xsl:attribute name="class">
                <xsl:choose>
                    <xsl:when test="\$scheduledCount > \$availableCount">report highlight1</xsl:when>
                    <xsl:otherwise>report</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:value-of select="\$scheduledCount" />
        </td>
    </xsl:template>

</xsl:stylesheet>
EOD;
