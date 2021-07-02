<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Full Schedule Report';
$report['multi'] = 'true';
$report['output_filename'] = 'fullScheduleReport.csv';
$report['description'] = 'List all sessions in all rooms.  Include full description and list of participants.';
$report['categories'] = array(
    'Publication Reports' => 880,
);
$report['queries'] = [];
$report['additionalOptions'] = array( "order" => array(array( 4,  "asc"  )));
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, S.progguiddesc, R.roomname, SCH.roomid, PS.pubstatusname,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%Y-%m-%d %H:%i') AS starttimefull,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
		T.trackname, KC.kidscatname, S.hashtag
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN PubStatuses PS USING (pubstatusid)
        JOIN Tracks T USING (trackid)
		JOIN KidsCategories KC USING (kidscatid)
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        SCH.sessionid, P.pubsname, P.badgeid, POS.moderator
    FROM
			 Schedule SCH
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump C USING (badgeid)
    ORDER BY
		SCH.sessionid, POS.moderator DESC, 
        IF(instr(P.pubsname,C.lastname)>0,C.lastname,substring_index(P.pubsname,' ',-1)),
        C.firstname;
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
                            <th>Session ID</th>
                            <th>Title</th>
                            <th>Track</th>
                            <th>Room</th>
                            <th>StartTime</th>
                            <th>Duration</th>
                            <th>
                                Publication
                                Status
                            </th>
                            <th>Suitability for Children</th>
                            <th>Hashtag</th>
                            <th>Description</th>
                            <th>Participants</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="doc/query[@queryName='sessions']/row"/>
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
	<xsl:variable name="sessionid" select="@sessionid" />
        <tr>
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
            <td><xsl:value-of select="@trackname" /></td>
            <td>
				<xsl:call-template name="showRoomName">
					<xsl:with-param name="roomid" select="@roomid" />
					<xsl:with-param name="roomname" select="@roomname" />
				</xsl:call-template>
			</td>
            <td class="text-nowrap">
                <time>
                    <xsl:attribute name="datetime">
                        <xsl:value-of select="@starttimefull" />
                    </xsl:attribute>
                    <xsl:value-of select="@starttime" />
                </time>
            </td>
            <td class="text-nowrap">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@pubstatusname" /></td>
            <td><xsl:value-of select="@kidscatname" /></td>
            <td><small><xsl:value-of select="@hashtag" /></small></td>
            <td><xsl:value-of select="@progguiddesc" /></td>
            <td>
                <xsl:choose>
                    <xsl:when test="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
                        <xsl:for-each select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid]">
                            <xsl:if test="position() != 1">
                                <xsl:text>, </xsl:text>
                            </xsl:if>
                            <xsl:call-template name="showPubsnameWithBadgeid">
                                <xsl:with-param name="badgeid" select = "@badgeid" />
                                <xsl:with-param name="pubsname" select = "@pubsname" />
                            </xsl:call-template>
                            <xsl:if test="@moderator='1'"> (MOD)</xsl:if>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                        NULL
                    </xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
