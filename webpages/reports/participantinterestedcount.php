<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participant Interested Count';
$report['description'] = 'Quick count of participants that are interested in attending.';
$report['categories'] = array(
    'Participant Info Reports' => 710,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.interested, count(*) AS interestedCount
    FROM
             Participants P
        JOIN UserHasPermissionRole UHPR USING (badgeid)
    WHERE
        UHPR.permroleid = 3 /* Program Participant */
    GROUP BY
        P.interested;
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
                        <th class="report">Interested Status</th>
                        <th class="report">Count</th>
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
                <xsl:choose>
                    <xsl:when test="@interested='0'">Didn't respond</xsl:when>
                    <xsl:when test="@interested='1'">Yes</xsl:when>
                    <xsl:when test="@interested='2'">No</xsl:when>
                    <xsl:otherwise>Didn't log in</xsl:otherwise>
                </xsl:choose>
            </td>
            <td class="report"><xsl:value-of select="@interestedCount"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
