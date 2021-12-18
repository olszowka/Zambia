<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Categorized Session Count Report ';
$report['multi'] = 'true';
$report['output_filename'] = 'partSessCategoryCount.csv';
$report['description'] = 'Show count of how many sessions each participant is scheduled for broken down by division (disregarding signings)';
$report['categories'] = array(
    'Registration Reports' => 780,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.pubsname, P.badgeid, CD.regtype, CD.email, CD.lastname, CD.firstname,
        SUM(IF(S.divisionid=2 OR S.divisionid=8,1,0)) AS prog_child,
        SUM(IF(S.divisionid=3,1,0)) AS events,
        SUM(IF(S.divisionid!=2 AND S.divisionid!=3 AND S.divisionid!=8,1,0)) AS other
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantOnSession POS USING (badgeid)
        JOIN Sessions S USING (sessionid) 
        JOIN Schedule SCH USING (sessionid)
    WHERE
            S.typeid != 7 /* signing */
        AND S.pubstatusid = 2 /* published/public */
    GROUP BY
        P.badgeid
    ORDER BY
        CD.lastname;
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
                            <th >Badge ID</th>
                            <th >Publication Name</th>
                            <th >Last Name</th>
                            <th >First Name</th>
                            <th >Email</th>
                            <th >Registration Type</th>
                            <th >Number of Programming or <br />Childrens' Services Sessions</th>
                            <th >Number of Events Sessions</th>
                            <th >Number of Other Sessions</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td><xsl:value-of select="@badgeid" /></td>
            <td>
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@lastname" /></td>
            <td><xsl:value-of select="@firstname" /></td>
            <td><xsl:value-of select="@email" /></td>
            <td><xsl:value-of select="@regtype" /></td>
            <td><xsl:value-of select="@prog_child" /></td>
            <td><xsl:value-of select="@events" /></td>
            <td><xsl:value-of select="@other" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
