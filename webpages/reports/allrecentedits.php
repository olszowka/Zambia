<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'All Recent Edits';
$report['description'] = 'All edits to sessions, session participants or the schedule made within the past 10 days.';
$report['categories'] = array(
    'Events Reports' => 80,
    'Programming Reports' => 80,
    'GOH Reports' => 80,
    'Publication Reports' => 80
);
$report['queries'] = [];
$report['queries']['Days'] =<<<'EOD'
(SELECT
        datediff(now(), SEH.timestamp) AS days
    FROM
        SessionEditHistory SEH
    WHERE
        datediff(now(), SEH.timestamp) < 10)
UNION
(SELECT
        datediff(now(), POSH.createdts)
    FROM
        ParticipantOnSessionHistory POSH
    WHERE
        datediff(now(), POSH.createdts) < 10)
UNION
(SELECT
        datediff(now(), POSH.inactivatedts)
    FROM
        ParticipantOnSessionHistory POSH
    WHERE
        datediff(now(), POSH.inactivatedts) < 10)
    ORDER BY
        days;
EOD;
$report['queries']['SessionDays'] =<<<'EOD'
SELECT
        SUBQA.sessionid, SUBQA.days, TR.trackname
    FROM
        (
            (SELECT
                    SEH.sessionid, datediff(now(), SEH.timestamp) AS days
                FROM
                    SessionEditHistory SEH
                WHERE
                    datediff(now(), SEH.timestamp) < 10)
        UNION
            (SELECT
                    POSH.sessionid, datediff(now(), POSH.createdts)
                FROM
                    ParticipantOnSessionHistory POSH
                WHERE
                    datediff(now(), POSH.createdts) < 10)
        UNION
            (SELECT
                    POSH.sessionid, datediff(now(), POSH.inactivatedts)
                FROM
                    ParticipantOnSessionHistory POSH
                WHERE
                    datediff(now(), POSH.inactivatedts) < 10)
        ) SUBQA
        JOIN Sessions S USING (sessionid)
        JOIN Tracks TR USING (trackid)
    ORDER BY
        SUBQA.days, TR.trackname, SUBQA.sessionid;
EOD;
$report['queries']['Sessions'] =<<<'EOD'
SELECT
        TR.trackname, S.title, SS.statusname, SCH.roomid, R.roomname, S.sessionid,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
                  Sessions S
             JOIN Tracks TR using (trackid)
             JOIN SessionStatuses SS using (statusid)
        LEFT JOIN Schedule SCH using (sessionid)
        LEFT JOIN Rooms R using (roomid)
    WHERE
           EXISTS (
                SELECT * From SessionEditHistory SEH
                    WHERE
                            SEH.sessionid = S.sessionid
                        AND datediff(now(), SEH.timestamp) < 10
                )
        OR EXISTS (
                SELECT * From ParticipantOnSessionHistory POSH
                    WHERE
                            POSH.sessionid = S.sessionid
                        AND datediff(now(), POSH.createdts) < 10
                )
        OR EXISTS (
                SELECT * From ParticipantOnSessionHistory POSH
                    WHERE
                            POSH.sessionid = S.sessionid
                        AND datediff(now(), POSH.inactivatedts) < 10
                );
EOD;
$report['queries']['Persons'] =<<<'EOD'
SELECT
        P.badgeid, IF(P.pubsname IS NULL OR P.pubsname = "", CONCAT(CD.firstname, " ", CD.lastname), P.pubsname) AS name
    FROM
             Participants P
        JOIN CongoDump CD USING (badgeid)
    WHERE
           EXISTS (
                SELECT *
                        FROM
                    ParticipantOnSessionHistory POSH
                        WHERE
                                POSH.badgeid = P.badgeid
                            AND (   DATEDIFF(NOW(), POSH.inactivatedts) < 10
                                 OR DATEDIFF(NOW(), POSH.createdts) < 10
                                )
                )
        OR EXISTS (
                SELECT *
                        FROM
                    ParticipantOnSessionHistory POSH
                        WHERE
                                POSH.createdbybadgeid = P.badgeid
                            AND DATEDIFF(NOW(), POSH.createdts) < 10
                )
        OR EXISTS (
                SELECT *
                        FROM
                    ParticipantOnSessionHistory POSH
                        WHERE
                                POSH.inactivatedbybadgeid = P.badgeid
                            AND DATEDIFF(NOW(), POSH.inactivatedts) < 10
                )
        OR EXISTS (
                SELECT *
                        FROM
                    SessionEditHistory SEH
                        WHERE
                                SEH.badgeid = P.badgeid
                            AND DATEDIFF(NOW(), SEH.timestamp) < 10
                );
EOD;
$report['queries']['Timestamps'] =<<<'EOD'
SELECT
        SEH.timestamp, DATE_FORMAT(SEH.timestamp, "%c/%e/%y %l:%i %p") AS formattedts, DATEDIFF(NOW(), SEH.timestamp) AS days, sessionid, badgeid
    FROM
        SessionEditHistory SEH
    WHERE
        DATEDIFF(NOW(), SEH.timestamp) < 10
