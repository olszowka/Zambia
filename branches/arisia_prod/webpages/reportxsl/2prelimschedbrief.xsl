<?xml version="1.0" encoding="UTF-8" ?>
<!--
	2prelimschedbriefreport
	Created by Peter Olszowka on 2011-01-02.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <table class="report">
        <tr>
            <th class="report">Start Time</th>
            <th class="report">Track Name</th>
            <th class="report" title="Click on Session ID to edit session participants.">Session Id</th>
            <th class="report" title="Click on Title to edit session details.">Title</th>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='schedule']/row" />
    </table>
</xsl:template>
<xsl:template match="/doc/query[@queryName='schedule']/row">
    <tr>
        <td class="report"><xsl:value-of select="@starttime" /></td>
        <td class="report"><xsl:value-of select="@trackname" /></td>
        <td class="report"><a href="StaffAssignParticipants.php?selsess={@sessionid}" title="Edit session participants"><xsl:value-of select="@sessionid" /></a></td>
        <td class="report"><a href="EditSession.php?id={@sessionid}" title="Edit session details"><xsl:value-of select="@title" /></a></td>
    </tr>
</xsl:template>
</xsl:stylesheet>
