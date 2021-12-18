<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Sessions with under 4 people assigned';
$report['multi'] = 'true';
$report['output_filename'] = 'fewPeopleAssigned.csv';
$report['description'] = 'Lists all public sessions which are either on the schedule or have anyone assigned and have 3 or fewer participants assigned.  Excludes Dropped, Cancelled, and Duplicate Sessions.';
$report['categories'] = array(
    'Conflict Reports' => 330,
);
$report['columns'] = array(
    null,
    null,
    null,
    null,
    null
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        T.trackname,
        S.sessionid,
        S.title,
        TY.typename,
        COUNT(badgeid) AS assigned
    FROM
                  Sessions S
             JOIN Tracks T USING (trackid)
             JOIN Types TY USING (typeid)
        LEFT JOIN ParticipantOnSession POS USING (sessionid)
    WHERE
            S.pubstatusid = 2 ## Public
        AND S.statusid NOT IN (4,5,10) ## Dropped, Cancelled, or Duplicate
        AND (
                S.sessionid IN (SELECT sessionid FROM Schedule)
             OR S.sessionid IN (SELECT sessionid FROM ParticipantOnSession)
             )
    GROUP BY
        S.sessionid
    HAVING
        assigned < 4
    ORDER BY
        T.trackname, S.sessionid;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table id="reportTable" class="table table-sm table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th>Track</th>
                            <th>Type</th>
                            <th>Session ID</th>
                            <th>Title</th>
                            <th>How Many Assigned</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="/doc/query[@queryName='sessions']/row"/>
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='sessions']/row">
        <tr>
            <td><xsl:value-of select="@trackname"/></td>
            <td><xsl:value-of select="@typename"/></td>
            <td><xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select="@sessionid"/></xsl:call-template></td>
            <td>
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@assigned"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