UNION
SELECT 
        POSH.createdts, DATE_FORMAT(POSH.createdts, "%c/%e/%y %l:%i %p") AS formattedts, DATEDIFF(NOW(), POSH.createdts) AS days, sessionid, createdbybadgeid
    FROM
        ParticipantOnSessionHistory POSH
    WHERE
        DATEDIFF(NOW(), POSH.createdts) < 10
UNION
SELECT 
        POSH.inactivatedts, DATE_FORMAT(POSH.inactivatedts, "%c/%e/%y %l:%i %p") AS formattedts, DATEDIFF(NOW(), POSH.inactivatedts) AS days, sessionid, inactivatedbybadgeid
    FROM
        ParticipantOnSessionHistory POSH
    WHERE
        DATEDIFF(NOW(), POSH.inactivatedts) < 10;
EOD;
$report['queries']['ParticipantEdits'] =<<<'EOD'
SELECT
        POSH.badgeid, POSH.sessionid, POSH.moderator, POSH.createdts, POSH.createdbybadgeid, POSH.inactivatedts, POSH.inactivatedbybadgeid
    FROM
        ParticipantOnSessionHistory POSH
    WHERE
           DATEDIFF(NOW(), POSH.createdts) < 10
        OR DATEDIFF(NOW(), POSH.inactivatedts) < 10;
