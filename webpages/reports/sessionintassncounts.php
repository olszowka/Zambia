<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Assigned, Interested and Not-scheduled Report';
$report['description'] = 'These are sessions that are in need of a home in the schedule';
$report['categories'] = array(
    'Programming Reports' => 160,
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, TY.typename, SS.statusname, T.trackname,
        IFNULL(SUBQA.numInt, 0) AS numInt, IFNULL(SUBQB.numAssgnd, 0) AS numAssgnd
    FROM
                  Sessions S
             JOIN Types TY USING (typeid)
             JOIN SessionStatuses SS USING (statusid)
             JOIN Tracks T USING (trackid)
        LEFT JOIN Schedule SCH USING (sessionid)
        LEFT JOIN (SELECT
                            PSI.sessionid, COUNT(*) AS numInt
                        FROM
                                 ParticipantSessionInterest PSI
                            JOIN Participants P USING (badgeid)
                        WHERE
                            P.interested = 1
                        GROUP BY
			    PSI.sessionid
                  ) AS SUBQA USING (sessionid)
        LEFT JOIN (SELECT
                            POS.sessionid, COUNT(*) AS numAssgnd
                        FROM
                                 ParticipantOnSession POS
                            JOIN Participants P USING (badgeid)
                        WHERE
                            P.interested = 1
                        GROUP BY
			    POS.sessionid
                  ) AS SUBQB USING (sessionid)
    WHERE
	    S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
        AND SCH.scheduleid IS NULL
        AND (    SUBQA.numInt >= 4
             OR SUBQB.numAssgnd >= 3 )
    ORDER BY
        SUBQA.numInt DESC, SUBQB.numAssgnd DESC;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table class="report">
                    <tr>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Track</th>
                        <th class="report">Type</th>
                        <th class="report">Status</th>
                        <th class="report">
                            <div>Num.</div>
                            <div>Interested</div>
                        </th>
                        <th class="report">
                            <div>Num.</div>
                            <div>Assigned</div>
                        </th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='sessions']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
        <tr>
            <td class="report">
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                    <xsl:with-param name="title" select="@title" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@trackname" /></td>
            <td class="report"><xsl:value-of select="@typename" /></td>
            <td class="report"><xsl:value-of select="@statusname" /></td>
            <td class="report" align="right" ><xsl:value-of select="@numInt" /></td>
            <td class="report" align="right" ><xsl:value-of select="@numAssgnd" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
