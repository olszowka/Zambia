<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Autographing Role';
$report['description'] = 'Show all participants who selected the "Autographing" role.';
$report['categories'] = array(
    'Participant Info Reports' => 1110,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
		CD.badgeid, CD.firstname, CD.lastname, CD.badgename, P.pubsname
	FROM
			 CongoDump CD
		JOIN Participants P USING (badgeid)
		JOIN ParticipantHasRole PHR USING (badgeid)
	WHERE
			PHR.roleid = 3 /* Autographing */
		AND	P.interested = 1
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table class="report">
                    <tr>
                        <th class="report">Badge Id</th>
                        <th class="report">Pubs Name</th>
                        <th class="report">Badge Name</th>
                        <th class="report">Last Name, First Name</th>
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
        <tr>
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:value-of select="@badgename"/></td>
            <td class="report"><xsl:value-of select="@lastname"/><xsl:text disable-output-escaping="yes">,&amp;nbsp;</xsl:text><xsl:value-of select="@firstname"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
