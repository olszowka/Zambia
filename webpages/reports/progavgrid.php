<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Program AV Grid';
$report['description'] = 'Show Service Requests on a grid. ';
$report['categories'] = array(
    'Programming Reports' => 240,
    'Tech Reports' => 240,
    'Grid Reports' => 240,
);
$report['queries'] = [];
$report['queries']['rooms'] =<<<'EOD'
SELECT
		R.roomname,
		R.roomid
	FROM
		Rooms R
	WHERE
		R.roomid in
			(SELECT DISTINCT SCH.roomid
				FROM
						Schedule SCH
				   JOIN Sessions S USING (sessionid)
				   JOIN SessionHasService SHS USING (sessionid)
				WHERE
					SHS.serviceid in (2,7,8,11)
                                   AND SCH.roomid NOT IN (2,3,7,8,11,13)
			)
	ORDER BY
        R.display_order;
EOD;
$report['queries']['times'] =<<<'EOD'
SELECT DISTINCT DATE_FORMAT(ADDTIME("$ConStartDatim$",SCH.starttime),"%a %l:%i %p") as starttimeFMT, SCH.starttime
	FROM
			Schedule SCH
	   JOIN Sessions S USING (sessionid)
	   JOIN SessionHasService SHS USING (sessionid)
	WHERE
		SHS.serviceid in (2,7,8,11)
            AND SCH.roomid NOT IN (2,3,7,8,11,13)
	ORDER BY
		SCH.starttime
EOD;
$report['queries']['sessions'] =<<<'EOD'
SELECT SCH.starttime, SCH.sessionid, SCH.roomid, DATE_FORMAT(S.duration,"%H:%i") as duration, S.title, TR.trackname, TY.typename 
	FROM
			Schedule SCH
	   JOIN Sessions S USING (sessionid)
	   JOIN SessionHasService SHS USING (sessionid)
	   JOIN Tracks TR USING (trackid)
	   JOIN Types TY USING (typeid)
	WHERE
                SHS.serviceid in (2,7,8,11)
            AND SCH.roomid NOT IN (2,3,7,8,11,13)
	ORDER BY
		SCH.starttime
EOD;
$report['queries']['services'] =<<<'EOD'
SELECT SCH.sessionid, SVCS.servicename 
	FROM
			Schedule SCH
	   JOIN SessionHasService SHS USING (sessionid)
	   JOIN Services SVCS USING (serviceid)
	WHERE
                SHS.serviceid in (2,7,8,11)
            AND SCH.roomid NOT IN (2,3,7,8,11,13)
	ORDER BY
		SCH.sessionid
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='rooms']/row and doc/query[@queryName='times']/row and doc/query[@queryName='sessions']">
                <table class="report">
                    <tr>
                        <th class="report" style="">Time</th>
                        <xsl:apply-templates select="doc/query[@queryName='rooms']/row" />
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='times']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
            
    <xsl:template match="doc/query[@queryName='times']/row">
        <xsl:variable name="starttime" select="@starttime" />
        <tr>
            <td class="report"><xsl:value-of select="@starttimeFMT" /></td>
            <xsl:for-each select="/doc/query[@queryName='rooms']/row">
                <xsl:variable name="roomid" select="@roomid" />
                <xsl:variable name="sessionid" select="/doc/query[@queryName='sessions']/row[@roomid=$roomid and @starttime=$starttime]/@sessionid" />
                <td class="report">
                    <xsl:choose>
                        <xsl:when test="$sessionid">
                            <div>
                                <xsl:call-template name="showSessionidWithTitle">
                                    <xsl:with-param name="sessionid" select = "$sessionid" />
                                    <xsl:with-param name="title" select = "/doc/query[@queryName='sessions']/row[@sessionid=$sessionid]/@title" />
                                </xsl:call-template>
                                <xsl:text> </xsl:text>
                                <span style="color:green"><xsl:value-of select="/doc/query[@queryName='sessions']/row[@sessionid=$sessionid]/@duration" /></span>
                            </div>
                            <xsl:apply-templates select="/doc/query[@queryName='services']/row[@sessionid=$sessionid]" />
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </td>
            </xsl:for-each>
        </tr>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='rooms']/row">
        <th class="report">
            <xsl:call-template name="showRoomName">
                <xsl:with-param name="roomid" select = "@roomid" />
                <xsl:with-param name="roomname" select = "@roomname" />
            </xsl:call-template>
        </th>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='services']/row">
        <div>
            <xsl:value-of select="@servicename" />
        </div>
    </xsl:template>
</xsl:stylesheet>
EOD;
