<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template name="showBadgeid">
        <xsl:param name="badgeid" />
        <a href="AdminParticipants.php?badgeid={$badgeid}" title="Administer participants"><xsl:value-of select="$badgeid" /></a>
    </xsl:template>

    <xsl:template name="showSessionid">
        <xsl:param name="sessionid" />
        <a href="StaffAssignParticipants.php?selsess={$sessionid}" title="Edit session participants"><xsl:value-of select="$sessionid" /></a>
    </xsl:template>

    <xsl:template name="showSessionTitle">
        <xsl:param name="sessionid" />
        <xsl:param name="title" />
        <a href="EditSession.php?id={$sessionid}" title="Edit session"><xsl:value-of select="$title" /></a>
    </xsl:template>

    <xsl:template name="showRoomName">
        <xsl:param name="roomid" />
        <xsl:param name="roomname" />
        <a href="MaintainRoomSched.php?selroom={$roomid}" title="Maintain room schedule"><xsl:value-of select="$roomname" /></a>
    </xsl:template>
</xsl:stylesheet>
