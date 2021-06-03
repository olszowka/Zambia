<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Pubs - Participant Bio and pubname';
$report['description'] = 'Show the badgeid, pubsname and bio for each participant who is on at least one scheduled, public session.';
$report['categories'] = array(
    'Publication Reports' => 870,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, P.bio, P.pronouns
    FROM
            Participants P
        JOIN CongoDump CD USING (badgeid)
    WHERE
        EXISTS (
            SELECT SCH.sessionid
                FROM
                        Schedule SCH
                    JOIN ParticipantOnSession POS USING (sessionid)
                    JOIN Sessions S USING (sessionid)
                WHERE
                        S.pubstatusid = 2 /* public */
                    AND POS.badgeid = P.badgeid
            )
    ORDER BY
        IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;

if (defined('USE_PRONOUNS') && USE_PRONOUNS) {

    $report['xsl'] =<<<'EOD'
    <?xml version="1.0" encoding="UTF-8" ?>
    <xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
        <xsl:output encoding="UTF-8" indent="yes" method="html" />
        <xsl:include href="xsl/reportInclude.xsl" />
        <xsl:template match="/">
            <xsl:choose>
                <xsl:when test="doc/query[@queryName='participants']/row">
                    <table class="report">
                        <col style="width:6em;" />
                        <col style="width:12em;" />
                        <col style="width:8em;" />
                        <col />
                        <tr>
                            <th class="report">Badge Id</th>
                            <th class="report">Name for Publications</th>
                            <th class="report">Pronouns</th>
                            <th class="report">Biography</th>
                        </tr>
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
                <td class="report"><xsl:value-of select="@pronouns" /></td>
                <td class="report"><xsl:value-of select="@bio" /></td>
            </tr>
        </xsl:template>
    </xsl:stylesheet>
    EOD;
} else {
    $report['xsl'] =<<<'EOD'
    <?xml version="1.0" encoding="UTF-8" ?>
    <xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
        <xsl:output encoding="UTF-8" indent="yes" method="html" />
        <xsl:include href="xsl/reportInclude.xsl" />
        <xsl:template match="/">
            <xsl:choose>
                <xsl:when test="doc/query[@queryName='participants']/row">
                    <table class="report">
                        <col style="width:6em;" />
                        <col style="width:12em;" />
                        <col />
                        <tr>
                            <th class="report">Badge Id</th>
                            <th class="report">Name for Publications</th>
                            <th class="report">Biography</th>
                        </tr>
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
                <td class="report"><xsl:value-of select="@bio" /></td>
            </tr>
        </xsl:template>
    </xsl:stylesheet>
    EOD;
}