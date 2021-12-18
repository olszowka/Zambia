<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Interest Report (counts)';
$report['multi'] = 'true';
$report['output_filename'] = 'sessionInterestCounts.csv';
$report['description'] = 'For each session, show number of participants who have put it on their interest list. (Excludes invited guest sessions.)';
$report['categories'] = array(
    'Programming Reports' => 960,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, T.trackname, count(P.badgeid) as partCount
    FROM
                  Sessions S 
             JOIN Tracks T USING (trackid)
             JOIN Types Ty USING (typeid)
        LEFT JOIN ParticipantSessionInterest PSI USING (sessionid)
        LEFT JOIN Participants P ON PSI.badgeid = P.badgeid AND P.interested = 1
    WHERE
            T.selfselect = 1
        AND Ty.selfselect = 1
        AND S.invitedguest = 0
        AND S.statusid IN (2,3,7) ## Vetted, Scheduled, Assigned
    GROUP BY
        S.sessionid 
    ORDER BY
        T.display_order, 
        S.sessionid;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table id="reportTable" class="table table-sm table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th>Track</th>
                            <th>Session ID</th>
                            <th>Title</th>
                            <th>
                                <div>Number of</div>
                                <div>Participants</div>
                            </th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
        <tr>
            <td><xsl:value-of select="@trackname" /></td>
            <td>
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                </xsl:call-template>
            </td>
            <td>
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                    <xsl:with-param name="title" select="@title" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@partCount" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
