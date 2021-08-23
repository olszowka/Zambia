<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Room Configuration';
$report['description'] = 'List all configurable information associated with rooms.';
$report['categories'] = array(
    'Zambia Administration Reports' => 890,
    'Hotel Reports' => 890,
);
$report['queries'] = [];
$report['queries']['rooms'] =<<<'EOD'
SELECT
           R.roomid, R.roomname, R.height, R.dimensions, R.area, R.function, R.floor, R.notes,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.opentime1),'%a %l:%i %p') opentime1,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.closetime1),'%a %l:%i %p') closetime1,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.opentime2),'%a %l:%i %p') opentime2,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.closetime2),'%a %l:%i %p') closetime2,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.opentime3),'%a %l:%i %p') opentime3,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.closetime3),'%a %l:%i %p') closetime3,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.opentime4),'%a %l:%i %p') opentime4,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',R.closetime4),'%a %l:%i %p') closetime4,
        IF(R.is_scheduled,'Yes','No') AS is_scheduled,
        IF(EXISTS (SELECT * FROM Schedule SCH WHERE SCH.roomid = R.roomid),'Yes','No') AS scheduled
    FROM
           Rooms R
    ORDER BY
        R.display_order;
EOD;
$report['queries']['roomsets'] =<<<'EOD'
SELECT R.roomid, RS.roomsetname, RHS.capacity
    FROM
             Rooms R
        JOIN RoomHasSet RHS using (roomid)
        JOIN RoomSets RS using (roomsetid)
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='rooms']/row">
                <table class="report">
                    <tr>
                        <th rowspan = "2" class="report">Room Id</th>
                        <th class="report">Room Name</th>
                        <th class="report">Height</th>
                        <th class="report">Dimensions</th>
                        <th class="report">Area</th>
                        <th class="report">Function</th>
                        <th class="report">Floor</th>
                        <th class="report">Notes</th>
                        <th class="report" title="rooms.is_scheduled">To be scheduled*</th>
                        <th class="report">Has been scheduled</th>
                    </tr>
                    <tr>
                        <th class="report" colspan = "4">Times</th>
                        <th class="report" colspan = "5">Room Sets</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='rooms']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='rooms']/row">
        <xsl:variable name="roomid" select="@roomid" />
        <tr>
            <td rowspan="2" class="report"><xsl:value-of select="@roomid"/></td>
            <td class="report"><xsl:value-of select="@roomname"/></td>
            <td class="report"><xsl:value-of select="@height"/></td>
            <td class="report"><xsl:value-of select="@dimensions"/></td>
            <td class="report"><xsl:value-of select="@area"/></td>
            <td class="report"><xsl:value-of select="@function"/></td>
            <td class="report"><xsl:value-of select="@floor"/></td>
            <td class="report"><xsl:value-of select="@notes"/></td>
            <td class="report"><xsl:value-of select="@is_scheduled"/></td>
            <td class="report"><xsl:value-of select="@scheduled"/></td>
        </tr>
        <tr>
            <td colspan="4" class="report">
                <xsl:choose>
                    <xsl:when test="@opentime1 or @closetime1">
                        <xsl:value-of select="@opentime1"/><xsl:text> - </xsl:text><xsl:value-of select="@closetime1"/><br />
                    </xsl:when>
                    <xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
                </xsl:choose>
                <xsl:choose>
                    <xsl:when test="@opentime2 or @closetime2">
                        <xsl:value-of select="@opentime2"/><xsl:text> - </xsl:text><xsl:value-of select="@closetime2"/><br />
                    </xsl:when>
                    <xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
                </xsl:choose>
                <xsl:choose>
                    <xsl:when test="@opentime3 or @closetime3">
                        <xsl:value-of select="@opentime3"/><xsl:text> - </xsl:text><xsl:value-of select="@closetime3"/><br />
                    </xsl:when>
                    <xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
                </xsl:choose>
                <xsl:choose>
                    <xsl:when test="@opentime4 or @closetime4">
                        <xsl:value-of select="@opentime4"/><xsl:text> - </xsl:text><xsl:value-of select="@closetime4"/><br />
                    </xsl:when>
                    <xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
                </xsl:choose>
            </td>
            <td colspan="5" class="report">
                <xsl:choose>
                    <xsl:when test="/doc/query[@queryName='roomsets']/row[@roomid=$roomid]">
                        <xsl:apply-templates select="/doc/query[@queryName='roomsets']/row[@roomid=$roomid]" />
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='roomsets']/row">
        <div>
            <xsl:value-of select="@roomsetname"/><xsl:text> : </xsl:text><xsl:value-of select="@capacity"/>
        </div>
    </xsl:template>
</xsl:stylesheet>
EOD;
