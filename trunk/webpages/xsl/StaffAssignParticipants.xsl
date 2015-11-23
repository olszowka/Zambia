<?xml version="1.0" encoding="UTF-8" ?>
<!--
    $Header$
	Created by Peter Olszowka on 2015-10-16;
	Copyright (c) 2011-2015 Peter Olszowka. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" indent="yes" method="html" />
	<xsl:template match="/">
		<xsl:variable name="editSessionNotes" select="doc/parameters/@editSessionNotes = 'true'" />
		<hr />
		<h2>
			<xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@sessionid" />
			<xsl:text> - </xsl:text>
			<xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@title" />
		</h2>
		<form id="selsesform" name="selsesform" class="form-inline" method="post" action="StaffAssignParticipants.php">
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@progguiddesc">
				<div class="row-fluid stAsPa-sesInfRow">
					<div class="span12">
						<span class="label">Program Guide Text:</span>
						<span><xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@progguiddesc" /></span>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@persppartinfo">
				<div class="row-fluid stAsPa-sesInfRow">
					<div class="span12">
						<span class="label">Prospective Participant Info:</span>
						<span><xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@persppartinfo" /></span>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@notesforpart">
				<div class="row-fluid stAsPa-sesInfRow">
					<div class="span12">
						<span class="label">Notes for Participant:</span>
						<span><xsl:value-of select="/doc/query[@queryName='sessionInfo']/row/@notesforpart" /></span>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@notesforprog or $editSessionNotes">
				<div class="row-fluid stAsPa-sesInfRow">
					<div class="span12">
						<span class="label">Notes for Program Staff:</span>
						<xsl:if test="$editSessionNotes">
							<button type="button" id="editNPS_BUT" class="btn btn-mini">Edit</button>
						</xsl:if>
						<span id="NPS_SPN"><xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@notesforprog" /></span>
					</div>
				</div>
			</xsl:if>
			<hr />
			<div class="row-container">
				<div class="row-fluid">
					<div class="span4">
						<label for="no-moderator" class="radio">
							<input type="radio" name="moderator" id="no-moderator" value="0">
								<xsl:if test="count(doc/query[@queryName='participantInterest']/row[@moderator=1]/@moderator) = 0">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</input>
							<xsl:text>No Moderator Selected</xsl:text>
						</label>
					</div>
					<div class="span2 offset3">
						<button type="submit" name="update" class="btn btn-primary">Update</button>
					</div>
				</div>
			</div>
			<xsl:apply-templates select="doc/query[@queryName='participantInterest']/row" />
			<input type="hidden" name="selsess" value="{doc/query[@queryName='sessionInfo']/row/@sessionid}" />
			<input type="hidden" name="numrows" value="{count(doc/query[@queryName='participantInterest']/row)}" />
			<input type="hidden" name="wasmodid" value="{doc/query[@queryName='participantInterest']/row[@moderator=1]/@badgeid}" />
			<hr />
			<div class="row-container">
				<label for="partDropdown">Assign participant not indicated as interested or invited.</label>
			</div>
			<div class="row-container">
				<div class="row-fluid">
					<div class="span3">
						<select id="partDropdown" name="asgnpart">
							<option value="0" selected="selected">Assign Participant</option>
							<xsl:apply-templates select="doc/query[@queryName='otherParticipants']/row" />
						</select>
					</div>
					<div class="span1">
						<button type="submit" name="update" class="btn btn-primary">Add</button>
					</div>
					<div class="span2">
						<button type="button" id="BioBtn" class="btn btn-info" data-loading-text="Fetching Bio...">Show Bio</button>
					</div>
				</div>
			</div>
		</form>
	</xsl:template>
	<xsl:template match="doc/query[@queryName='participantInterest']/row">
		<div class="row-container">
			<div>
				<xsl:choose>
					<xsl:when test="@moderator='1'"><xsl:attribute name="class">row-fluid success-bg</xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="class">row-fluid</xsl:attribute></xsl:otherwise>
				</xsl:choose>
				<div class="span2">
					<label class="checkbox">
						<input type="checkbox" value="1">
							<xsl:attribute name="name">asgn<xsl:value-of select="@badgeid" /></xsl:attribute>
							<xsl:if test="@posbadgeid">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</input>
						<xsl:text>Assigned</xsl:text>
					</label>
					<input type="hidden" name="row{position()}" value="{@badgeid}" />
					<input type="hidden" name="wasasgn{@badgeid}">
						<xsl:choose>
							<xsl:when test="@posbadgeid"><xsl:attribute name="value">1</xsl:attribute></xsl:when>
							<xsl:otherwise><xsl:attribute name="value">0</xsl:attribute></xsl:otherwise>
						</xsl:choose>
					</input>									   
				</div>
				<div class="span1"><xsl:value-of select="@badgeid" /></div>
				<div class="span4">
					<xsl:value-of select="@pubsname" />
					<xsl:text disable-output-escaping="yes">&amp;nbsp;&amp;nbsp;&amp;nbsp;</xsl:text>
					<button type="button" rel="popover" class="btn btn-info btn-mini" data-content="{@bio}" data-original-title="Bio for {@pubsname}">Bio</button>
				</div>
				<div class="span2">
					<xsl:text>Rank: </xsl:text>
					<xsl:choose>
						<xsl:when test="@rank='99'"><xsl:text>None</xsl:text></xsl:when>
						<xsl:otherwise><xsl:value-of select="@rank" /></xsl:otherwise>
					</xsl:choose>
				</div>
				<div class="span3">
					<xsl:choose>
						<xsl:when test="@willmoderate='1' and @roleid">
							<xsl:attribute name="title">Volunteered to moderate this panel and in general</xsl:attribute>
							<xsl:text>Mod this or any</xsl:text>
						</xsl:when>
						<xsl:when test="@willmoderate='1'">
							<xsl:attribute name="title">Volunteered to moderate this panel, but not in general</xsl:attribute>
							<xsl:text>Mod this</xsl:text>
						</xsl:when>
						<xsl:when test="@roleid">
							<xsl:attribute name="title">Volunteered to moderate in general, but not this panel</xsl:attribute>
							<xsl:text>Mod any but not this</xsl:text>
						</xsl:when>
						<xsl:otherwise><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></xsl:otherwise>
					</xsl:choose>
				</div>
			</div>
			<div>
				<xsl:choose>
					<xsl:when test="@moderator='1'"><xsl:attribute name="class">row-fluid success-bg</xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="class">row-fluid</xsl:attribute></xsl:otherwise>
				</xsl:choose>
				<div class="span2">
					<label class="radio">
						<input type="radio" name="moderator" id="moderator-{position()}" value="{@badgeid}">
							<xsl:if test="@moderator='1'">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
						</input>
						<xsl:text>Moderator</xsl:text>
					</label>
				</div>
				<div>
					<xsl:choose>
						<xsl:when test="@moderator='1'"><xsl:attribute name="class">span10</xsl:attribute></xsl:when>
						<xsl:otherwise><xsl:attribute name="class">span10 info-bg</xsl:attribute></xsl:otherwise>
					</xsl:choose>
					<xsl:value-of select="@comments"/>
				</div>	
			</div>
			<xsl:if test="@staff_notes">
				<div>
					<xsl:choose>
						<xsl:when test="@moderator='1'"><xsl:attribute name="class">row-fluid success-bg</xsl:attribute></xsl:when>
						<xsl:otherwise><xsl:attribute name="class">row-fluid</xsl:attribute></xsl:otherwise>
					</xsl:choose>
					<div class="span10 offset2 warning-bg">
						<xsl:value-of select="@staff_notes"/>
					</div>	
				</div>
			</xsl:if>
		</div>
	</xsl:template>
	<xsl:template match="doc/query[@queryName='otherParticipants']/row">
		<option value="{@badgeid}"><xsl:value-of select="@pubsname" /><xsl:text> - </xsl:text><xsl:value-of select="@badgeid" /></option>
	</xsl:template>
</xsl:stylesheet>