EOD;
$report['queries']['SessionEdits'] =<<<'EOD'
SELECT
        SEH.timestamp, SEH.sessionid, SEH.badgeid, SEH.editdescription, SS.statusname, SEC.description
    FROM
             SessionEditHistory SEH
        JOIN SessionEditCodes SEC USING (sessioneditcode)
        JOIN SessionStatuses SS USING(statusid) 
    WHERE
        DATEDIFF(NOW(), SEH.timestamp) < 10;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='Days']/row">
                <xsl:apply-templates select="doc/query[@queryName='Days']/row" />
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='Days']/row">
        <xsl:variable name="days" select="@days" />
        <div class="well well-success well-small">
            <h3 class="text-center">
                <xsl:text>Changes from </xsl:text>
                <xsl:choose>
                    <xsl:when test="$days='0'">today</xsl:when>
                    <xsl:when test="$days='1'">1 day ago</xsl:when>
                    <xsl:otherwise><xsl:value-of select="$days" /> days ago</xsl:otherwise>
                </xsl:choose>
            </h3>
        </div>
        <xsl:apply-templates select="/doc/query[@queryName='SessionDays']/row[@days=$days]" >
            <xsl:sort select="@trackname" />
        </xsl:apply-templates>
    </xsl:template>
    
    <xsl:template match="doc/query[@queryName='SessionDays']/row">
        <xsl:variable name="days" select="@days" />
        <xsl:variable name="sessionid" select="@sessionid" />
        <xsl:variable name="sessionrow" select="/doc/query[@queryName='Sessions']/row[@sessionid=$sessionid]" />
        <div class="row-fluid">
            <div class="span10 offset1">
                <div class="row-fluid grid-table-row info">
                    <div class="span3 grid-table-cell"><xsl:value-of select="@trackname" /></div>
                    <div class="span6 grid-table-cell">
                        <xsl:call-template name="showSessionTitle">
                            <xsl:with-param name="sessionid" select = "$sessionid" />
                            <xsl:with-param name="title" select = "$sessionrow/@title" />
                        </xsl:call-template>
                    </div>
                    <div class="span3 grid-table-cell">
                        <xsl:call-template name="showSessionid">
                            <xsl:with-param name="sessionid" select = "$sessionid" />
                        </xsl:call-template>
                    </div>
                </div>
                <div class="row-fluid grid-table-row info">
                    <div class="span3 grid-table-cell"><xsl:value-of select="$sessionrow/@statusname" /></div>
                    <div class="span3 grid-table-cell">
                        <xsl:call-template name="showRoomName">
                            <xsl:with-param name="roomid" select = "$sessionrow/@roomid" />
                            <xsl:with-param name="roomname" select = "$sessionrow/@roomname" />
                        </xsl:call-template>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </div>
                    <div class="span3 grid-table-cell">
                        <xsl:value-of select="$sessionrow/@starttime" />
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </div>
                    <div class="span3 grid-table-cell">
                        <xsl:call-template name="showSessionHistoryLink">
                            <xsl:with-param name="sessionid" select = "$sessionid" />
                        </xsl:call-template>
                    </div>
                </div>
            </div>
        </div>
        <xsl:apply-templates select="/doc/query[@queryName='Timestamps']/row[@days=$days and @sessionid=$sessionid]" >
            <xsl:sort select="@timestamp" order="descending" />
        </xsl:apply-templates>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='Timestamps']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <xsl:variable name="timestamp" select="@timestamp" />
        <div class="row-fluid">
            <div class="span6 offset3 za-sessionHistory-editBanner">
                <div class="row-fluid">
                    <div class="span9 offset3">
                        <xsl:value-of select="/doc/query[@queryName='Persons']/row[@badgeid=$badgeid]/@name" />
                        <xsl:text> (</xsl:text><xsl:value-of select="$badgeid" /><xsl:text>) </xsl:text>
                        <xsl:value-of select="@formattedts" />
                    </div>
                </div>
            </div>
        </div>
        <xsl:call-template name="processModeratorEdit">
            <xsl:with-param name="timestamp" select = "@timestamp" />
        </xsl:call-template>
        <xsl:apply-templates mode="additions" select="/doc/query[@queryName='ParticipantEdits']/row[@createdts = $timestamp]" />
        <xsl:apply-templates mode="deletions" select="/doc/query[@queryName='ParticipantEdits']/row[@inactivatedts = $timestamp]" />
        <xsl:apply-templates select="/doc/query[@queryName='SessionEdits']/row[@timestamp = $timestamp]" />

    </xsl:template>

    <xsl:template name="processModeratorEdit">
        <xsl:param name="timestamp" />
        <xsl:variable name="addModeratorRow" select="/doc/query[@queryName='ParticipantEdits']/row[@createdts = $timestamp and @moderator='1']" />
        <xsl:variable name="addModeratorBadgeid" select="$addModeratorRow/@badgeid" />
        <xsl:variable name="deleteModeratorRow" select="/doc/query[@queryName='ParticipantEdits']/row[@inactivatedts = $timestamp and @moderator='1']" />
        <xsl:variable name="deleteModeratorBadgeid" select="$deleteModeratorRow/@badgeid" />
        <xsl:if test="count($addModeratorRow) > 0 or count($deleteModeratorRow) > 0">
            <div class="row-fluid">
                <span class="span5 offset1">
                    <xsl:choose>
                        <xsl:when test="count($addModeratorRow) > 0 and count($deleteModeratorRow) > 0">
                            Change moderator from <xsl:value-of select="/doc/query[@queryName='Persons']/row[@badgeid=$deleteModeratorBadgeid]/@name"/>
                            (<xsl:value-of select="$deleteModeratorBadgeid" />)
                            to <xsl:value-of select="/doc/query[@queryName='Persons']/row[@badgeid=$addModeratorBadgeid]/@name"/>
                            (<xsl:value-of select="$addModeratorBadgeid" />).
                        </xsl:when>
                        <xsl:when test="count($addModeratorRow) > 0">
                            Assign <xsl:value-of select="/doc/query[@queryName='Persons']/row[@badgeid=$addModeratorBadgeid]/@name"/>
                            (<xsl:value-of select="$addModeratorBadgeid" />) as moderator.
                        </xsl:when>
                        <xsl:otherwise>
                            Remove <xsl:value-of select="/doc/query[@queryName='Persons']/row[@badgeid=$deleteModeratorBadgeid]/@name"/>
                            (<xsl:value-of select="$deleteModeratorBadgeid" />) from moderator.
                        </xsl:otherwise>
                    </xsl:choose>
                </span>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template mode="additions" match="doc/query[@queryName='ParticipantEdits']/row">
        <xsl:variable name="timestamp" select="@createdts" />
        <xsl:variable name="badgeid" select="@badgeid" />
        <xsl:if test="count(/doc/query[@queryName='ParticipantEdits']/row[@inactivatedts = $timestamp and @badgeid = $badgeid]) = 0">
            <div class="row-fluid">
                <span class="span5 offset1">
                    Add <xsl:value-of select="/doc/query[@queryName='Persons']/row[@badgeid=$badgeid]/@name"/>
                    (<xsl:value-of select="$badgeid" />) to panel.
                </span>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template mode="deletions" match="doc/query[@queryName='ParticipantEdits']/row">
        <xsl:variable name="timestamp" select="@inactivatedts" />
        <xsl:variable name="badgeid" select="@badgeid" />
        <xsl:if test="count(/doc/query[@queryName='ParticipantEdits']/row[@createdts = $timestamp and @badgeid = $badgeid]) = 0">
            <div class="row-fluid">
                <span class="span5 offset1">
                    Remove <xsl:value-of select="/doc/query[@queryName='Persons']/row[@badgeid=$badgeid]/@name"/>
                    (<xsl:value-of select="$badgeid" />) from panel.
                </span>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='SessionEdits']/row">
        <div class="row-fluid">
            <span class="span6 offset6">
                <xsl:value-of select="@description" /> —
                <xsl:if test="@editdescription"><xsl:value-of select="@editdescription" /> — </xsl:if>
                status:<xsl:value-of select="@statusname" />
            </span>
        </div>
    </xsl:template>
</xsl:stylesheet>
EOD;
