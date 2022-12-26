<?php
// Copyright (c) 2022 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Interest Report (all info)';
$report['description'] = 'Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report. (All data included including for invited sessions.)';
$report['categories'] = array(
    'Programming Reports' => 990,
);
$report['columns'] = array(
    array(),
    array(),
    array("width" => "28em"),
    array(),
    array(),
    array(),
    array(),
    array()
);
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        T.trackname, S.sessionid, S.title, P.pubsname, P.badgeid, PSI.rank, PSI.willmoderate,
        PSI.comments
    FROM
             Sessions S
        JOIN Tracks T USING (trackid)
        JOIN Types TY USING (typeid)
        JOIN ParticipantSessionInterest PSI USING (sessionid)
        JOIN Participants P USING (badgeid)
    WHERE
            P.interested = 1
        AND T.selfselect = 1
        AND TY.selfselect = 1
        AND S.invitedguest = 0
        AND S.statusid IN (2, 3, 7) ## Vetted, Scheduled, Assigned
    ORDER BY
        T.trackname, S.title;
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
                            <th class="report">Track</th>
                            <th class="report">Session ID</th>
                            <th class="report">Title</th>
                            <th class="report">Pubsname</th>
                            <th class="report">Badge ID</th>
                            <th class="report">Rank</th>
                            <th class="report">Moderator</th>
                            <th class="report">Comments</th>
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
            <td class="report"><xsl:value-of select="@trackname" /></td>
            <td class="report">
                <xsl:call-template name="showSessionid">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@rank" /></td>
            <td class="report">
                <xsl:if test="@willmoderate='1'">Yes</xsl:if>
            </td>
            <td class="report"><xsl:value-of select="@comments" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
