<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2026-06-06;
    Copyright (c) 2026 Peter Olszowka. All rights reserved.
    See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="conName" select="''"/>
    <xsl:param name="conNumDays" select="''"/>
    <xsl:param name="conStartDate" select="''"/>
    <xsl:param name="conEndDate" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <xsl:variable name="staff_overview" select="/doc/customText/@staff_overview" />
        <div class="mt-2"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></div>
        <xsl:choose>
            <xsl:when test="string-length($staff_overview) > 0">
                <xsl:value-of select="$staff_overview" disable-output-escaping="yes"/>
            </xsl:when>
            <xsl:otherwise>
                <p>Please note the tabs above. One of them will take you to your participant view. Another will allow you to manage Sessions. Note that "Sessions" is the generic term Zambia uses for anything it can schedule, e.g. Panels, Events, Readings, etc.</p>
                <p>The general flow of sessions over time is:</p>
                <dl class="ms-4">
                    <dt>Brainstorm</dt>
                    <dd>
                        <xsl:text>If </xsl:text>
                        <xsl:value-of select="$conName" />
                        <xsl:text> is using the brainstorm functionality, these are sessions created by non-staff members which haven't yet been edited by a staff member.</xsl:text>
                    </dd>
                    <dt>Edit Me</dt>
                    <dd>New session idea that a staff member entered. An idea entered by a brainstorm user that is non-offensive and the least bit feasible should be moved to this status. These are still rough and may well have issues. There still could be duplicates.</dd>
                    <dt>Vetted</dt>
                    <dd>A real session that we'd like to see happen. At this point the language should be fairly close to final in the description. Proofreading should have happened. More fields are required at this point. This is the minimal status that participants are allowed to sign up for. Avoid duplicates, but many of these still will not happen for various reasons.</dd>
                    <dt>Assigned</dt>
                    <dd>Session has participants assigned to it.</dd>
                    <dt>Scheduled</dt>
                    <dd>Session is in the schedule (don't set this by hand as Zambia actually sets this for you when you schedule it in a room!) The language needs to match what you want to see <strong>published</strong>.</dd>
                </dl>
                <p>There are 3 other statuses that a session can have:</p>
                <dl class="ms-4">
                    <dt>Dropped</dt>
                    <dd>This item is no longer under consideration and is unlikely even to be mined for future ideas.</dd>
                    <dt>Duplicate</dt>
                    <dd>Might have been a good session, but was too close or identical to another one.</dd>
                    <dt>Cancelled</dt>
                    <dd>Over all a good idea, but it isn't going to happen this year. Generally used later in the programming process. You should probably still say why it was cancelled in the "Notes for Program Committee" field. This is a category we can mine for ideas in future years</dd>
                </dl>
                <p>
                    <xsl:text>Some details regarding </xsl:text>
                    <xsl:value-of select="$conName" />
                    <xsl:text>:</xsl:text>
                </p>
                <dl class="ms-4">
                    <dd>
                        <xsl:text>Convention dates: </xsl:text>
                        <xsl:value-of select="$conStartDate" />
                        <xsl:text> - </xsl:text>
                        <xsl:value-of select="$conEndDate" />
                    </dd>
                    <dd>
                        <xsl:text>Number of days: </xsl:text>
                        <xsl:value-of select="$conNumDays" />
                    </dd>
                </dl>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
