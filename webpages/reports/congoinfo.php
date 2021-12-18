<?php
// Copyright (c) 2018-2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Registration data dump';
$report['multi'] = 'true';
$report['output_filename'] = 'congoinfo.csv';
$report['description'] = 'Shows all participant information retreived from the registration system';
$report['categories'] = array(
    'Events Reports' => 500,
    'Zambia Administration Reports' => 500,
    'Participant Info Reports' => 500,
);
$report['columns'] = array(
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null,
    null
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        badgename, badgeid, regtype, lastname, firstname, phone, email, postaddress1,
        postaddress2, postcity, poststate, postzip, postcountry
    FROM
        CongoDump
    WHERE
        badgeid IS NOT NULL
    ORDER BY
        badgename;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table id="reportTable" class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2" valign="top" style="padding-top:0.7rem">Badge Name</th>
                            <th rowspan="2" valign="top" style="padding-top:0.7rem">Badge Id</th>
                            <th rowspan="2" valign="top" style="padding-top:0.7rem">Reg Type</th>
                            <th rowspan="2" valign="top" style="padding-top:0.7rem">Last Name</th>
                            <th rowspan="2" valign="top" style="padding-top:0.7rem">First Name</th>
                            <th rowspan="2" valign="top" style="padding-top:0.7rem">Phone</th>
                            <th rowspan="2" valign="top" style="padding-top:0.7rem">Email</th>
                            <th colspan="6">Postal Address</th>
                        </tr>
                        <tr style="height:2.6rem">
                            <th>Line 1</th>
                            <th>Line 2</th>
                            <th>City</th>
                            <th>State</th>
                            <th>Zip (Code)</th>
                            <th>Country</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="doc/query[@queryName='participants']/row" />
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td><xsl:value-of select="@badgename" /></td>
            <td><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td><xsl:value-of select="@regtype" /></td>
            <td><xsl:value-of select="@lastname" /></td>
            <td><xsl:value-of select="@firstname" /></td>
            <td><xsl:value-of select="@phone" /></td>
            <td><xsl:value-of select="@email" /></td>
            <td><xsl:value-of select="@postaddress1" /></td>
            <td><xsl:value-of select="@postaddress2" /></td>
            <td><xsl:value-of select="@postcity" /></td>
            <td><xsl:value-of select="@poststate" /></td>
            <td><xsl:value-of select="@postzip" /></td>
            <td><xsl:value-of select="@postcountry" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
