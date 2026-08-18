<?xml version='1.0'?>
<!-- Created by Peter Olszowka, 18 Aug 2026
     Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="not(/doc/query[@queryName='sessions']/row)">
                <div class="alert alert-info">No sessions found.</div>
            </xsl:when>
            <xsl:otherwise>
                <div class="container-lg mt-3">
                    <div class="alert alert-info">Sessions are sorted by session status.</div>
                    <table class="table table-condensed table-clear table-bordered border-dark">
                        <thead>
                            <tr>
                                <th>Track</th>
                                <th>Status</th>
                                <th>Number of Sessions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Brainstorm']" />
                            </xsl:call-template>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Edit Me']" />
                            </xsl:call-template>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Vetted']" />
                            </xsl:call-template>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Assigned']" />
                            </xsl:call-template>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Scheduled']" />
                            </xsl:call-template>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Dropped']" />
                            </xsl:call-template>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Duplicate']" />
                            </xsl:call-template>
                            <xsl:call-template name="one_status">
                                <xsl:with-param name="nodes" select="doc/query[@queryName='sessions']/row[@statusname='Cancelled']" />
                            </xsl:call-template>
                        </tbody>
                    </table>

                </div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template name="one_status">
        <xsl:param name="nodes" />
        <xsl:if test="count($nodes) > 0">
            <xsl:variable name="statusname" select="$nodes[1]/@statusname" />
            <xsl:for-each select="$nodes">
                <tr>
                    <td><xsl:value-of select="@trackname" /></td>
                    <td><xsl:value-of select="@statusname" /></td>
                    <td><xsl:value-of select="@count" /></td>
                </tr>
            </xsl:for-each>
            <tr>
                <td><b>Total</b></td>
                <td><xsl:value-of select="$statusname" /></td>
                <td><xsl:value-of select="sum($nodes/@count)" /></td>
            </tr>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
