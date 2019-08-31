<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Unabridged Program Panel Grid';
$report['description'] = 'Display complete schedule of program division sessions with rooms on horizontal axis and time on vertical. This includes any item marked "Do Not Print". ';
$report['categories'] = array(
    'Programming Reports' => 215,
    'Grid Reports' => 215,
);
$report['queries'] = [];
$report['queries']['rooms'] =<<<'EOD'
SELECT
        R.roomname,
        R.roomid
    FROM
        Rooms R
    WHERE
	R.roomid IN
            (SELECT DISTINCT SCH.roomid
                FROM
		            `Schedule` SCH
		       JOIN Sessions S USING (sessionid)
                    WHERE
                        S.divisionid = 2 # Programming
                )
    ORDER BY
        R.display_order;
EOD;
$report['queries']['times'] =<<<'EOD'
SELECT DISTINCT DATE_FORMAT(ADDTIME("$ConStartDatim$",SCH.starttime),"%a %l:%i %p") AS starttimeFMT, SCH.starttime
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
    WHERE
        SCH.roomid IN
            (SELECT DISTINCT SCH.roomid
                FROM
    	            Schedule SCH
    	       JOIN Sessions S USING (sessionid)
                    WHERE
                        S.divisionid = 2 # Programming
                )
    ORDER BY
        SCH.starttime;
EOD;
$report['queries']['sessions'] =<<<'EOD'
SELECT SCH.starttime, SCH.sessionid, SCH.roomid, DATE_FORMAT(S.duration,"%H:%i") AS duration, S.title 
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
    WHERE
            SCH.roomid IN
                (SELECT DISTINCT SCH.roomid
                    FROM
    	                    Schedule SCH
                       JOIN Sessions S USING (sessionid)
                    WHERE
                        S.divisionid = 2 # Programming
                 )
        AND S.divisionid = 2 # Programming
    ORDER BY
        SCH.starttime;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='rooms']/row and doc/query[@queryName='times']/row and doc/query[@queryName='sessions']/row">
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

    <xsl:template match="doc/query[@queryName='rooms']/row">
        <th class="report">
            <xsl:call-template name="showRoomName">
                <xsl:with-param name="roomid" select = "@roomid" />
                <xsl:with-param name="roomname" select = "@roomname" />
            </xsl:call-template>
        </th>
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
                        <xsl:when test="/doc/query[@queryName='sessions']/row[@roomid=$roomid and @starttime=$starttime]">
                            <xsl:for-each select="/doc/query[@queryName='sessions']/row[@roomid=$roomid and @starttime=$starttime]">
                                <div>
                                    <xsl:text>(</xsl:text>
                                    <xsl:call-template name="showSessionid">
                                        <xsl:with-param name="sessionid" select = "@sessionid" />
                                    </xsl:call-template>
                                    <xsl:text>) </xsl:text>
                                    <xsl:call-template name="showSessionTitle">
                                        <xsl:with-param name="sessionid" select = "@sessionid" />
                                        <xsl:with-param name="title" select = "@title" />
                                    </xsl:call-template> 
                                    <xsl:text> (</xsl:text>
                                    <xsl:value-of select="@duration" />
                                    <xsl:text>) </xsl:text>
                                </div>
                           </xsl:for-each>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </td>
            </xsl:for-each>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
