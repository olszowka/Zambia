<?php
// Copyright (c) 2018-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Staff Members';
$report['description'] = 'List Staff Members and their priviliges';
$report['categories'] = array(
    'Zambia Administration Reports' => 1010,
);
$report['columns'] = array(
    null,
    array("orderData" => 2),
    array("visible" => false),
    array("orderData" => 4),
    array("visible" => false),
    null,
    array("orderable" => false)
);
$report['queries'] = [];
if (empty(DEFAULT_USER_PASSWORD)) {
    $report['queries']['bad_password'] = "SELECT badgeid FROM Participants WHERE 1 = 2;";
} else {
    $defaultUserPassword = DEFAULT_USER_PASSWORD;
    // Have to run a query here to find all the default passwords;
    $badPasswordArr = array();
    $prequery =<<<EOD
SELECT
        P.badgeid, P.password
    FROM
             Participants P
        JOIN UserHasPermissionRole UHPR using (badgeid)
    WHERE
        EXISTS ( /* Administrator, Staff, Senior Staff for B61 */
            SELECT * FROM UserHasPermissionRole UHPR2 WHERE
                    P.badgeid = UHPR2.badgeid
                AND UHPR2.permroleid IN (1, 2, 3)
        );
EOD;
    if (!$result = mysqli_query_exit_on_error($prequery)) {
        exit(0); //should have exited already
    }
    while ($resultObj = mysqli_fetch_object($result)) {
        if (password_verify(DEFAULT_USER_PASSWORD, $resultObj->password)) {
            $badPasswordArr[] = "'{$resultObj->badgeid}'";
        }
    }
    if (count($badPasswordArr) > 0) {
        $badPasswordList = implode(',', $badPasswordArr);
    } else {
        $badPasswordList = '-99999999';
    }

    $report['queries']['bad_password'] = "SELECT badgeid FROM Participants WHERE badgeid IN ($badPasswordList);";
}

$report['queries']['staff'] =<<<EOD
SELECT
        badgeid, P.pubsname, concat(CD.firstname,' ',CD.lastname) AS name, CONCAT(CD.lastname, CD.firstname) AS nameSort,
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)) AS pubsnameSort
    FROM
             Participants P
        JOIN CongoDump CD using (badgeid)
    WHERE
        EXISTS ( /* Administrator, Staff, Senior Staff for B61 */
            SELECT * FROM UserHasPermissionRole UHPR2 WHERE
                    P.badgeid = UHPR2.badgeid
                AND UHPR2.permroleid IN (1, 2, 3)
        )
    ORDER BY
        CD.lastname, CD.firstname;
EOD;
$report['queries']['privileges'] =<<<'EOD'
SELECT
        UHPR.badgeid,
        PR.permrolename
    FROM
             UserHasPermissionRole UHPR
        JOIN PermissionRoles PR using (permroleid)
    WHERE
        EXISTS ( /* Administrator, Staff, Senior Staff for B61 */
            SELECT * FROM UserHasPermissionRole UHPR2 WHERE
                    UHPR.badgeid = UHPR2.badgeid
                AND UHPR2.permroleid IN (1, 2, 3)
        );
EOD;
$report['xsl'] =<<<EOD
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='staff']/row">
                <table id="reportTable" class="report">
                    <thead>
                         <tr style="height:2.6rem">
                            <th class="report">Badgeid</th>
                            <th class="report">Name</th>
                            <th></th>
                            <th class="report">Name for publications</th>
                            <th></th>
                            <th class="report">Password</th>
                            <th class="report">Permission roles</th>
                        </tr>             
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='staff']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='staff']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <tr>
            <td class="report"><xsl:call-template name="showBadgeid"><xsl:with-param name="badgeid" select="@badgeid"/></xsl:call-template></td>
            <td class="report"><xsl:value-of select="@name"/></td>
            <td class="report"><xsl:value-of select="@nameSort"/></td>
            <td class="report"><xsl:value-of select="@pubsname"/></td>
            <td class="report"><xsl:value-of select="@pubsnameSort"/></td>
            <td class="report">
                <xsl:choose>
                    <xsl:when test="/doc/query[@queryName='bad_password']/row[@badgeid=\$badgeid]">
                        <xsl:text>$defaultUserPassword</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>OK</xsl:text>
                    </xsl:otherwise>                    
                </xsl:choose>
            </td>
            <td class="report">
                <xsl:apply-templates select="/doc/query[@queryName = 'privileges']/row[@badgeid = \$badgeid]"/>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='privileges']/row">
        <div><xsl:value-of select="@permrolename"/></div>
    </xsl:template>
</xsl:stylesheet>
EOD;
