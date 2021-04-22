<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Arisia TV by time ';
$report['description'] = 'Just things in TV room';
$report['categories'] = array(
//    'Arisia TV Reports' => 250,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') as starttime,
        DATE_FORMAT(S.duration,'%k:%i') as duration, TY.typename, S.sessionid, S.title, S.progguiddesc
    FROM
             Schedule SCH
        JOIN Sessions S USING (sessionid)
        JOIN Types TY USING (typeid)
        JOIN Rooms R USING (roomid)
    WHERE
        R.roomname = 'ArisiaTV'
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
            <xsl:when test="doc/query[@queryName='schedule']/row">
                <table class="report">
					<col style="width:8em;" />
					<col style="width:5em;" />
					<col style="width:6em;" />
					<col style="width:8em;" />
					<col style="width:20em;" />
					<col />
                    <tr>
                        <th class="report" style="">Start Time</th>
                        <th class="report" style="">Duration</th>
                        <th class="report" style="">Session ID</th>
                        <th class="report" style="">Type</th>
                        <th class="report" style="">Title</th>
                        <th class="report" style="">Description</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='schedule']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='schedule']/row">
        <tr>
            <td class="report"><xsl:value-of select="@starttime" /></td>
            <td class="report"><xsl:value-of select="@duration" /></td>
            <td class="report">
                <xsl:call-template name="showSessionid"><xsl:with-param name="sessionid" select = "@sessionid" /></xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@typename" /></td>
            <td class="report">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@progguiddesc" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
