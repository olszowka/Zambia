<?php
// Copyright (c) 2018-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Participants with 1 or 2 sessions';
$report['description'] = 'Program or Event participants with one or two scheduled sessions in Program or Event divisions not counting signings.';
$report['categories'] = array(
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, CD.lastname, CD.firstname, CD.badgename, CD.email, 
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
            `Schedule` SCH
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
        JOIN (
            SELECT
                    POS.badgeid, COUNT(*) AS mycount
                FROM
                        `Schedule`
                    JOIN ParticipantOnSession POS USING (sessionid)
                    JOIN Sessions S USING (sessionid)
                WHERE
                        S.typeid != 10 /* signing */
                    AND S.divisionid IN (2,3) /* Programming, Events */
                GROUP BY
                    POS.badgeid
                HAVING
                        mycount >= 1
                    AND mycount <= 2
            ) AS subq USING (badgeid)
        JOIN Sessions S USING (sessionid)
     WHERE
             S.typeid != 10 /* signing */
         AND S.divisionid IN (2,3) /* Programming, Events */
     ORDER BY
         IF(INSTR(P.pubsname,CD.lastname)>0,CD.lastname,SUBSTRING_INDEX(P.pubsname,' ',-1)),
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
                        <th class="report">Badge ID</th>
                        <th class="report">Name for Publications</th>
                        <th class="report">Last name, first name</th>
                        <th class="report">Badge name</th>
                        <th class="report">email</th>
                        <th class="report">Session start time</th>
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
            <td class="report"><xsl:value-of select="@badgeid" /></td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@lastname" />, <xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@badgename" /></td>
            <td class="report"><xsl:value-of select="@email" /></td>
            <td class="report"><xsl:value-of select="@starttime" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
