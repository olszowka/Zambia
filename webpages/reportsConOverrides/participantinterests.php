<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$report['categories']['Boskone Central'] = 260;
$report['columns'][] = array("orderable" => false);
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Badge ID</th>
                            <th class="report">Name for Publications</th>
                            <th></th>
                            <th class="report">Name for Sorting</th>
                            <th class="report">"Workshops or presentations I'd like to run"</th>
                            <th class="report">"Panel types I am not interested in participating in"</th>
                            <th class="report">"People with whom I'd like to be on a session"</th>
                            <th class="report">"People with whom I'd rather not be on a session"</th>
                            <th class="report">"Other" Role Details</th>
                            <th class="report">Bio</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <xsl:variable name="bagdeid" select="@badgeid" />
        <tr>
            <td class="report" style="white-space: nowrap;">
                <xsl:call-template name="showBadgeid">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:value-of select="@pubsnameSort"/></td>
            <td class="report"><xsl:value-of select="@name_for_sorting"/></td>
            <td class="report"><xsl:value-of select="@yespanels"/></td>
            <td class="report"><xsl:value-of select="@nopanels"/></td>
            <td class="report"><xsl:value-of select="@yespeople"/></td>
            <td class="report"><xsl:value-of select="@nopeople"/></td>
            <td class="report"><xsl:value-of select="@otherroles"/></td>
            <td class="report"><xsl:value-of select="@bio"/></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
