<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'New Comps Report ';
$report['description'] = 'Show count of how many sessions each participant is scheduled for broken down by division (disregarding signings)';
$report['categories'] = array(
    'Registration Reports' => 1170,
);
$report['queries'] = [];
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.pubsname, P.badgeid, CD.regtype, CD.email, CD.lastname, CD.firstname, 
        IFNULL(subQ.total, 0) as total, IFNULL(subQ.py, 0) as py, IFNULL(subQ.ev, 0) as ev,
        IFNULL(subQ.gl, 0) as gl, IFNULL(subQ.gt, 0) as gt, IFNULL(subQ.grpg, 0) as grpg, 
        IFNULL(subQ.total, 0) - IFNULL(subQ.py, 0) - IFNULL(subQ.ev, 0) - IFNULL(subQ.gl, 0)
          - IFNULL(subQ.gt, 0) - IFNULL(subQ.grpg, 0) AS other
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN (
        	SELECT
        	        POS.badgeid, Count(*) AS total,
        	        SUM(IF((S.divisionid=2 OR S.divisionid=8),1,0)) AS py, /* programming or youth services divisions */
        	        SUM(IF((S.divisionid=3),1,0)) AS ev, /* events divisions */
        	        SUM(IF((S.divisionid=9 AND S.typeid = 23),1,0)) AS gl, /* gaming division and LARP type */
        	        SUM(IF((S.divisionid=9 AND S.typeid = 25),1,0)) AS gt, /* gaming division and board game type */
        	        SUM(IF((S.divisionid=9 AND S.typeid = 26),1,0)) AS grpg /* gaming division and tabletop rpg type */
        		FROM
        		         Schedule SCH
        		    JOIN ParticipantOnSession POS USING (sessionid)
        		    JOIN Sessions S USING (sessionid)
        		WHERE
        		        S.typeid != 7 /* signing */
        		    AND S.pubstatusid = 2 /* Public */
        		GROUP BY POS.badgeid    
        	    ) AS subQ USING (badgeid)
    WHERE
        P.interested = 1
    ORDER BY
        CD.regtype, subQ.total;

EOD;
$report['queries']['sessions'] =<<<'EOD'
SELECT
        S.title, D.divisionname, TY.typename, TR.trackname, POS.badgeid, S.sessionid,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
             Schedule SCH
        JOIN Sessions S using (sessionid)
        JOIN Divisions D using (divisionid)
        JOIN Types TY using (typeid)
        JOIN Tracks TR using (trackid)
        JOIN ParticipantOnSession POS using (sessionid)
    WHERE
            S.pubstatusid = 2 /* Public */
        AND S.typeid != 7 /* signing */
    ORDER BY
        SCH.starttime;
EOD;
$report['queries']['otherSessions'] =<<<'EOD'
SELECT
        S.title, D.divisionname, TY.typename, TR.trackname, POS.badgeid, 
        DATE_FORMAT(ADDDATE('$ConStartDatim$', SCH.starttime), "%a %l:%i %p") AS starttime
    FROM
             Schedule SCH
        JOIN Sessions S using (sessionid)
        JOIN Divisions D using (divisionid)
        JOIN Types TY using (typeid)
        JOIN Tracks TR using (trackid)
        JOIN ParticipantOnSession POS using (sessionid)
    WHERE
           (     S.divisionid IN (1,4,5,6,7)
             OR (S.divisionid = 9 AND S.typeid NOT IN (23, 25, 26))
           ) AND S.pubstatusid = 2 /* Public */
             AND S.typeid != 7 /* signing */
    ORDER BY
        SCH.starttime;
