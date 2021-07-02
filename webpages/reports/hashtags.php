<?php
$report = [];
$report['name'] = 'Session Hashtag Maintenance Report';
$report['output_filename'] = 'hashtagMaintenance.csv';
$report['description'] = 'List all scheduled sessions that need some hashtag editing, either because the current hashtag is empty or because it\'s too long.';
$report['categories'] = array(
    'Programming Reports' => 750
);
$report['multi'] = 'true';
$report['queries'] = [];
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.sessionid, S.title, S.progguiddesc, S.hashtag, PS.pubstatusname,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
		T.trackname, KC.kidscatname
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN PubStatuses PS USING (pubstatusid)
        JOIN Tracks T USING (trackid)
		JOIN KidsCategories KC USING (kidscatid)
    WHERE
             S.hashtag IS NULL
          OR S.hashtag = ''
          OR LENGTH(S.hashtag) > 26;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='sessions']/row">
                <table id="reportTable" class="table table-sm table-bordered">
                    <thead>
                        <tr class="table-primary">
                            <th class="text-nowrap">Session ID</th>
                            <th>Title</th>
                            <th>Hashtag</th>
                            <th>Issue</th>
                            <th>Track</th>
                            <th>Suitability for Children</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="doc/query[@queryName='sessions']/row"/>
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='sessions']/row">
	<xsl:variable name="sessionid" select="@sessionid" />
        <tr>
            <td>
				<xsl:call-template name="showSessionid">
					<xsl:with-param name="sessionid" select="@sessionid" />
				</xsl:call-template>
			</td>
            <td>
				<xsl:call-template name="showSessionTitle">
					<xsl:with-param name="sessionid" select="@sessionid" />
					<xsl:with-param name="title" select="@title" />
				</xsl:call-template>
			</td>
            <td><xsl:value-of select="@hashtag" /></td>
            <td>
                <xsl:choose>
                    <xsl:when test="string-length(@hashtag) > 26">
                        <span class="badge badge-warning">Too long (<xsl:value-of select="string-length(@hashtag)" />)</span>
                    </xsl:when>
                    <xsl:when test="string-length(@hashtag) = 0">
                        <span class="badge badge-warning">Missing</span>
                    </xsl:when>
                </xsl:choose>
            </td>
            <td><xsl:value-of select="@trackname" /></td>
            <td><xsl:value-of select="@kidscatname" /></td>
            <td><xsl:value-of select="@progguiddesc" /></td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
