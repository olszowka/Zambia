<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'All Privileges Report';
$report['description'] = 'List all users and their permission roles';
$report['categories'] = array(
    'Zambia Administration Reports' => 170,
);
$report['columns'] = array(
    array(),
    array("orderData" => 2),
    array("visible" => false),
    array()
);
$report['queries'] = [];
$report['queries']['users'] =<<<'EOD'
SELECT
        CD.badgeid, COALESCE(P.pubsname, CONCAT(CD.firstname," ",CD.lastname)) AS name,
        IF(ISNULL(P.pubsname) OR INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)) AS pubsnamesort
    FROM
             CongoDump CD
        JOIN Participants P USING (badgeid) 
    ORDER BY
        CD.lastname;
EOD;
$report['queries']['user_roles'] =<<<'EOD'
SELECT
		CD.badgeid, PR.permrolename
	FROM
			CongoDump CD
	   JOIN UserHasPermissionRole UHPR USING (badgeid)
	   JOIN PermissionRoles PR USING (permroleid)
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='users']/row">
                <table class="report" id="reportTable">
                    <thead>
                        <tr>
                            <th class="report" style="height:3.5em;">Badgeid</th>
                            <th class="report">Name</th>
                            <th></th>
                            <th class="report">Permission roles</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="/doc/query[@queryName='users']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='users']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <tr>
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td class="report"><xsl:value-of select="@name"/></td>
            <td class="report"><xsl:value-of select="@pubsnamesort"/></td>
            <td class="report">
                <xsl:for-each select="/doc/query[@queryName = 'user_roles']/row[@badgeid = $badgeid]">
                    <div><xsl:value-of select="@permrolename"/></div>
                </xsl:for-each>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
