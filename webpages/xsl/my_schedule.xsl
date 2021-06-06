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
				<table class="table table-sm">
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
			<td colspan="7"></td>
		</tr>
		<tr>
			<td colspan="2">
				<span class="badge badge-primary"><xsl:value-of select="@title" /></span>
			</td>
			<td>
				<span class="badge badge-primary" title="Room"><xsl:value-of select="@roomname" /></span>
			</td>
			<td>
				<span class="badge badge-primary" title="Track"><xsl:value-of select="@trackname" /></span>
			</td>
			<td>
				<span class="badge badge-primary" title="Type"><xsl:value-of select="@typename" /></span>
			</td>
			<td>
				<span class="badge badge-primary"><xsl:value-of select="@starttime" /></span>
			</td>
			<td>
				<span class="badge badge-primary">Duration: <xsl:value-of select="@duration" /></span>
			</td>
			<td></td>
		</tr>
		<tr>
			<td></td>
			<td colspan="7">
				<span class="badge badge-secondary">Description</span>
				<span><xsl:text> </xsl:text><xsl:value-of select="@progguiddesc" /></span>
			</td>
		</tr>
		<xsl:if test="@persppartinfo">
			<tr>
				<td></td>
				<td colspan="7">
					<span class="badge badge-secondary">Prospective participant information</span>
					<span><xsl:text> </xsl:text><xsl:value-of select="@persppartinfo" /></span>
				</td>
			</tr>
		</xsl:if>
		<xsl:if test="@notesforpart">
			<tr>
				<td></td>
				<td colspan="7">
					<span class="badge badge-secondary">Notes for participants</span>
					<span><xsl:text> </xsl:text><xsl:value-of select="@notesforpart" /></span>
				</td>
			</tr>
		</xsl:if>
		<tr>
			<td></td>
			<td>
				<span class="badge badge-secondary">Panelists' Publication Names (Badge Names)</span>
			</td>
			<td colspan="2">
				<span class="badge badge-secondary">Email addresses</span>
			</td>
			<td colspan="4">
				<span class="badge badge-secondary">Comments</span>
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
			<td colspan="4">
				<span><xsl:value-of select="@comments" /></span>
			</td>
		</tr>
	</xsl:template>
</xsl:stylesheet>
