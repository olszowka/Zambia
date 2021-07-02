<?php
$report = [];
$report['name'] = 'Session Hashtag Maintenance Report';
$report['description'] = 'List all scheduled sessions that need some hashtag editing, either because the current hashtag is empty or because it\'s too long.';
$report['categories'] = array(
    'Publication Reports' => 880,
);
$report['multi'] = 'true';
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, S.progguiddesc, R.roomname, SCH.roomid, PS.pubstatusname,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        DATE_FORMAT(S.duration,'%i') AS durationmin, DATE_FORMAT(S.duration,'%k') AS durationhrs,
		T.trackname, KC.kidscatname
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
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table class="table table-sm table-bordered">
                    <tr class="table-primary">
                        <th class="text-nowrap">Session ID</th>
                        <th>Title</th>
                        <th>Track</th>
                        <th>Room</th>
                        <th>StartTime</th>
                        <th>Duration</th>
                        <th>
                            <div>Publication</div>
                            <div>Status</div>
                        </th>
                        <th>Suitability for Children</th>
                        <th>Description</th>
                        <th>Participants</th>
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
            <td style="white-space:nowrap;"><xsl:value-of select="@starttime" /></td>
            <td style="white-space:nowrap;">
                <xsl:call-template name="showDuration">
                    <xsl:with-param name="durationhrs" select = "@durationhrs" />
                    <xsl:with-param name="durationmin" select = "@durationmin" />
                </xsl:call-template>
            </td>
            <td><xsl:value-of select="@pubstatusname" /></td>
            <td><xsl:value-of select="@kidscatname" /></td>
            <td><xsl:value-of select="@progguiddesc" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
