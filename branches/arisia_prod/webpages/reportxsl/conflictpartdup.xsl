<?xml version="1.0" encoding="UTF-8" ?>
<!--
	conflictpartdupreport
	Created by Peter Olszowka on 2011-12-26.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <table class="report">
        <tr>
            <th class="report" style="">Participant</th>
            <th class="report" style="width:20%">Title A</th>
            <th class="report" style="">Track A</th>
            <th class="report" style="">Room ID A</th>
            <th class="report" style="">Session ID A</th>
            <th class="report" style="width:8%">Start Time A</th>
            <th class="report" style="">Duration A</th>
            <th class="report" style="width:20%">Title B</th>
            <th class="report" style="">Track B</th>
            <th class="report" style="">Room ID B</th>
            <th class="report" style="">Session ID B</th>
            <th class="report" style="width:8%">Start Time B</th>
            <th class="report" style="">Duration B</th>
        </tr>
        <xsl:apply-templates select="doc/query[@queryName='conflict']/row" /> 
    </table>
</xsl:template>

<xsl:template match="/doc/query[@queryName='conflict']/row" >
    <tr>
        <td class="report">
            <xsl:choose>
                <xsl:when test="@pubsname">
                    <xsl:value-of select="@pubsname" />
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="@firstname" /> <xsl:value-of select="@lastname" />
                </xsl:otherwise>
            </xsl:choose>
        </td>
        <td class="report">
            <a href="EditSession.php?id={@sessionida}"><xsl:value-of select="@titlea" /></a>
        </td>
        <td class="report">
            <xsl:value-of select="@tracknamea" />
        </td>
        <td class="report">
            <a href="MaintainRoomSched.php?selroom={@roomida}"><xsl:value-of select="@roomnamea" /></a>
        </td>
        <td class="report">
            <a href="StaffAssignParticipants.php?selsess={@sessionida}"><xsl:value-of select="@sessionida" /></a>
        </td>
        <td class="report" style="white-space:nowrap"><xsl:value-of select="@starttimea" /></td>
        <td class="report">
            <xsl:value-of select="@durationa" />
        </td>
        <td class="report">
            <a href="EditSession.php?id={@sessionidb}"><xsl:value-of select="@titleb" /></a>
        </td>
        <td class="report">
            <xsl:value-of select="@tracknameb" />
        </td>
        <td class="report">
            <a href="MaintainRoomSched.php?selroom={@roomidb}"><xsl:value-of select="@roomnameb" /></a>
        </td>
        <td class="report">
            <a href="StaffAssignParticipants.php?selsess={@sessionidb}"><xsl:value-of select="@sessionidb" /></a>
        </td>
        <td class="report" style="white-space:nowrap"><xsl:value-of select="@starttimeb" /></td>
        <td class="report">
            <xsl:value-of select="@durationb" />
        </td>
    </tr>
</xsl:template>

</xsl:stylesheet>
