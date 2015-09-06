<?xml version="1.0" encoding="UTF-8" ?>
<!--
	my_schedule
	Created by Peter Olszowka on 2013-12-09.
	Copyright (c) 2013 Peter Olszowka. All rights reserved.
-->
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" indent="yes" method="html" />
	<xsl:template match="/">
		<xsl:choose>
			<xsl:when test="doc/query[@queryName='sessions']/row">
				<table class="table table-condensed">
					<col style="width:4em;" />
					<col style="width:15%;" />
					<col style="width:8em;" />
					<col style="width:8em;" />
					<col style="width:8em;" />
					<col style="width:8em;" />
					<col style="width:65%;" />
					<xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
				</table>
			</xsl:when>
			<xsl:otherwise>
				<div class="alert alert-error">No schedule sessions found.</div>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	
	<xsl:template match="doc/query[@queryName='sessions']/row">
		<tr>
			<td>
				<span class="badge badge-info"><xsl:value-of select="@sessionid" /></span>
			</td>
			<td colspan="6"></td>
		</tr>
		<tr>
			<td colspan="2">
				<span class="label label-info"><xsl:value-of select="@title" /></span>
			</td>
			<td>
				<span class="label label-info" title="Room"><xsl:value-of select="@roomname" /></span>
			</td>
			<td>
				<span class="label label-info" title="Track"><xsl:value-of select="@trackname" /></span>
			</td>
			<td>
				<span class="label label-info"><xsl:value-of select="@starttime" /></span>
			</td>
			<td>
				<span class="label label-info"><xsl:value-of select="@duration" /></span>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="6">
				<span class="label">Description</span>
				<span><xsl:text> </xsl:text><xsl:value-of select="@progguiddesc" /></span>
			</td>
		</tr>
		<xsl:if test="@persppartinfo">
			<tr>
				<td></td>
				<td colspan="6">
					<span class="label">Prospective participant information</span>
					<span><xsl:text> </xsl:text><xsl:value-of select="@persppartinfo" /></span>
				</td>
			</tr>
		</xsl:if>
		<xsl:if test="@notesforpart">
			<tr>
				<td></td>
				<td colspan="6">
					<span class="label">Notes for participants</span>
					<span><xsl:text> </xsl:text><xsl:value-of select="@notesforpart" /></span>
				</td>
			</tr>
		</xsl:if>
		<tr>
			<td></td>
			<td>
				<span class="label">Panelists' Publication Names (Badge Names)</span>
			</td>
			<td colspan="2">
				<span class="label">Email addresses</span>
			</td>
			<td colspan="3">
				<span class="label">Comments</span>
			</td>
		</tr>
		<xsl:variable name="sessionid" select="@sessionid" />
		<xsl:apply-templates select="/doc/query[@queryName='participants']/row[@sessionid = $sessionid]" />
	</xsl:template>

	<xsl:template match="/doc/query[@queryName='participants']/row">
		<tr>
			<td></td>
			<td>
				<span><xsl:value-of select="@pubsname" /></span>
				<span><xsl:text> (</xsl:text><xsl:value-of select="@badgename" /><xsl:text>)</xsl:text></span>
				<xsl:if test="@moderator = '1'">
					<span style="font-style:italic;"><xsl:text> mod</xsl:text></span>
				</xsl:if>
			</td>
			<td colspan="2">
				<span><xsl:value-of select="@email" /></span>
			</td>
			<td colspan="3">
				<span><xsl:value-of select="@comments" /></span>
			</td>
		</tr>
	</xsl:template>
</xsl:stylesheet>
