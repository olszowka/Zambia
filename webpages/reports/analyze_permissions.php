<?php
//	Created by Peter Olszowka on 06 June 2020
// Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'Analyze Permissions Report';
$report['description'] = 'Show permission atoms for each permroleid';
$report['categories'] = array(
    'Zambia Administration Reports' => 180,
);
$report['columns'] = array();
$report['queries'] = [];
$report['queries']['permission_roles'] =<<<'EOD'
SELECT
        PR.permroleid, PR.permrolename, PR.notes
    FROM
        PermissionRoles PR
    ORDER BY
        PR.permroleid;
EOD;
$report['queries']['permission_atoms'] =<<<'EOD'
SELECT
		PA.permatomid, PA.permatomtag, PA.notes
	FROM
		PermissionAtoms PA
    ORDER BY
        PA.permatomid;
EOD;
$report['queries']['permissions'] =<<<'EOD'
SELECT DISTINCT
		P.permatomid, P.permroleid
	FROM
		Permissions P
    ORDER BY
        P.permroleid, P.permatomid;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='permission_roles']/row">
                <table class="report">
                    <thead>
                        <tr>
                            <th class="report" rowspan="2" style="width:5rem">Permission Role ID</th>
                            <th class="report" rowspan="2">Permission Role Name</th>
                            <th class="report" rowspan="2" style="width:12rem">Permission Role Notes</th>
                            <th class="report" colspan="3">Permission Details</th>
                        </tr>
                        <tr>
                            <th class="report" style="width:5rem">Permission Atom ID</th>
                            <th class="report">Permission Atom Tag</th>
                            <th class="report">Permission Atom Notes</th>
                        </tr>
                    </thead>
                    <xsl:apply-templates select="doc/query[@queryName='permission_roles']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="doc/query[@queryName='permission_roles']/row">
        <xsl:variable name="permroleid" select="@permroleid" />
        <xsl:variable name="permissions_rows" select="/doc/query[@queryName = 'permissions']/row[@permroleid = $permroleid]" />
        <xsl:variable name="rowcount" select="count($permissions_rows)" />
        <xsl:choose>
            <xsl:when test="$rowcount=0">
                <tr>
                    <td class="report"><xsl:value-of select="$permroleid"/></td>
                    <td class="report"><xsl:value-of select="@permrolename"/></td>
                    <td class="report"><xsl:value-of select="@notes"/></td>
                    <td class="report"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
                    <td class="report"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
                    <td class="report"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
                </tr>
            </xsl:when>
            <xsl:when test="$rowcount=1">
                <tr>
                    <td class="report"><xsl:value-of select="$permroleid"/></td>
                    <td class="report"><xsl:value-of select="@permrolename"/></td>
                    <td class="report"><xsl:value-of select="@notes"/></td>
                    <xsl:call-template name="permissions_inner">
                        <xsl:with-param name="permatomid" select="$permissions_rows[1]/@permatomid" />
                    </xsl:call-template>
                </tr>
            </xsl:when>
            <xsl:otherwise>
                <tr>
                    <td class="report" rowspan="{$rowcount}"><xsl:value-of select="$permroleid"/></td>
                    <td class="report" rowspan="{$rowcount}"><xsl:value-of select="@permrolename"/></td>
                    <td class="report" rowspan="{$rowcount}"><xsl:value-of select="@notes"/></td>
                    <xsl:call-template name="permissions_inner">
                        <xsl:with-param name="permatomid" select="$permissions_rows[1]/@permatomid" />
                    </xsl:call-template>
                </tr>
                <xsl:call-template name="permissions_outer">
                    <xsl:with-param name="permission_rows_outer" select="$permissions_rows[position() > 1]" />
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template name="permissions_outer">
        <xsl:param name="permission_rows_outer" />
        <xsl:for-each select="$permission_rows_outer">
            <tr>
                <xsl:call-template name="permissions_inner">
                    <xsl:with-param name="permatomid" select="@permatomid" />
                </xsl:call-template>
            </tr>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="permissions_inner">
        <xsl:param name="permatomid" />
        <xsl:variable name="permission_row_inner" select="/doc/query[@queryName = 'permission_atoms']/row[@permatomid=$permatomid]" />
        <td class="report"><xsl:value-of select="$permission_row_inner/@permatomid" /></td>
        <td class="report"><xsl:value-of select="$permission_row_inner/@permatomtag" /></td>
        <td class="report"><xsl:value-of select="$permission_row_inner/@notes" /></td>
    </xsl:template>
</xsl:stylesheet>
EOD;
