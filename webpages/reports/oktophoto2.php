<?php
// Copyright (c) 2018 Peter Olszowka. All rights reserved. See copyright document for more details.
$report = [];
$report['name'] = 'OK to Photograph Report -- Sessions';
$report['multi'] = 'true';
$report['output_filename'] = 'oktophoto2.csv';
$report['description'] = 'List of all sessions with participants and whether each participant granted permission for their photo to be used';
$report['categories'] = array(
    'Programming Reports' => 110,
);
$report['queries'] = [];
$report['queries']['schedule'] =<<<'EOD'
SELECT
        S.sessionid, S.title, DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime,
        R.roomid, R.roomname, TR.trackname
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
        JOIN Tracks TR USING (trackid) 
    WHERE EXISTS (
        SELECT *
            FROM
                     Schedule SCH2
                JOIN ParticipantOnSession POS2 USING (sessionid)
            WHERE
                 SCH2.sessionid = S.sessionid
        )
    ORDER BY
        TR.trackname, SCH.starttime;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        S.sessionid, P.badgeid, P.pubsname, P.use_photo
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN ParticipantOnSession POS USING (sessionid)
        JOIN Participants P USING (badgeid)
        JOIN CongoDump CD USING (badgeid)
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;
$report['xsl'] =<<<'EOD'
<?xml version="1.0" encoding="UTF-8" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:include href="xsl/reportInclude.xsl" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="doc/query[@queryName='schedule']/row">
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Track</th>
                            <th>Room Name</th>
                            <th>Start Time</th>
                            <th>Pubsname</th>
                            <th>Badgeid</th>
                            <th>Ok to Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="doc/query[@queryName='schedule']/row" />
                    </tbody>
                </table>
            </xsl:when>
            <xsl:otherwise>
                <div class="text-info">No results found.</div>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='schedule']/row">
        <xsl:variable name="sessionid" select="@sessionid" />
        <xsl:variable name="partCount" select="count(/doc/query[@queryName='participants']/row[@sessionid=$sessionid])" />
        <xsl:variable name="firstSchedRow" select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid][1]" />
        <tr>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell za-report-firstColCell">
                <xsl:call-template name="showSessionTitle">
                    <xsl:with-param name="sessionid" select = "@sessionid" />
                    <xsl:with-param name="title" select = "@title" />
                </xsl:call-template>
            </td>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                <xsl:value-of select="@trackname" />
            </td>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                <xsl:call-template name="showRoomName">
                    <xsl:with-param name="roomid" select = "@roomid" />
                    <xsl:with-param name="roomname" select = "@roomname" />
                </xsl:call-template>
            </td>
            <td rowspan="{$partCount}" class="report za-report-firstRowCell za-report-lastRowCell">
                <xsl:value-of select="@starttime" />
            </td>
            <xsl:choose>
                <xsl:when test="$partCount = 1">
                    <td class="report za-report-firstRowCell za-report-lastRowCell">
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                            <xsl:with-param name="pubsname" select = "$firstSchedRow/@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td  class="report za-report-firstRowCell za-report-lastRowCell">
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell za-report-lastColCell za-report-lastColCell">
                        <xsl:call-template name="oktophoto">
                            <xsl:with-param name="use_photo" select = "$firstSchedRow/@use_photo" />
                        </xsl:call-template>
                    </td>
                </xsl:when>
                <xsl:otherwise>
                    <td class="report za-report-firstRowCell">
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                            <xsl:with-param name="pubsname" select = "$firstSchedRow/@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell">
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "$firstSchedRow/@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-firstRowCell za-report-lastColCell">
                        <xsl:call-template name="oktophoto">
                            <xsl:with-param name="use_photo" select = "$firstSchedRow/@use_photo" />
                        </xsl:call-template>
                    </td>
                </xsl:otherwise>
            </xsl:choose>
        </tr>
        <xsl:apply-templates select="/doc/query[@queryName='participants']/row[@sessionid=$sessionid][position() > 1]" />
    </xsl:template>

    <xsl:template match="doc/query[@queryName='participants']/row" >
        <tr>
            <xsl:choose>
                <xsl:when test="position() = last()">
                    <td class="report za-report-lastRowCell">
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                            <xsl:with-param name="pubsname" select = "@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell">
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell za-report-lastColCell">
                        <xsl:call-template name="oktophoto">
                            <xsl:with-param name="use_photo" select = "@use_photo" />
                        </xsl:call-template>
                    </td>
                </xsl:when>
                <xsl:otherwise>
                    <td>
                        <xsl:call-template name="showPubsname">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                            <xsl:with-param name="pubsname" select = "@pubsname" />
                        </xsl:call-template>
                    </td>
                    <td>
                        <xsl:call-template name="showBadgeid">
                            <xsl:with-param name="badgeid" select = "@badgeid" />
                        </xsl:call-template>
                    </td>
                    <td class="report za-report-lastRowCell za-report-lastColCell">
                        <xsl:call-template name="oktophoto">
                            <xsl:with-param name="use_photo" select = "@use_photo" />
                        </xsl:call-template>
                    </td>
                </xsl:otherwise>
            </xsl:choose>
        </tr>
    </xsl:template>

    <xsl:template name="oktophoto">
        <xsl:param name="use_photo" />
        <xsl:choose>
            <xsl:when test="$use_photo='0'">
                <xsl:text>No</xsl:text>
            </xsl:when>
            <xsl:when test="$use_photo='1'">
                <xsl:text>Yes</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:text>N/A</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
EOD;
