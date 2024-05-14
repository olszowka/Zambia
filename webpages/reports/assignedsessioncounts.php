<?php
// Copyright (c) 2022 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Assigned Session by Session (counts)';
$report['description'] = 'How many people are assinged to each session? (Shows scheduled and unscheduled sessions which have anyone assigned)';
$report['categories'] = array(
//  'Events Reports' => 155,
    'Programming Reports' => 155,
);
$report['columns'] = array(
    null,
    null,
    null,
    null,
    null,
    null
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, T.trackname, SS.statusname, TY.typename, SQ.numAssigned 
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN SessionStatuses SS USING (statusid)
        JOIN Types TY USING (typeid)
        JOIN (SELECT 
                        POS.sessionid, COUNT(POS.badgeid) AS numAssigned
                    FROM
                        ParticipantOnSession POS
                    GROUP BY 
                        POS.sessionid
            ) SQ USING (sessionid)
    WHERE
        S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Track</th>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Status</th>
                            <th class="report">Type</th>
                            <th class="report">
                                <div>Number of </div>
                                <div>Participants</div>
                            </th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='sessions']/row">
        <tr class="report">
            <td class="report" >
                <xsl:value-of select="@trackname" />
            </td>
            <td class="report" >
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                </xsl:call-template>
            </td>
            <td class="report" >
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report" >
                <xsl:value-of select="@statusname" />
            </td>
            <td class="report" >
                <xsl:value-of select="@typename" />
            </td>
            <td class="report" >
                <xsl:value-of select="@numAssigned" />
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
