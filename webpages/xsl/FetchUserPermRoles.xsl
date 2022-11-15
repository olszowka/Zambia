<?xml version="1.0" encoding="UTF-8" ?>
<!--
        Created by Peter Olszowka on 2021-01-04;
        Copyright (c) 2021 Peter Olszowka. All rights reserved.
        See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" indent="yes" method="html" />
	<xsl:template match="/">
		<div class="col-sm-auto me-1">
			<div class="pb-1">
				User Permission Roles:
			</div>
			<div>
				<div class="tag-chk-container">
					<xsl:for-each select="/doc/query[@queryName='permroles']/row">
						<xsl:call-template name="permbox">
							<xsl:with-param name="mayedit" select="@mayedit"/>
							<xsl:with-param name="permroleid" select="@permroleid"/>
							<xsl:with-param name="badgeid" select="@badgeid"/>
							<xsl:with-param name="permrolename" select="@permrolename"/>
						</xsl:call-template>
					</xsl:for-each>
				</div>
			</div>
		</div>
		<xsl:if test="count(/doc/query/row[@title!='']) > 0">
			<div class="col-sm-9">
				<div class="row pb-1">
					<div class="col-sm-auto">
						Sessions Assigned:
					</div>
				</div>
				<div class="row">
					<div class="col-sm-2">
						<strong>Assignment</strong>
					</div>
					<div class="col-sm-6">
						<strong>Session</strong>
					</div>
					<div class="col-sm-4">
						<strong>Schedule Room</strong>
					</div>
				</div>
				<xsl:for-each select="/doc/query[@queryName='sessionlist']/row">
					<div class="row">
						<xsl:call-template name="session">
							<xsl:with-param name="badgeid" select="@badgeid"/>
							<xsl:with-param name="sessionid" select="@sessionid"/>
							<xsl:with-param name="moderator" select="@moderator"/>
							<xsl:with-param name="roomname" select="@roomname"/>
							<xsl:with-param name="roomid" select="@roomid"/>
							<xsl:with-param name="starttime" select="@starttime"/>
							<xsl:with-param name="title" select="@title"/>
						</xsl:call-template>
					</div>
				</xsl:for-each>
			</div>
		</xsl:if>
	</xsl:template>
	<xsl:template name="permbox">
		<div>
			<xsl:choose>
				<xsl:when test="@mayedit != '1'">
					<xsl:attribute name="class">tag-chk-label-wrapper disabled</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:attribute name="class">tag-chk-label-wrapper</xsl:attribute>
				</xsl:otherwise>
			</xsl:choose>
			<label class="tag-chk-label">
				<input type="checkbox" name="permroles[]" id="role_{@permroleid}" class="tag-chk mycontrol" value="{@permroleid}" >
					<xsl:if test="@badgeid">
						<xsl:attribute name="checked" >checked</xsl:attribute>
					</xsl:if>
					<xsl:if test="@mayedit != '1'">
						<xsl:attribute name="disabled">disabled</xsl:attribute>
					</xsl:if>
				</input>
				<xsl:value-of select="@permrolename" />
			</label>
		</div>
	</xsl:template>
	<xsl:template name="session">
		<div class="col-sm-2">
			<a>
				<xsl:attribute name="href">
					<xsl:text>/StaffAssignParticipants.php?selsess=</xsl:text>
					<xsl:value-of select="@sessionid"/>
				</xsl:attribute>
				<xsl:attribute name="target">_new</xsl:attribute>
				<xsl:value-of select="@sessionid"/>
			</a>
			<xsl:choose>
				<xsl:when test="@moderator = '1'">
					[Mod]
				</xsl:when>
			</xsl:choose>
		</div>
		<div class="col-sm-6">
			<a>
				<xsl:attribute name="href">
					<xsl:text>/EditSession.php?id=</xsl:text>
					<xsl:value-of select="@sessionid"/>
				</xsl:attribute>
				<xsl:attribute name="target">_new</xsl:attribute>
				<xsl:value-of select="@title"/>
			</a>			
		</div>
		<div class="col-sm-4">
			<a>
				<xsl:attribute name="href">
					<xsl:text>/MaintainRoomSched.php?selroom=</xsl:text>
					<xsl:value-of select="@roomid"/>
				</xsl:attribute>
				<xsl:attribute name="target">_new</xsl:attribute>
				<xsl:value-of select="@roomname"/>			
			</a>
			<xsl:text> </xsl:text>
			<xsl:value-of select="@starttime"/>		
		</div>
	</xsl:template>
</xsl:stylesheet>
