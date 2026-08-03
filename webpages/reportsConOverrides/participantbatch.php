<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$userIDPrompt = USER_ID_PROMPT;
$report = [];
$report['name'] = 'Participant Batches';
$report['description'] = 'Which batch each participant/user is in';
$report['categories'] = array(
    'Boskone Central' => 700,
    'Participant Info Reports' => 2000
);
$report['columns'] = array(
    array(),
    array(),
    array(),
    array(),
    array(),
    array()
);
$report['additionalOptions'] = array("order" => array(array(4, 'asc')));
$report['queries'] = [];
$report['queries']['participants'] = <<<'EOD'
WITH ParticipantTagList AS (
    SELECT
            P.badgeid, GROUP_CONCAT(PT.participanttagname SEPARATOR ', ') AS taglist
        FROM
                 Participants P
            JOIN ParticipantHasTag PHT USING (badgeid)
            JOIN ParticipantTags PT USING (participanttagid)
        GROUP BY
            P.badgeid
)
SELECT
        P.badgeid, P.pubsname, CD.firstname, CD.lastname, CD.badgename, PTL.taglist
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN ParticipantTagList PTL USING (badgeid)
    ORDER BY
        PTL.taglist;
EOD;
$report['xsl'] = <<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />

    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <table id="reportTable" class="report">
                    <thead>
                        <tr style="height:2.6rem">
                            <th class="report">First Name</th>
                            <th class="report">Last Name</th>
                            <th class="report">Badge Name</th>
                            <th class="report">Pubsname</th>
                            <th class="report">Batch</th>
                            <th class="report">$userIDPrompt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row">
        <tr>
            <td class="report"><xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@lastname" /></td>
            <td class="report"><xsl:value-of select="@badgename" /></td>
            <td class="report"><xsl:value-of select="@pubsname" /></td>
            <td class="report"><xsl:value-of select="@taglist" /></td>
            <td class="report"><xsl:value-of select="@badgeid" /></td>            
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
