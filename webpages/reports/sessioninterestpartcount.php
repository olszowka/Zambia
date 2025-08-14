<?php
// Copyright (c) 2018-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Interest Counts by Participant';
$report['description'] = 'Just how many panels did each participant sign up for anyway? (Also counts invitations)';
$report['categories'] = array(
    'Participant Info Reports' => 970,
);
$report['columns'] = array(null, null, null, null, null, null);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
WITH Interest AS (
    SELECT
            PSI.badgeid, COUNT(*) AS interested
        FROM
            ParticipantSessionInterest PSI
        GROUP BY
            PSI.badgeid
    )
SELECT
        P.badgeid, P.pubsname, CD.firstname, CD.lastname, CD.badgename, IFNULL(Interest.interested, 0) AS interested
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
             JOIN UserHasPermissionRole UHPR USING (badgeid)
        LEFT JOIN Interest USING (badgeid)
    WHERE
            P.interested = 1
        AND UHPR.permroleid = 4 /* B61 Program Participant */
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
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Badge ID</th>
                            <th class="report">Name for Publications</th>
                            <th class="report">First Name</th>
                            <th class="report">Last Name</th>
                            <th class="report">Badge Name</th>
                            <th class="report">Interested Sessions Count</th>
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
        <tr>
            <td class="report"><xsl:value-of select="@badgeid" /></td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@lastname" /></td>
            <td class="report"><xsl:value-of select="@badgename" /></td>
            <td class="report"><xsl:value-of select="@interested" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
