<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2016-05-11;
    Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:variable name="selsessionid" select="/doc/parameters/@selsessionid" />
    <xsl:template match="/">
        <div class="container-xl">
            <form id="session-history-form" name="selsesform" class="mt-3" method="get" action="SessionHistory.php">
                <div class="row">
                    <div class="col-md-7 col-xl-4">
                        <label for="sessionDropdown">Select Session:</label>
                    </div>
                    <div class="col-md-21 col-xl-15">
                        <select id="sessionDropdown" name="selsess">
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
                    </div>
                    <div class="col-md-7 col-xl-6">
                        <button id="sessionBtn" type="submit" name="submit" class="btn btn-primary">
                            <xsl:if test="$selsessionid = '0'">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
                            <xsl:text>Select Session</xsl:text>
                        </button>
                    </div>
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
                    </xsl:when>
                    <xsl:otherwise>
                        <p>No participants are currently assigned.</p>
                    </xsl:otherwise>
                </xsl:choose>
                <h4>Edits</h4>
                <xsl:apply-templates select="doc/query[@queryName='timestamps']/row" />
            </xsl:if>
        </div>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='chooseSession']/row">
        <option value="{@sessionid}">
            <xsl:if test="@sessionid = $selsessionid"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
            <xsl:value-of select="@trackname"/> - <xsl:value-of select="@sessionid" /> - <xsl:value-of select="@title" />
        </option>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='currentAssignments']/row">
        <div class="row">
            <div class="col-33 offset-3">
                <xsl:value-of select="@pubsname"/> (<xsl:value-of select="@badgeid" />) <xsl:if test="@moderator='1'"><span class="za-sessionHistory-moderator">moderator</span></xsl:if>
            </div>
        </div>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='timestamps']/row">
        <xsl:variable name="timestamp" select="@timestamp" />
        <xsl:variable name="createParticipantRow" select="/doc/query[@queryName='participantedits']/row[@createdts = $timestamp][1]" />
        <xsl:variable name="inactivateParticipantRow" select="/doc/query[@queryName='participantedits']/row[@inactivatedts = $timestamp][1]" />
        <xsl:variable name="editSessionRow" select="/doc/query[@queryName='sessionedits']/row[@timestamp = $timestamp][1]" />
        <xsl:if test="count($createParticipantRow) > 0 or count($inactivateParticipantRow) > 0 or count($editSessionRow) > 0">
            <div class="row">
                <div class="col-18 offset-9 za-sessionHistory-editBanner">
                    <div class="row">
                        <div class="col-27 offset-9">
                            <xsl:choose>
                                <xsl:when test="count($createParticipantRow) > 0">
                                    <xsl:value-of select="$createParticipantRow/@crpubsname"/> (<xsl:value-of select="$createParticipantRow/@createdbybadgeid" />)
                                    <xsl:value-of select="$createParticipantRow/@createdtsformat" />
                                </xsl:when>
                                <xsl:when test="count($inactivateParticipantRow) > 0">
                                    <xsl:value-of select="$inactivateParticipantRow/@inactpubsname"/> (<xsl:value-of select="$inactivateParticipantRow/@inactivatedbybadgeid" />)
                                    <xsl:value-of select="$inactivateParticipantRow/@inactivatedtsformat" />
                                </xsl:when>
                                <xsl:when test="count($editSessionRow) > 0">
                                    <xsl:value-of select="$editSessionRow/@name"/> (<xsl:value-of select="$editSessionRow/@badgeid" />)
                                    <xsl:value-of select="$editSessionRow/@tsformat" />
                                </xsl:when>
                            </xsl:choose>
                        </div>
                    </div>
                </div>
            </div>
            <xsl:call-template name="processModeratorEdit">
                <xsl:with-param name="timestamp" select = "$timestamp" />
            </xsl:call-template>
            <xsl:apply-templates mode="additions" select="/doc/query[@queryName='participantedits']/row[@createdts = $timestamp]" />
            <xsl:apply-templates mode="deletions" select="/doc/query[@queryName='participantedits']/row[@inactivatedts = $timestamp]" />
            <xsl:apply-templates select="/doc/query[@queryName='sessionedits']/row[@timestamp = $timestamp]" />
        </xsl:if>
    </xsl:template>

    <xsl:template name="processModeratorEdit">
        <xsl:param name="timestamp" />
        <xsl:variable name="addModeratorRow" select="/doc/query[@queryName='participantedits']/row[@createdts = $timestamp and @moderator='1']" />
        <xsl:variable name="deleteModeratorRow" select="/doc/query[@queryName='participantedits']/row[@inactivatedts = $timestamp and @moderator='1']" />
        <xsl:if test="count($addModeratorRow) > 0 or count($deleteModeratorRow) > 0">
            <div class="row">
                <div class="col-15 offset-3">
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
                </div>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template mode="additions" match="doc/query[@queryName='participantedits']/row">
        <xsl:variable name="timestamp" select="@createdts" />
        <xsl:variable name="badgeid" select="@badgeid" />
        <xsl:if test="count(/doc/query[@queryName='participantedits']/row[@inactivatedts = $timestamp and @badgeid = $badgeid]) = 0">
            <div class="row">
                <div class="col-15 offset-3">
                    Add <xsl:value-of select="@pubsname"/> (<xsl:value-of select="$badgeid" />) to panel.
                </div>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template mode="deletions" match="doc/query[@queryName='participantedits']/row">
        <xsl:variable name="timestamp" select="@inactivatedts" />
        <xsl:variable name="badgeid" select="@badgeid" />
        <xsl:if test="count(/doc/query[@queryName='participantedits']/row[@createdts = $timestamp and @badgeid = $badgeid]) = 0">
            <div class="row">
                <div class="col-15 offset-3">
                    Remove <xsl:value-of select="@pubsname"/> (<xsl:value-of select="$badgeid" />) from panel.
                </div>
            </div>
        </xsl:if>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessionedits']/row">
        <xsl:variable name="timestamp" select="@timestamp" />
        <xsl:variable name="badgeid" select="@badgeid" />
        <div class="row">
            <div class="col-18 offset-18">
                <xsl:value-of select="@codedescription" /> —
                <xsl:if test="@editdescription"><xsl:value-of select="@editdescription" /> — </xsl:if>
                status:<xsl:value-of select="@statusname" />
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
