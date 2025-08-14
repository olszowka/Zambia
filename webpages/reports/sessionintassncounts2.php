<?php
// Copyright (c) 2022 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka on 2022-11-05
$report = [];
$report['name'] = 'Panel Sign-up Report';
$report['description'] = 'Show all sessions for which particpants can sign up and how many have';
$report['categories'] = array(
    'Programming Reports' => 160,
);
$report['columns'] = array(
    array(),
    array("width" => "28em"),
    array(),
    array("width" => "28em"),
    array(),
    array(),
    array(),
    array()
);
$report['additionalOptions'] = array("order" => array( array(6, 'desc')));
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, TY.typename, SS.statusname, T.trackname, SUBQ_TAGS.tags,
        IFNULL(SUBQ_INT.numInt, 0) AS numInt, IFNULL(SUBQ_ASSGN.numAssgnd, 0) AS numAssgnd
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
                  ) AS SUBQ_INT USING (sessionid)
        LEFT JOIN (SELECT
                            POS.sessionid, COUNT(*) AS numAssgnd
                        FROM
                                 ParticipantOnSession POS
                            JOIN Participants P USING (badgeid)
                        WHERE
                            P.interested = 1
                        GROUP BY
			                POS.sessionid
                  ) AS SUBQ_ASSGN USING (sessionid)
        LEFT JOIN (SELECT
                            SHT.sessionid, GROUP_CONCAT(TA.tagname SEPARATOR ', ') AS tags
                        FROM
                                 SessionHasTag SHT
                            JOIN Tags TA USING (tagid)
                        GROUP BY
                            SHT.sessionid
                  ) AS SUBQ_TAGS USING (sessionid)    
    WHERE
	        S.statusid IN (2, 3, 7) ## Vetted, Scheduled, Assigned
        AND SCH.scheduleid IS NULL
        AND T.selfselect = 1
        AND TY.selfselect = 1
        AND S.invitedguest = 0
    ORDER BY
        SUBQ_INT.numInt DESC, SUBQ_ASSGN.numAssgnd DESC;
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
                        <tr>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Track</th>
                            <th class="report">Tags</th>
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
                    </thead>
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
            <td class="report report-spacious report-align-right">
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                </xsl:call-template>
            </td>
            <td class="report report-spacious">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select="@sessionid" />
                    <xsl:with-param name="title" select="@title" />
                </xsl:call-template>
            </td>
            <td class="report report-spacious"><xsl:value-of select="@trackname" /></td>
            <td class="report report-spacious"><xsl:value-of select="@tags" /></td>
            <td class="report report-spacious"><xsl:value-of select="@typename" /></td>
            <td class="report report-spacious"><xsl:value-of select="@statusname" /></td>
            <td class="report report-spacious report-align-right"><xsl:value-of select="@numInt" /></td>
            <td class="report report-spacious report-align-right"><xsl:value-of select="@numAssgnd" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
