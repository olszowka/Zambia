<?xml version="1.0" encoding="UTF-8" ?>
<!--
    SearchSessions.xsl
    Created by Peter Olszowka on 2026-09-06.
    Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
    Page intended for BS5
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="xml"/>
    <xsl:param name="track_tag_usage" />

    <xsl:template match="/">
        <div class="container-xl">
            <form method="POST" action="ShowSessions.php" class="mt-4">
                <div class="row">
                    <div class="col">
                        Session Search (shows same data as Precis View for each session):
                    </div>
                </div>
                <xsl:choose>
                    <xsl:when test="$track_tag_usage = 'TAG_ONLY'">
                        <input id="track" type="hidden" name="track" Value="0" />
                    </xsl:when>
                    <xsl:otherwise>
                        <div class="row mt-3">
                            <div class="col-md-4 col-lg-3 offset-md-2">
                                <label for="track" >Track: </label>
                            </div>
                            <div class="col-md-12">
                                <select id="track" name="track">
                                    <option value="0" selected="selected">ANY</option>
                                    <xsl:apply-templates select="/doc/query[@queryName='tracks']" />
                                </select>
                            </div>
                        </div>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:choose>
                    <xsl:when test="$track_tag_usage = 'TRACK_ONLY'">
                        <input id="tag" type="hidden" name="tags[]" Value="" />
                    </xsl:when>
                    <xsl:otherwise>
                        <div class="row mt-3">
                            <div class="col-md-4 col-lg-3 offset-md-2">
                                <label for="tags" >Tags: </label>
                            </div>
                            <div class="col-md-16 col-lg-13 col-xl-10">
                                <div class="checkbox-list-container">
                                    <xsl:apply-templates select="doc/query[@queryName='tags']/row" />
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-10 col-xl-8">
                                <label class="tag-match-label"><input type="radio" id="tagmatch1" name="tagmatch" class="tag-match-radio" value="any" checked="checked" />Match Any Selected</label>
                                <label class="tag-match-label mt-2"><input type="radio" id="tagmatch2" name="tagmatch" class="tag-match-radio" value="all" />Match All Selected</label>
                                <div class="mt-2">
                                    <span>Select no tags to match everything</span>
                                </div>
                            </div>
                        </div>
                    </xsl:otherwise>
                </xsl:choose>
                <div class="row mt-3">
                    <div class="col-md-4 col-lg-3 offset-md-2">
                        <label for="type" >Type: </label>
                    </div>
                    <div class="col-md-12">
                        <select id="type" name="type">
                            <option value="0" selected="selected">ANY</option>
                            <xsl:apply-templates select="/doc/query[@queryName='types']" />
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 col-lg-3 offset-md-2">
                        <label for="status" >Status: </label>
                    </div>
                    <div class="col-md-15">
                        <select id="status" name="status">
                            <option value="0" selected="selected">ANY</option>
                            <xsl:apply-templates select="/doc/query[@queryName='session_statuses']" />
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4 col-lg-3 offset-md-2">
                        <label for="sessionid" >Session ID: </label>
                    </div>
                    <div class="col-md-15">
                        <input id="sessionid" name="sessionid" size="10" />
                        <span class="ps-3">Leave blank for any</span>
                    </div>
                </div>
                <xsl:choose>
                    <xsl:when test="count(/doc/query[@queryName='divisions']) &lt; 2">
                        <input id="divisionid" type="hidden" name="divisionid" Value="0" />
                    </xsl:when>
                    <xsl:otherwise>
                        <div class="row mt-3">
                            <div class="col-md-4 col-lg-3 offset-md-2">
                                <label for="divisionid" >Division: </label>
                            </div>
                            <div class="col-md-15">
                                <select id="divisionid" name="divisionid">
                                    <option value="0" selected="selected">ANY</option>
                                    <xsl:apply-templates select="/doc/query[@queryName='divisions']" />
                                </select>
                            </div>
                        </div>
                    </xsl:otherwise>
                </xsl:choose>
                <div class="row mt-3">
                    <div class="col-md-4 col-lg-3 offset-md-2">
                        <label for="searchtitle" >Title: </label>
                    </div>
                    <div class="col-md-30">
                        <input id="searchtitle" name="searchtitle" size="35" />
                        <span class="ps-3">Leave blank for any</span>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col offset-md-10 offset-lg-9 offset-xl-8">
                        <button class="btn btn-primary" type="submit" value="search">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='tags']/row">
        <div class="checkbox-list-label-wrapper">
            <label class="checkbox-list-label">
                <input type="checkbox" name="tagdest[]" id="tag_{@tagid}" class="checkbox-list-check mycontrol tag-check" value="{@tagid}" />
                <xsl:value-of select="@tagname" />
            </label>
        </div>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='tracks']/row">
        <option value="{@trackid}">
            <xsl:value-of select="@trackname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='types']/row">
        <option value="{@typeid}">
            <xsl:value-of select="@typename" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='session_statuses']/row">
        <option value="{@statusid}">
            <xsl:value-of select="@statusname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='divisions']/row">
        <option value="{@divisionid}">
            <xsl:value-of select="@divisionname" />
        </option>
    </xsl:template>

</xsl:stylesheet>
