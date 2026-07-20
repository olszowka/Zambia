<?xml version='1.0'?>
<!-- Created by Peter Olszowka, 20 July 2026
     Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="not(/doc/query[@queryName='schedule']/row)">
                <div class="alert alert-info">This participant is not assigned to any scheduled sessions.</div>
            </xsl:when>
            <xsl:otherwise>
                <table class="table table-condensed table-clear">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Room</th>
                            <th>Title</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:for-each select="/doc/query[@queryName='schedule']/row">
                            <tr>
                                <td><xsl:value-of select="@day"/></td>
                                <td><xsl:value-of select="@starttime"/></td>
                                <td><xsl:value-of select="@endtime"/></td>
                                <td><xsl:value-of select="@roomname"/></td>
                                <td><xsl:value-of select="@title"/></td>
                                <td><xsl:value-of select="@typename"/></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
