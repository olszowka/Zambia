<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2016-05-05;
	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" indent="yes" method="html" />
	<xsl:template match="/">
		<div class="alert alert-error alert-block">
			<p>
				Error: Your updates to participant assignments could not be made because the session has been updated since
				you last loaded the page.  Following is a list of your attempted edits:
			</p>
			<ul>
				<xsl:apply-templates select="doc/moderator" />
				<xsl:apply-templates select="doc/participantchanges/addparticipant" />
				<xsl:apply-templates select="doc/participantchanges/removeparticipant" />
			</ul>
			<p>
				The page below reflects the most recent updates.
			</p>
		</div>
	</xsl:template>
	
	<xsl:template match="doc/moderator">
		<xsl:choose>
			<xsl:when test="@changecode='AM'">
				<xsl:variable name="addedModeratorBadgeid" select="@tomoderator" />
				<xsl:variable name="addedModerator" select="/doc/query[@queryName='participants']/row[@badgeid=$addedModeratorBadgeid]/@pubsname" />
				<li>Set <xsl:value-of select="$addedModerator"/> (<xsl:value-of select="$addedModeratorBadgeid"/>) as moderator.</li>
			</xsl:when>
			<xsl:when test="@changecode='RM'">
				<xsl:variable name="removedModeratorBadgeid" select="@frommoderator" />
				<xsl:variable name="removedModerator" select="/doc/query[@queryName='participants']/row[@badgeid=$removedModeratorBadgeid]/@pubsname" />
				<li>Remove <xsl:value-of select="$removedModerator"/> (<xsl:value-of select="$removedModeratorBadgeid"/>) as moderator.</li>
			</xsl:when>
			<xsl:when test="@changecode='CM'">
				<xsl:variable name="addedModeratorBadgeid" select="@tomoderator" />
				<xsl:variable name="addedModerator" select="/doc/query[@queryName='participants']/row[@badgeid=$addedModeratorBadgeid]/@pubsname" />
				<xsl:variable name="removedModeratorBadgeid" select="@frommoderator" />
				<xsl:variable name="removedModerator" select="/doc/query[@queryName='participants']/row[@badgeid=$removedModeratorBadgeid]/@pubsname" />
				<li>Change moderator from <xsl:value-of select="$removedModerator"/> (<xsl:value-of select="$removedModeratorBadgeid"/>) to <xsl:value-of select="$addedModerator"/> (<xsl:value-of select="$addedModeratorBadgeid"/>).</li>
			</xsl:when>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="doc/participantchanges/addparticipant">
		<xsl:variable name="badgeid" select="@badgeid" />
		<xsl:variable name="addedParticipant" select="/doc/query[@queryName='participants']/row[@badgeid=$badgeid]/@pubsname" />
		<li>Add <xsl:value-of select="$addedParticipant"/> (<xsl:value-of select="$badgeid"/>) to session.</li>
	</xsl:template>
		
	<xsl:template match="doc/participantchanges/removeparticipant">
		<xsl:variable name="badgeid" select="@badgeid" />
		<xsl:variable name="removedParticipant" select="/doc/query[@queryName='participants']/row[@badgeid=$badgeid]/@pubsname" />
		<li>Remove <xsl:value-of select="$removedParticipant"/> (<xsl:value-of select="$badgeid"/>) from session.</li>
	</xsl:template>
		
</xsl:stylesheet>
