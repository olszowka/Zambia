<?php
// Copyright (c) 2018-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'New Comps Report';
$report['description'] = 'Session counts for each participant.  Rewritten for B61';
$report['categories'] = array(
    'Registration Reports' => 100,
);
$report['columns'] = array(
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array(),
    array()
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, CD.regtype, RT.message, CD.firstname, CD.lastname, CD.badgename, P.pubsname, CD.email,
        CD.phone, CD.postaddress1, CD.postaddress2, CD.postcity, CD.poststate, CD.postzip, CD.postcountry,
        IFNULL(subQ.blp, 0) as blp, IFNULL(subQ.ag, 0) as ag, IFNULL(subQ.re, 0) as re, IFNULL(subQ.kk, 0) as kk,
        IFNULL(subQ.other, 0) AS other, IFNULL(subQ.total, 0) as total
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN RegTypes RT USING (regtype) 
        LEFT JOIN (
            SELECT
                    POS.badgeid,
                    SUM(IF((S.sessionid = 340), 1, 0)) AS blp, /* book launch party */
                    SUM(IF((S.typeid = 8), 1, 0)) AS ag, /* autographings */
                    SUM(IF((S.typeid = 7), 1, 0)) AS re, /* reading */
                    SUM(IF((S.typeid = 1), 1, 0)) AS kk, /* kaffeeklatsches */
                    SUM(IF((S.sessionid != 340 AND S.typeid != 8 AND S.typeid != 7 AND S.typeid != 1), 1, 0)) AS other, /* see above */
                    Count(*) AS total
                FROM
                         Schedule SCH
                    JOIN ParticipantOnSession POS USING (sessionid)
                    JOIN Sessions S USING (sessionid)
                WHERE
                    S.pubstatusid = 2 /* Public */
                GROUP BY POS.badgeid    
            ) AS subQ USING (badgeid)
    WHERE
        P.interested = 1
    ORDER BY
        CD.lastname, CD.firstname;

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
                        <tr style="height:4em;">
                            <th class="report" >Badge ID</th>
                            <th class="report" >Registration Type</th>
                            <th class="report" >Registration Description</th>
                            <th class="report" >First Name</th>
                            <th class="report" >Last Name</th>
                            <th class="report" >Badge Name</th>
                            <th class="report" >Pubs. Name</th>
                            <th class="report" >Email</th>
                            <th class="report" >Phone</th>
                            <th class="report" >Address</th>
                            <th class="report" >Address 2</th>
                            <th class="report" >City</th>
                            <th class="report" >State</th>
                            <th class="report" >Zip</th>
                            <th class="report" >Country</th>
                            <th class="report" >B. Launch P.</th>
                            <th class="report" >Autogr.</th>
                            <th class="report" >Reading</th>
                            <th class="report" >KK's</th>
                            <th class="report" >Other</th>
                            <th class="report" >Total</th>
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
        <tr class="mainrow">
            <td class="report"><xsl:value-of select="@badgeid" /></td>
            <td class="report"><xsl:value-of select="@regtype" /></td>
            <td class="report"><xsl:value-of select="@message" /></td>
            <td class="report"><xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@lastname" /></td>
            <td class="report"><xsl:value-of select="@badgename" /></td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@email" /></td>
            <td class="report"><xsl:value-of select="@phone" /></td>
            <td class="report"><xsl:value-of select="@postaddress1" /></td>
            <td class="report"><xsl:value-of select="@postaddress2" /></td>
            <td class="report"><xsl:value-of select="@postcity" /></td>
            <td class="report"><xsl:value-of select="@poststate" /></td>
            <td class="report"><xsl:value-of select="@postzip" /></td>
            <td class="report"><xsl:value-of select="@postcountry" /></td>
            <td class="report"><xsl:value-of select="@blp" /></td>
            <td class="report"><xsl:value-of select="@ag" /></td>
            <td class="report"><xsl:value-of select="@re" /></td>
            <td class="report"><xsl:value-of select="@kk" /></td>
            <td class="report"><xsl:value-of select="@other" /></td>
            <td class="report"><xsl:value-of select="@total" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
