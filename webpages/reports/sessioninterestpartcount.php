<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Interest Counts by Participant';
$report['description'] = 'Just how many panels did each participant sign up for anyway? (Also counts invitations)';
$report['categories'] = array(
    'Programming Reports' => 970,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, COUNT(sessionid) AS interested 
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN ParticipantSessionInterest PSI USING (badgeid)
    WHERE
        P.interested = 1 
    GROUP BY
        P.badgeid
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
                <table class="report">
                    <tr>
                        <th class="report">Badge ID</th>
                        <th class="report">Name for Publications</th>
                        <th class="report">Interested Sessions Count</th>
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
            <td class="report"><xsl:value-of select="@interested" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
