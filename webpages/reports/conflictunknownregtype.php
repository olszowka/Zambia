<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Conflict Report - Unknown RegTypes';
$report['description'] = 'Registration types that Zambia does not recognize';
$report['categories'] = array(
    'Conflict Reports' => 490,
    'Zambia Administration Reports' => 490,
);
$report['queries'] = [];
$report['queries']['regtypes'] =<<<'EOD'
SELECT DISTINCT
        C.regtype
    FROM
                  CongoDump C 
        LEFT JOIN RegTypes R USING (regtype)
    WHERE
            R.regtype IS NULL
        AND C.regtype IS NOT NULL
    ORDER BY
        C.Regtype;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='regtypes']/row">
                <table class="report">
                    <tr>
                        <th class="report" style="">Reg Types</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='regtypes']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='regtypes']/row">
        <tr>
            <td class="report"><xsl:value-of select="@regtype" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
