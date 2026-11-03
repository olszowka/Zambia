<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2026-09-03;
    Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="count(doc/query[@queryName='previous_sessions']/row) > 0">
                <div class="container-xl">
                    <form method="post" action="SubmitImportSessions.php">
                        <xsl:apply-templates select="doc/query[@queryName='previous_sessions']/row" />
                        <input type="hidden" name="lastrownum" value="{count(doc/query[@queryName='previous_sessions']/row)}" />
                        <div class="row">
                            <div class="col offset-md-12">
                                <button type="submit" class="btn btn-primary" value="submitimport">Import</button>
                            </div>
                        </div>
                    </form>
                </div>

            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-secondary" role="alert">
                    No matching sessions found.
                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='previous_sessions']/row">
        <div class="row">
            <div class="col-md-15">
                <span class="fw-bold">
                    <xsl:value-of select="@title" />
                </span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-4 col-xl-3">
                <input type="checkbox" id = "import{position()}" name="import{position()}">
                    <xsl:if test="@importedsessionid">
                        <xsl:attribute name="disabled">disabled</xsl:attribute>
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <label for="import{position()}" class="ps-2">
                    Import
                </label>
            </div>
            <div class="col-md-7 col-xl-4">
                <span class="badge text-bg-secondary">
                    <xsl:value-of select="@trackname" />
                </span>
            </div>
            <div class="col-md-6 col-xl-4">
                <span class="badge text-bg-secondary">
                    <xsl:value-of select="@typename" />
                </span>
            </div>
            <div class="col-md-6 col-xl-4">
                <span class="badge text-bg-secondary">
                    <xsl:value-of select="@statusname" />
                </span>
            </div>
            <div class="col-md-8 col-xl-5">
                <span class="badge text-bg-primary">
                    <xsl:value-of select="@previousconname" />
                </span>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col">
                <xsl:value-of select="@progguiddesc" />
            </div>
        </div>
        <input type="hidden" name="previousconid{position()}" value="{@previousconid}" />
        <input type="hidden" name="previoussessionid{position()}" value="{@previoussessionid}" />
    <hr />
    </xsl:template>
</xsl:stylesheet>
