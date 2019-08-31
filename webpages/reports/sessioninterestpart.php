<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Session Interest by participant (all info)';
$report['description'] = 'Shows who has expressed interest in each session, how they ranked it, what they said, if they will moderate... Large Report. (All data included including for invited sessions.) order by participant';
$report['categories'] = array(
    'Programming Reports' => 980,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, T.trackname, S.sessionid, S.title,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
        PSI.rank, PSI.willmoderate, PSI.comments
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
             JOIN ParticipantSessionInterest PSI USING (badgeid)
             JOIN Sessions S USING (sessionid)
             JOIN Tracks T USING (trackid)
             JOIN Types TY USING (typeid)
        LEFT JOIN Schedule SCH USING (sessionid)
    WHERE
            P.interested = 1
        AND T.selfselect = 1
        AND TY.selfselect = 1
        AND S.invitedguest = 0
        AND S.statusid IN (1, 2, 3, 6, 7) ## Brainstorm, Vetted, Scheduled, Edit Me, Assigned
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname,
        T.trackname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table class="report">
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:10%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:5%" />
                    <col style="width:50%" />
                    <tr>
                        <th class="report">Badge ID</th>
                        <th class="report">Pubsname</th>
                        <th class="report">Track</th>
                        <th class="report">Session ID</th>
                        <th class="report">Title</th>
                        <th class="report">Start Time</th>
                        <th class="report">Duration</th>
                        <th class="report">Rank</th>
                        <th class="report">Moderator</th>
                        <th class="report">Comments</th>
                    </tr>
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
            <td class="report">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
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
            <td class="report"><xsl:value-of select="@starttime" /></td>
            <td class="report">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
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
