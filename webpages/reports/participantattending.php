<?php
// Copyright (c) 2022-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// Created by Peter Olszowka on 2022-11-04
$report = [];
$report['name'] = 'Participant Attending';
$report['description'] = 'How each participant is attending (Assumes there is a survey question with a name of "AttendanceType")';
$report['categories'] = array(
    'Participant Info Reports' => 500
);
$report['columns'] = array(
    array(),
    array(),
    array(),
    array(),
    array()
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, CD.firstname, CD.lastname,
        JSON_VALUE(PSR.answers, '$.AttendanceType.value') AS attendancetype
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN ParticipantSurveyResponses PSR ON PSR.badgeid = P.badgeid
    WHERE
        EXISTS ( SELECT *
                    FROM
                             UserHasPermissionRole UHPR2
                        JOIN PermissionRoles PR USING (permroleid)
                    WHERE
                            UHPR2.badgeid = P.badgeid
                        AND PR.permrolename = 'Participant'
            )
    ORDER BY
        P.pubsname;
EOD;
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
                            <th class="report">Badge Id</th>
                            <th class="report">Publication Name</th>
                            <th class="report">First Name</th>
                            <th class="report">Last Name</th>
                            <th class="report">Attendance Type</th>
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
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td class="report"><xsl:value-of select="@pubsname" /></td>
            <td class="report"><xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@lastname" /></td>
            <td class="report"><xsl:value-of select="@attendancetype" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
