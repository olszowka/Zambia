<?php
// Copyright (c) 2019 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Sample Configurable Report';
$report['description'] = 'Shows participant name for publication and shows use_photo flag only if configured';
$report['categories'] = array(
    'Participant Info Reports' => 9999,
);
$report['params'] = array();
$report['params']['enable_photo_question'] = ENABLE_USE_PHOTO_QUESTION;
$report['columns'] = array();
$report['columns'][] = array("width" => "15em");
if (ENABLE_USE_PHOTO_QUESTION) {
    $report['columns'][] = array("width" => "5em");
}
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, P.use_photo
    FROM
        Participants P;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="enable_photo_question" />
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">Name for Publications</th>
                            <xsl:if test="$enable_photo_question">
                                <th class="report">May use photo</th>
                            </xsl:if>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row" />
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <xsl:if test="$enable_photo_question">
                <td class="report">
                    <xsl:choose>
                        <xsl:when test="@use_photo = '1'">
                            <xsl:text>Yes</xsl:text>
                        </xsl:when>
                        <xsl:when test="@use_photo = '0'">
                            <xsl:text>No</xsl:text>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:text>Did not answer</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </td>
            </xsl:if>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
