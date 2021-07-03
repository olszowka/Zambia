<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Not Registered';
$report['multi'] = 'true';
$report['output_filename'] = 'conflictnotreg.csv';
$report['description'] = 'This is a report of participants sorted by number of sessions they are on that are actually running, with some registration information. It is useful for cons that comp program participants based on a minimum number of panels. In this case, this report helps make sure people get their comps. Also, participants who have not earned a comp may need some kind of consideration.';
$report['categories'] = array(
    'Conflict Reports' => 380,
    'Registration Reports' => 380,
);
$report['columns'] = array(
    array("width" => "7em"),
    array("width" => "17em", "orderData" => 2),
    array("visible" => false),
    array("width" => "17em", "orderData" => 4),
    array("visible" => false),
    array("width" => "8em"),
    array("width" => "12em")
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname,
        concat(CD.firstname,' ',CD.lastname) AS name, CONCAT(CD.lastname, CD.firstname) AS nameSort,
        IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)) AS pubsnameSort,
        IFNULL(CD.regtype, ' ') AS regtype, SU.assigned
    FROM 
                  Participants P 
             JOIN CongoDump CD USING (badgeid)
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
	     SU.assigned > 0
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
                <table id="reportTable" class="table table-sm table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th>Badge ID</th>
                            <th>Pubsname</th>
                            <th></th>
                            <th>Name</th>
                            <th></th>
                            <th>Reg Type</th>
                            <th>Number of Sessions Assigned</th>
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
            <td><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td><xsl:value-of select="@pubsname"/></td>
            <td><xsl:value-of select="@pubsnameSort"/></td>
            <td><xsl:value-of select="@name"/></td>
            <td><xsl:value-of select="@nameSort"/></td>
            <td><xsl:value-of select="@regtype"/></td>
            <td><xsl:value-of select="@assigned"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
