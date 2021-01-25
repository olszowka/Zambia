<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2015-10-16;
	Copyright (c) 2011-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:import href="renderSurveySearch.xsl"/>
	<xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:param name="surveys" select="'0'"/>
  <xsl:param name="SurveyUsed" select="'0'"/>
	<xsl:template match="/">
		<xsl:variable name="editSessionNotes" select="doc/parameters/@editSessionNotes = 'true'" />
    <div id="message" alert-dismissible="true" fade="true" show="true" class="alert mt-4 alert-success" style="display: none;">
      <button type="button" class="close" data-dismiss="alert">&#215;</button>
    </div>
		<hr />
		<h2 class="alert alert-secondary" style="text-align: center;">
      <span stlye="font-weight: bold;">
        <xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@sessionid" />
        <xsl:text> - </xsl:text>
        <xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@title" />
      </span>
		</h2>
		<form id="assign-participants-form" method="post" action="StaffAssignParticipants.php">
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@progguiddesc">
				<div class="row mp-2">
          <div class="col col-2">
            <span class="btn btn-sm btn-secondary disabled">Program Guide Text:</span>
          </div>
          <div class="col col-10">
            <span>
              <xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@progguiddesc" />
            </span>
          </div>
				</div>
			</xsl:if>
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@persppartinfo">
				<div class="row mt-1">
					<div class="col col-2">
						<span class="btn btn-sm btn-secondary disabled">Prospective Participant Info:</span>
          </div>
          <div class="col col-10">
            <span><xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@persppartinfo" /></span>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@notesforpart">
				<div class="row mt-1">
					<div class="col col-2">
						<span class="btn btn-sm btn-secondary disabled">Notes for Participant:</span>
          </div>
          <div class="col col-10">
						<span><xsl:value-of select="/doc/query[@queryName='sessionInfo']/row/@notesforpart" /></span>
					</div>
				</div>
			</xsl:if>
			<xsl:if test="doc/query[@queryName='sessionInfo']/row/@notesforprog or $editSessionNotes">
				<div class="row mt-1">
					<div class="col col-2">
						<span class="btn btn-sm btn-secondary disabled">Notes for Program Staff:</span>
          </div>
          <div class="col col-10">
						<xsl:if test="$editSessionNotes">
							<button type="button" id="editNPS_BUT" class="btn btn-sm btn-light" style="margin-right:5px; vertical-align:middle;">Edit</button>
						</xsl:if>
						<span id="NPS_SPN" style="vertical-align:middle;"><xsl:value-of select="doc/query[@queryName='sessionInfo']/row/@notesforprog" /></span>
					</div>
				</div>
			</xsl:if>
			<hr />
      <div class="row">
        <div class="col col-6">
          <label for="no-moderator" class="radio">
            <input type="radio" name="moderator" id="no-moderator" value="" style="margin-right: 5px;">
              <xsl:if test="count(doc/query[@queryName='participantInterest']/row[@moderator=1]/@moderator) = 0">
                <xsl:attribute name="checked">checked</xsl:attribute>
              </xsl:if>
            </input>
            <xsl:text>No Moderator Selected</xsl:text>
          </label>
        </div>
        <div class="col col-2">
          <button type="submit" name="update" class="btn btn-primary">Update</button>
        </div>
      </div>
			<xsl:apply-templates select="doc/query[@queryName='participantInterest']/row" />
			<input type="hidden" name="maxtimestamp" value="{doc/query[@queryName='maxtimestamp']/row/@maxtimestamp}" />
			<input type="hidden" name="selsess" value="{doc/query[@queryName='sessionInfo']/row/@sessionid}" />
			<input type="hidden" name="numrows" value="{count(doc/query[@queryName='participantInterest']/row)}" />
			<input type="hidden" name="wasmodid" value="{doc/query[@queryName='participantInterest']/row[@moderator=1]/@badgeid}" />
			<hr />
      <div class="row">
        <div class="col col-auto">
          <label for="partDropdown">Assign participant not indicated as interested or invited.</label>
        </div>
        <xsl:if test="$SurveyUsed = '1'">
          <div class="col col-auto">
            <button type="button" id="showhideSurveyFilter" class="btn btn-secondary" onclick="toggleShowFilter();">Show Survey Filter</button>
          </div>
        </xsl:if>
      </div>
      <xsl:if test="$SurveyUsed = '1'">
        <xsl:apply-imports/>
      </xsl:if>
      <div class="row mt-4" id="assign-participant-row">
        <div class="col col-5">
          <span id="popover-target"></span>
          <div id="partDropdown-div">
            <select id="partDropdown" name="asgnpart">
              <option value="" selected="selected">Assign Participant</option>
              <xsl:apply-templates select="doc/query[@queryName='otherParticipants']/row" >
                <xsl:sort select="@sortableNameLc" />
              </xsl:apply-templates>
            </select>
          </div>
        </div>
        <div class="col col-auto">
          <button type="submit" name="update" class="btn btn-primary">Add</button>
        </div>
        <div class="col col-auto">
          <button type="button" id="BioBtn" class="btn btn-info" data-loading-text="Fetching Bio...">Show Bio</button>
        </div>
        <xsl:if test="$surveys > 0">
          <div class="col col-auto">
            <button type="button" id="SurveyBtn" class="btn btn-info" title="Show Survey Results" onclick="showSurveyResults('partDropdown', 'element');">
              Show Survey Results
            </button>
          </div>
        </xsl:if>
			</div>
		</form>
	</xsl:template>
	<xsl:template match="doc/query[@queryName='participantInterest']/row">
    <xsl:if test="preceding-sibling::*[1]/@attending='1' and @attending='0'">
      <div class="row mt-1 bs4-alert-danger">
        <div class="col col-12">
          <span style="font-weight: bold;">Not Attending</span>
        </div>
      </div>
    </xsl:if>
    <div>
      <xsl:attribute name="class">
        <xsl:text>row mp-2</xsl:text>
        <xsl:choose>
          <xsl:when test="@attending='0'">
            <xsl:text> bs4-alert-danger</xsl:text>
          </xsl:when>
          <xsl:when test="@moderator='1'">
            <xsl:text> bs4-alert-success</xsl:text>
          </xsl:when>
        </xsl:choose>
      </xsl:attribute>
      <div class="col col-2">
        <label class="checkbox">
          <input type="checkbox" value="1" style="margin-right: 5px;">
            <xsl:attribute name="name">
              <xsl:text>asgn</xsl:text>
              <xsl:value-of select="@badgeid" />
            </xsl:attribute>
            <xsl:if test="@posbadgeid">
              <xsl:attribute name="checked">checked</xsl:attribute>
            </xsl:if>
          </input>
          <xsl:text>Assigned</xsl:text>
        </label>
        <input type="hidden" name="row{position()}" value="{@badgeid}" />
        <input type="hidden" name="wasasgn{@badgeid}">
          <xsl:choose>
            <xsl:when test="@posbadgeid">
              <xsl:attribute name="value">1</xsl:attribute>
            </xsl:when>
            <xsl:otherwise>
              <xsl:attribute name="value">0</xsl:attribute>
            </xsl:otherwise>
          </xsl:choose>
        </input>
      </div>
      <div class="col col-1">
        <xsl:value-of select="@badgeid" />
      </div>
      <div class="col col-4">
        <xsl:value-of select="@pubsname" />
        <xsl:text disable-output-escaping="yes">&amp;nbsp;&amp;nbsp;&amp;nbsp;</xsl:text>
        <button type="button" rel="popover" class="btn btn-info btn-sm" data-content="{@bio}" data-original-title="Bio for {@pubsname}">Bio</button>
        <xsl:if test="@answercount > 0">
          <xsl:text disable-output-escaping="yes">&amp;nbsp;&amp;nbsp;&amp;nbsp;</xsl:text>
          <button type="button" class="btn btn-info btn-sm" title="Survey results for {@pubsname}">
            <xsl:attribute name="onclick">
              <xsl:text>showSurveyResults('</xsl:text>
              <xsl:value-of select="@badgeid"/>
              <xsl:text>', 'badgeid');</xsl:text>
            </xsl:attribute>
            Survey
          </button>
        </xsl:if>
      </div>
      <div class="col col-2">
        <xsl:text>Rank: </xsl:text>
        <xsl:choose>
          <xsl:when test="@rank='99'">
            <xsl:text>None</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="@rank" />
          </xsl:otherwise>
        </xsl:choose>
      </div>
      <div class="col col-3">
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
            <xsl:attribute name="title">Volunteered to moderate in general, but not this panel in particular</xsl:attribute>
            <xsl:text>Mod any</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
      </div>
    </div>
    <div>
      <xsl:choose>
        <xsl:when test="@attending='0'">
          <xsl:attribute name="class">row bs4-alert-danger</xsl:attribute>
        </xsl:when>
        <xsl:when test="@moderator='1'">
          <xsl:attribute name="class">row bs4-alert-success</xsl:attribute>
        </xsl:when>
        <xsl:otherwise>
          <xsl:attribute name="class">row</xsl:attribute>
        </xsl:otherwise>
      </xsl:choose>
      <div class="col col-2">
        <label class="radio">
          <input type="radio" name="moderator" id="moderator-{position()}" value="{@badgeid}" style="margin-right: 5px;">
            <xsl:if test="@moderator='1'">
              <xsl:attribute name="checked">checked</xsl:attribute>
            </xsl:if>
          </input>
          <xsl:text>Moderator</xsl:text>
        </label>
      </div>
      <div>
        <xsl:choose>
          <xsl:when test="@moderator='1'">
            <xsl:attribute name="class">col col-10</xsl:attribute>
          </xsl:when>
          <xsl:otherwise>
            <xsl:attribute name="class">col col-10 bs4-alert-info</xsl:attribute>
          </xsl:otherwise>
        </xsl:choose>
        <xsl:value-of select="@comments"/>
      </div>
    </div>
    <xsl:if test="@staff_notes">
      <div>
        <xsl:choose>
          <xsl:when test="@attending='0'">
            <xsl:attribute name="class">row bs4-alert-danger</xsl:attribute>
          </xsl:when>
          <xsl:when test="@moderator='1'">
            <xsl:attribute name="class">row bs4-alert-success</xsl:attribute>
          </xsl:when>
          <xsl:otherwise>
            <xsl:attribute name="class">row</xsl:attribute>
          </xsl:otherwise>
        </xsl:choose>
        <div class="col col-10 warning-bg">
          <xsl:value-of select="@staff_notes"/>
        </div>
      </div>
    </xsl:if>
	</xsl:template>
	<xsl:template match="doc/query[@queryName='otherParticipants']/row">
		<option value="{@badgeid}"><xsl:value-of select="@sortableName" /><xsl:text> - </xsl:text><xsl:value-of select="@badgeid" /></option>
	</xsl:template>
</xsl:stylesheet>
