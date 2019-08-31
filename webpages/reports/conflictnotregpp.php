<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Not Registered  -- Program Participants';
$report['description'] = 'This is a report of program participants only sorted by number of sessions they are on that are actually running, with some registration information. It is useful for cons that comp program participants based on a minimum number of panels. In this case, this report helps make sure people get their comps. Also, participants who have not earned a comp may need some kind of consideration.';
$report['categories'] = array(
    'Registration Reports' => 385,
);
$report['columns'] = array(
    array("width" => "7em"),
    array("width" => "25em", "orderData" => 2),
    array("visible" => false),
    array("width" => "8em"),
    array("width" => "12em")
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, 
        P.pubsname, 
        IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)) AS pubsnamesort,
        IFNULL(CD.regtype, ' ') AS regtype, 
        SU.assigned
    FROM 
                  Participants P 
             JOIN CongoDump CD USING (badgeid)
             JOIN UserHasPermissionRole UHPR USING (badgeid)
        LEFT JOIN (
            SELECT
                    POS.badgeid, COUNT(POS.sessionid) AS assigned
                FROM
                             ParticipantOnSession POS
                        JOIN Schedule SCH USING (sessionid)
                        JOIN Sessions S USING (sessionid)
                    WHERE
                            S.pubstatusid = 2 /* public */
                        AND S.typeid != 7 /* Signing */
                    GROUP BY
                        POS.badgeid
                   ) AS SU USING (badgeid)
    WHERE
            UHPR.permroleid = 3 /* Program Participant */
	    AND SU.assigned > 0
    ORDER BY
        CD.regtype, SU.assigned DESC, pubsnamesort;
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
                        <tr style="height:3.2em;">
                            <th class="report">Badge ID</th>
                            <th class="report">Pubsname</th>
                            <th class="report">Pubsnamesort</th>
                            <th class="report">Reg Type</th>
                            <th class="report">Number of Sessions Assigned</th>
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
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:value-of select="@pubsnamesort"/></td>
            <td class="report"><xsl:value-of select="@regtype"/></td>
            <td class="report"><xsl:value-of select="@assigned"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