EOD;
$report['queries']['permissionRoles'] =<<<'EOD'
SELECT
        PR.permrolename, P.badgeid
    FROM
             Participants P
        JOIN UserHasPermissionRole UHPR using (badgeid)
        JOIN PermissionRoles PR using (permroleid)
    WHERE 
            P.interested = 1
        AND PR.permroleid NOT IN (1,2) /* administrator, staff */
        AND EXISTS (
            SELECT *
                FROM
                         Schedule SCH
                    JOIN ParticipantOnSession POS using (sessionid)
                WHERE
                    POS.badgeid = P.badgeid
                )
     ORDER BY P.badgeid;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='participants']/row">
                <style>
                    td.noborder {
                        border:none !important;
                        border-collapse: separate;
                        }
                    span.day, span.sessionid {
                        display: inline-block;
                        width: 9em;
                        }
                    tr.mainrow td {
                        border-top: 2px solid black;
                        }
                    table.noleftborder {
                        border-left: none !important;
                        }
                </style>
                <table class="report noleftborder">
                    <col style="width:4.75em;" />
                    <col style="width:12em;" />
                    <col style="width:10em;" />
                    <col style="width:8em;" />
                    <col style="width:12em;" />
                    <col style="width:8.5em;" />
                    <col style="width:7em;" />
                    <col style="width:4.5em;" />
                    <col style="width:4.5em;" />
                    <col style="width:4.5em;" />
                    <col style="width:4.5em;" />
                    <col style="width:4.5em;" />
                    <col style="width:14em;" />
                    <tr>
                        <th class="report" >Badge ID</th>
                        <th class="report" >Publication Name</th>
                        <th class="report" >Last Name</th>
                        <th class="report" >First Name</th>
                        <th class="report" >Email</th>
                        <th class="report" >Registration Type</th>
                        <th class="report" >Prog. or Youth</th>
                        <th class="report" >Events</th>
                        <th class="report" >LARPs</th>
                        <th class="report" >Board G's</th>
                        <th class="report" >TT RPG's</th>
                        <th class="report" >Other</th>
                        <th class="report" >Participant Role(s)</th>
                    </tr>
                    <xsl:apply-templates select="doc/query[@queryName='participants']/row"/>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="alert alert-danger">No results found.</div>
            </xsl:otherwise>                    
        </xsl:choose>
    </xsl:template>
    <xsl:template match="doc/query[@queryName='participants']/row">
        <xsl:variable name="badgeid" select="@badgeid" />
        <tr class="mainrow">
            <td class="report"><xsl:value-of select="@badgeid" /></td>
            <td class="report">
                <xsl:call-template name="showPubsname">
                    <xsl:with-param name="badgeid" select = "@badgeid" />
                    <xsl:with-param name="pubsname" select = "@pubsname" />
                </xsl:call-template>
            </td>
            <td class="report"><xsl:value-of select="@lastname" /></td>
            <td class="report"><xsl:value-of select="@firstname" /></td>
            <td class="report"><xsl:value-of select="@email" /></td>
            <td class="report"><xsl:value-of select="@regtype" /></td>
            <td class="report"><xsl:value-of select="@py" /></td>
            <td class="report"><xsl:value-of select="@ev" /></td>
            <td class="report"><xsl:value-of select="@gl" /></td>
            <td class="report"><xsl:value-of select="@gt" /></td>
            <td class="report"><xsl:value-of select="@grpg" /></td>
            <td class="report"><xsl:value-of select="@other" /></td>
            <td class="report"><xsl:apply-templates select="/doc/query[@queryName='permissionRoles']/row[@badgeid=$badgeid]"/></td>
        </tr>
        <xsl:if test="@total&gt;0 and @total&lt;3">
            <xsl:apply-templates select="/doc/query[@queryName='sessions']/row[@badgeid=$badgeid]"/>
        </xsl:if>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='permissionRoles']/row">
         <div><xsl:value-of select="@permrolename" /></div>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='sessions']/row">
        <tr>
            <td colspan="2" class="noborder"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
            <td colspan="11" class="report">
                 <span class="day"><xsl:value-of select="@starttime" /></span>
                 <span class="sessionid">
                     <xsl:call-template name="showSessionid">
                         <xsl:with-param name="sessionid" select = "@sessionid" />
                     </xsl:call-template>
				</span>
                 <span class="title">
                      <xsl:call-template name="showSessionTitle">
                          <xsl:with-param name="sessionid" select = "@sessionid" />
                          <xsl:with-param name="title" select = "@title" />
                      </xsl:call-template>
                 </span>
           </td>
        </tr>
    </xsl:template>
</xsl:stylesheet>
EOD;
