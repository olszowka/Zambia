<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Reading Own Works Role';
$report['description'] = 'Show all participants who checked the "Reading my own works" role';
$report['categories'] = array(
    'Participant Info Reports' => 1120,
);
$report['columns'] = array(
    null,
    array("orderData" => 2),
    array("visible" => false),
    null,
    array("orderData" => 5),
    array("visible" => false)
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
		CD.badgeid, CD.firstname, CD.lastname, CONCAT(CD.lastname, CD.firstname) AS nameSort, CD.badgename, P.pubsname,
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)) AS pubsnameSort
	FROM
			 CongoDump CD
		JOIN Participants P USING (badgeid)
		JOIN ParticipantHasRole PHR USING (badgeid)
	WHERE
			PHR.roleid = 2 /* Reading my own works */
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
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Badge Id</th>
                            <th class="report">Pubs Name</th>
                            <th></th>
                            <th class="report">Badge Name</th>
                            <th class="report">Name</th>
                            <th></th>
                        </tr>
                    </thead>
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
            <td class="report"><xsl:value-of select="@pubsnameSort"/></td>
            <td class="report"><xsl:value-of select="@badgename"/></td>
            <td class="report"><xsl:value-of select="@firstname"/><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text><xsl:value-of select="@lastname"/></td>
            <td class="report"><xsl:value-of select="@nameSort"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
