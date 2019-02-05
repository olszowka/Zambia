<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Attending Query (all info)';
$report['description'] = 'Shows who (of program participants only) has responded and if they are attending.';
$report['categories'] = array(
    'Participant Info Reports' => 300,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        CD.firstname, CD.lastname, P.pubsname, P.badgeid, P.interested
	FROM
	         Participants P
	    JOIN CongoDump CD USING (badgeid)
        JOIN UserHasPermissionRole UHPR USING (badgeid)
    WHERE
        UHPR.permroleid = 3 ## Program Participant
    ORDER BY
        P.pubsname;
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
                        <th class="report">Registration Name</th>
                        <th class="report">Pubs Name</th>
                        <th class="report">Badge Id</th>
                        <th class="report"><xsl:text disable-output-escaping="yes">Interested &amp;amp; Attending</xsl:text></th>
                    </tr>
                    <xsl:apply-templates select="/doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='participants']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <tr>
            <td class="report"><xsl:value-of select="@firstname"/><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text><xsl:value-of select="@lastname"/></td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="@interested='0'">Didn't respond</xsl:when>
                    <xsl:when test="@interested='1'">Yes</xsl:when>
                    <xsl:when test="@interested='2'">No</xsl:when>
                    <xsl:otherwise>Didn't log in</xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
