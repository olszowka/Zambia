<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Assigned Session by Session (counts)';
$report['multi'] = 'true';
$report['output_filename'] = 'assignedSessionCounts.csv';
$report['description'] = 'How many people are assinged to each session? (Sorted by track then sessionid; Shows scheduled and unscheduled sessions which have anyone assigned)';
$report['categories'] = array(
    'Events Reports' => 155,
    'Programming Reports' => 155,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, T.trackname, SS.statusname, TY.typename, COUNT(badgeid) AS numAssigned 
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
        JOIN Types TY USING (typeid)
        JOIN ParticipantOnSession POS USING (sessionid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    GROUP BY
        sessionid 
    ORDER BY
        trackname, sessionid;
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
                            <th>Session ID</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>
                                <div>Number of </div>
                                <div>Participants</div>
                            </th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='sessions']/row">
        <tr>
            <td >
                <xsl:value-of select="@trackname" />
            </td>
            <td >
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                </xsl:call-template>
            </td>
            <td >
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td >
                <xsl:value-of select="@statusname" />
            </td>
            <td >
                <xsl:value-of select="@typename" />
            </td>
            <td >
                <xsl:value-of select="@numAssigned" />
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
