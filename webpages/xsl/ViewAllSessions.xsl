<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- Copyright (c) 2005-2026 Peter Olszowka. All rights reserved. See copyright document for more details. -->
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:template match="/">
        <table id="reportTable" class="table table-bordered border-dark table-clear">
            <thead>
                <tr>
                    <th>Sess.<br />ID</th>
                    <th>Track</th>
                    <th>Tags</th>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Est. Atten.</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody class="table-group-divider">
                <xsl:apply-templates select="doc/query[@queryName='sessions']/row" />
            </tbody>
        </table>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
        <tr>
            <td><a href="EditSession.php?id={@sessionid}"><xsl:value-of select="@sessionid" /></a></td>
            <td><xsl:value-of select="@trackname" /></td>
            <td><xsl:value-of select="@taglist" /></td>
            <td><xsl:value-of select="@title" /></td>
            <td><xsl:value-of select="@duration" /></td>
            <td><xsl:value-of select="@estatten" /></td>
            <td><xsl:value-of select="@statusname" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
