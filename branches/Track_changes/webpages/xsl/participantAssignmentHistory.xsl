<?xml version="1.0" encoding="UTF-8" ?>
<!--
    $Header: https://svn.code.sf.net/p/zambia/code/branches/Track_changes/webpages/xsl/StaffAssignParticipants.xsl 1151 2015-11-23 13:31:52Z polszowka $
	Created by Peter Olszowka on 2016-05-11;
	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" indent="yes" method="html" />
	<xsl:variable name="selsessionid" select="/doc/parameters/@selsessionid" />
	<xsl:template match="/">
		<form id="selsesformtop" name="selsesform" class="form-inline" method="get" action="participantAssignmentHistory.php">
			<div>
				<label for="selsess">Select Session:</label>
				<xsl:text> </xsl:text>
				<select id="sessionDropdown" class="span6" name="selsess">
					<option value="0">
						<xsl:if test="$selsessionid = '0'">
							<xsl:attribute name="selected">selected</xsl:attribute>
						</xsl:if>
						<xsl:text>Select Session</xsl:text>
					</option>
					<xsl:apply-templates select="doc/query[@queryName='chooseSession']/row" >
						<xsl:sort select="@trackname" />
						<xsl:sort select="@sessionid" data-type="number" />
					</xsl:apply-templates>
				</select>
				<xsl:text> </xsl:text>
				<button id="sessionBtn" type="submit" name="submit" class="btn btn-primary">
					<xsl:if test="$selsessionid = '0'">
						<xsl:attribute name="disabled">disabled</xsl:attribute>
					</xsl:if>
					<xsl:text>Select Session</xsl:text>
				</button>
			</div>
		</form>
		<hr />
		<xsl:if test="$selsessionid != '0'">
			<h2>
				<xsl:value-of select="doc/query[@queryName='title']/row/@title" />
			</h2>
			<h4>Current Participants</h4>
			<xsl:choose>
				<xsl:when test="count(doc/query[@queryName='currentAssignments']/row) > 0">
					<xsl:apply-templates select="doc/query[@queryName='currentAssignments']/row" />
					<h4>Edits</h4>
					<xsl:apply-templates select="doc/query[@queryName='timestamps']/row" />
				</xsl:when>
				<xsl:otherwise>
					<p>No participants are currently assigned.</p>
				</xsl:otherwise>
			</xsl:choose>
			
		</xsl:if>
		
	</xsl:template>
	
	<xsl:template match="doc/query[@queryName='chooseSession']/row">
		<option value="{@sessionid}">
			<xsl:if test="@sessionid = $selsessionid"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
			<xsl:value-of select="@trackname"/> - <xsl:value-of select="@sessionid" /> - <xsl:value-of select="@title" />
		</option>
	</xsl:template>
	
	<xsl:template match="doc/query[@queryName='currentAssignments']/row">
		<div class="row-fluid">
			<span class="span11 offset1">
				<xsl:value-of select="@pubsname"/> (<xsl:value-of select="@badgeid" />) <xsl:if test="@moderator='1'"><span class="zam-partAsgnHist-moderator">moderator</span></xsl:if>
			</span>
		</div>
	</xsl:template>
	
	<xsl:template match="doc/query[@queryName='timestamps']/row">
		<xsl:variable name="timestamp" select="@timestamp" />
		<xsl:variable name="createRow" select="/doc/query[@queryName='edits']/row[@createdts = $timestamp][1]" />
		<xsl:variable name="inactivateRow" select="/doc/query[@queryName='edits']/row[@inactivatedts = $timestamp][1]" />
		<xsl:if test="count($createRow) > 0 or count($inactivateRow) > 0">
			<div class="row-fluid">
				<span class="span11 offset1">
					<span class="label">
						<xsl:choose>
							<xsl:when test="count($createRow) > 0">
								<xsl:value-of select="$createRow/@crPubsname"/> (<xsl:value-of select="$createRow/@createdbybadgeid" />) <xsl:value-of select="$createRow/@createdtsFormat" />
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="$inactivateRow/@inactPubsname"/> (<xsl:value-of select="$inactivateRow/@inactivatedbybadgeid" />) <xsl:value-of select="$inactivateRow/@inactivatedtsFormat" />
							</xsl:otherwise>
						</xsl:choose>
					</span>	
				</span>
			</div>
			<xsl:call-template name="processModeratorEdit">
				<xsl:with-param name="timestamp" select = "$timestamp" />
			</xsl:call-template>
			<xsl:apply-templates mode="additions" select="/doc/query[@queryName='edits']/row[@createdts = $timestamp]" />
			<xsl:apply-templates mode="deletions" select="/doc/query[@queryName='edits']/row[@inactivatedts = $timestamp]" />
		</xsl:if>
	</xsl:template>
	
	<xsl:template name="processModeratorEdit">
        <xsl:param name="timestamp" />
		<xsl:variable name="addModeratorRow" select="/doc/query[@queryName='edits']/row[@createdts = $timestamp and @moderator='1']" />
		<xsl:variable name="deleteModeratorRow" select="/doc/query[@queryName='edits']/row[@inactivatedts = $timestamp and @moderator='1']" />
		<xsl:if test="count($addModeratorRow) > 0 or count($deleteModeratorRow) > 0">
			<div class="row-fluid">
				<span class="span10 offset2">
					<xsl:choose>
						<xsl:when test="count($addModeratorRow) > 0 and count($deleteModeratorRow) > 0">
							Change moderator from <xsl:value-of select="$deleteModeratorRow/@pubsname"/> (<xsl:value-of select="$deleteModeratorRow/@badgeid" />)
							to <xsl:value-of select="$addModeratorRow/@pubsname"/> (<xsl:value-of select="$addModeratorRow/@badgeid" />).
						</xsl:when>
						<xsl:when test="count($addModeratorRow) > 0">
							Assign <xsl:value-of select="$addModeratorRow/@pubsname"/> (<xsl:value-of select="$addModeratorRow/@badgeid" />) as moderator.
						</xsl:when>
						<xsl:otherwise>
							Remove <xsl:value-of select="$deleteModeratorRow/@pubsname"/> (<xsl:value-of select="$deleteModeratorRow/@badgeid" />) from moderator.
						</xsl:otherwise>
					</xsl:choose>
				</span>
			</div>
		</xsl:if>
    </xsl:template>
	
	<xsl:template mode="additions" match="doc/query[@queryName='edits']/row">
		<xsl:variable name="timestamp" select="@createdts" />
		<xsl:variable name="badgeid" select="@badgeid" />
		<xsl:if test="count(/doc/query[@queryName='edits']/row[@inactivatedts = $timestamp and @badgeid = $badgeid]) = 0">
			<div class="row-fluid">
				<span class="span10 offset2">
					Add <xsl:value-of select="@pubsname"/> (<xsl:value-of select="$badgeid" />) to panel.
				</span>
			</div>
		</xsl:if>
	</xsl:template>
	
	<xsl:template mode="deletions" match="doc/query[@queryName='edits']/row">
		<xsl:variable name="timestamp" select="@inactivatedts" />
		<xsl:variable name="badgeid" select="@badgeid" />
		<xsl:if test="count(/doc/query[@queryName='edits']/row[@createdts = $timestamp and @badgeid = $badgeid]) = 0">
			<div class="row-fluid">
				<span class="span10 offset2">
					Remove <xsl:value-of select="@pubsname"/> (<xsl:value-of select="$badgeid" />) from panel.
				</span>
			</div>
		</xsl:if>
	</xsl:template>
</xsl:stylesheet>
