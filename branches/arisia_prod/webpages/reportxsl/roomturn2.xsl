<?xml version="1.0" encoding="UTF-8" ?>
<!--
	allprivsreport
	Created by Peter Olszowka on 2011-07-24.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <table class="report">
        <tr>
            <th class="report" style="width:7em">Start Time</th>
            <th class="report" style="width:9em">Room</th>
            <th class="report">Session</th>
            <th class="report" style="min-width:20em">Title</th>
            <th class="report" style="width:7em">Room Set Name</th>
            <th class="report">Notes for Tech and Hotel</th>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='report']/row"/>
    </table>
</xsl:template>
<xsl:template match="/doc/query[@queryName='report']/row">
    <tr>
        <td class="report"><xsl:value-of select="@starttime"/></td>
        <td class="report"><a href="MaintainRoomSched.php?selroom={@roomid}"><xsl:value-of select="@roomname"/></a></td>
        <td class="report"><a href="StaffAssignParticipants.php?selsess={@sessionid}"><xsl:value-of select="@sessionid"/></a></td>
        <td class="report"><a href="EditSession.php?id={@sessionid}"><xsl:value-of select="@title"/></a></td>
        <td class="report"><xsl:value-of select="@roomsetname"/></td>
        <td class="report"><xsl:value-of select="@servicenotes"/></td>
    </tr>
</xsl:template>
</xsl:stylesheet>
