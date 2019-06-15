<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant # Panel and Constraints';
$report['description'] = 'How many panels does each person want to be on and the other constraints they indicated';
$report['categories'] = array(
    'Participant Info Reports' => 730,
);
$report['columns'] = array_pad(
    array(
        null,
        array("orderData" => 2),
        array("visible" => false)
    ), CON_NUM_DAYS + 6, array("orderable" => false));
$report['queries'] = [];
$report['queries']['days'] =<<<'EOD'
SELECT DISTINCT day, DAYNAME(ADDDATE('$ConStartDatim$', day - 1)) AS dayName FROM ParticipantAvailabilityDays ORDER BY day;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
		P.badgeid, P.pubsname, PA.maxprog, PA.preventconflict, PA.otherconstraints,
        IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)) AS pubsnameSort
	FROM
				  Participants P
			 JOIN CongoDump CD USING (badgeid)
		LEFT JOIN ParticipantAvailability PA USING(badgeid)
	WHERE
		P.interested = 1
	ORDER BY
		IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)), CD.firstname
EOD;
$report['queries']['dayMaxProg'] =<<<'EOD'
SELECT
		P.badgeid, D.day, PAD.maxprog
	FROM
				  Participants P
			 JOIN (SELECT DISTINCT day FROM ParticipantAvailabilityDays) D
		LEFT JOIN ParticipantAvailabilityDays PAD ON P.badgeid = PAD.badgeid AND D.day = PAD.day
	ORDER BY
		P.badgeid, D.day
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row and doc/query[@queryName='dayMaxProg']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr>
                            <th rowspan="2" class="report" style="white-space:nowrap;">Badge ID</th>
                            <th rowspan="2" class="report">Name for Publications</th>
                            <th rowspan="2"></th>
                            <th colspan="{1 + count(doc/query[@queryName='days']/row)}" class="report">Maximum Number of Sessions</th>
                            <th rowspan="2" class="report">Prevent Conflict with these Activities</th>
                            <th rowspan="2" class="report">Participant's Other Scheduling Constraints</th>
                        </tr>
                        <tr>
                            <xsl:apply-templates select="doc/query[@queryName='days']/row" />
                            <th class="report">Total</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row" />
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
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report" style="white-space:nowrap;"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:value-of select="@pubsnameSort"/></td>
            <xsl:apply-templates select="/doc/query[@queryName='dayMaxProg']/row[@badgeid=$badgeid]"/>
            <td class="report"><xsl:value-of select="@maxprog"/></td>
            <td class="report"><xsl:value-of select="@preventconflict"/></td>
            <td class="report"><xsl:value-of select="@otherconstraints"/></td>
        </tr>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='days']/row">
        <th class="report">
            <xsl:value-of select="@dayName" />
        </th>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='dayMaxProg']/row">
        <td class="report">
            <xsl:value-of select="@maxprog" />
        </td>
    </xsl:template>
</xsl:stylesheet>
EOD;
