<?xml version="1.0" encoding="UTF-8" ?>
<!--
    my_schedule
    Created by Peter Olszowka on 2013-12-09.
    Copyright (c) 2013-2026 Peter Olszowka. All rights reserved.
-->
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-error">No schedule sessions found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
        <div class="row mt-4">
            <div class="col-lg-6">
                <xsl:text>Session ID: </xsl:text>
                <span class="h4">
                    <span class="badge text-bg-primary"><xsl:value-of select="@sessionid" /></span>
                </span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-lg-11">
                <span class="h5">
                    <span class="badge text-bg-primary"><xsl:value-of select="@title" /></span>
                </span>
            </div>
            <div class="col-lg-4">
                <span class="h5">
                    <span class="badge text-bg-primary" title="Room"><xsl:value-of select="@roomname" /></span>
                </span>
            </div>
            <div class="col-lg-5">
                <span class="h5">
                    <span class="badge text-bg-primary" title="Track"><xsl:value-of select="@trackname" /></span>
                </span>
            </div>
            <div class="col-lg-4">
                <span class="h5">
                    <span class="badge text-bg-primary" title="Type"><xsl:value-of select="@typename" /></span>
                </span>
            </div>
            <div class="col-lg-5">
                <span class="h5">
                    <span class="badge text-bg-primary"><xsl:value-of select="@starttime" /></span>
                </span>
            </div>
            <div class="col-lg-7">
                <span class="h5">
                    <xsl:text>Duration: </xsl:text>
                    <span class="badge text-bg-primary"><xsl:value-of select="@duration" /></span>
                </span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="offset-lg-3 col-lg-4 col-xxl-3">
                <span class="badge text-bg-secondary"><xsl:text>Description</xsl:text></span>
            </div>
            <div class="col-lg-29 col-xxl-30">
                <xsl:value-of select="@progguiddesc" />
            </div>
        </div>
        <xsl:if test="@persppartinfo">
            <div class="row mt-2">
                <div class="offset-lg-3 col-lg-9 col-xl-8 col-xxl-6">
                    <span class="badge text-bg-secondary"><xsl:text>Prospective participant information</xsl:text></span>
                </div>
                <div class="col-lg-24 col-xl-25 col-xxl-27">
                    <xsl:value-of select="@persppartinfo" />
                </div>
            </div>
        </xsl:if>
        <xsl:if test="@notesforpart">
            <div class="row mt-2">
                <div class="offset-lg-3 col-lg-9 col-xl-8 col-xxl-6">
                    <span class="badge text-bg-secondary"><xsl:text>Notes for participants</xsl:text></span>
                </div>
                <div class="col-lg-24 col-xl-25 col-xxl-27">
                    <xsl:value-of select="@notesforpart" />
                </div>
            </div>
        </xsl:if>
        <div class="row mt-3">
            <div class="col-lg-12 offset-lg-3">
                <span class="badge text-bg-secondary"><xsl:text>Panelists' Publication Names (Badge Names)</xsl:text></span>
            </div>
            <div class="col-lg-9">
                <span class="badge text-bg-secondary"><xsl:text>Email addresses</xsl:text></span>
            </div>
            <div class="col-lg-12">
                <span class="badge text-bg-secondary"><xsl:text>Comments</xsl:text></span>
            </div>
        </div>
        <xsl:variable name="sessionid" select="@sessionid" />
        <xsl:apply-templates select="/doc/query[@queryName='participants']/row[@sessionid = $sessionid]" />
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='participants']/row">
        <div class="row mt-1">
            <div class="col-lg-12 offset-lg-3">
                <span><xsl:value-of select="@pubsname" /></span>
                <span><xsl:text> (</xsl:text><xsl:value-of select="@badgename" /><xsl:text>)</xsl:text></span>
                <xsl:if test="@moderator = '1'">
                    <span style="font-style:italic;"><xsl:text> mod</xsl:text></span>
                </xsl:if>
            </div>
            <div class="col-lg-9">
                <span><xsl:value-of select="@email" /></span>
            </div>
            <div class="col-lg-12">
                <span><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></span>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
