<?php
// Copyright (c) 2018-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Program Participant Thank you note query';
$report['description'] = 'Name, mailing address, and number of scheduled sessions for programming division sessions only.';
$report['categories'] = array(
    'Programming Reports' => 100,
);
$report['queries'] = [];
$report['queries']['participants'] = <<<'EOD'
WITH Sched AS (
    SELECT
            POS.badgeid, COUNT(*) AS sessioncount
        FROM
                 ParticipantOnSession POS
            JOIN Schedule SCH USING (sessionid)
            JOIN Sessions S USING (sessionid)
        WHERE
            S.divisionid = 2 ## programming
        GROUP BY
            POS.badgeid
    )
SELECT
       CD.firstname, CD.lastname, P.pubsname, P.badgeid, Sched.sessioncount, CD.postaddress1, CD.postaddress2,
       CD.postcity, CD.poststate, CD.postzip, CD.postcountry
    FROM
             CongoDump CD
        JOIN Participants AS P USING (badgeid)
        JOIN Sched USING (badgeid) 
    ORDER BY
        CD.lastname, CD.firstname
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
                        <th rowspan="2" class="report">Registration Name</th>
                        <th rowspan="2" class="report">Pubs Name</th>
                        <th rowspan="2" class="report">Badge Id</th>
                        <th rowspan="2" class="report">Number of Sessions</th>
                        <th colspan="6" class="report">Postal Address</th>
                    </tr>
                    <tr>
                        <th class="report">Address 1</th>
                        <th class="report">Address 2</th>
                        <th class="report">City</th>
                        <th class="report">State</th>
                        <th class="report">Zip</th>
                        <th class="report">Country</th>
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
            <td class="report"><xsl:value-of select="@firstname"/><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text><xsl:value-of select="@lastname"/></td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td class="report"><xsl:value-of select="@sessioncount"/></td>
            <td class="report"><xsl:value-of select="@postaddress1"/></td>
            <td class="report"><xsl:value-of select="@postaddress2"/></td>
            <td class="report"><xsl:value-of select="@postcity"/></td>
            <td class="report"><xsl:value-of select="@poststate"/></td>
            <td class="report"><xsl:value-of select="@postzip"/></td>
            <td class="report"><xsl:value-of select="@postcountry"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
