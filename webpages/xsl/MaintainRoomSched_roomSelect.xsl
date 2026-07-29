<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2011-01-02;
    Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:param name="showUnschedRmsCHK" select="'0'" />
    <xsl:param name="returnToPage" select="''" />
    <xsl:template match="/">
        <div class="container-xxl">
            <form id="maintain-room-sched-room-form" class="mt-4" name="selroomform" method="POST" action="MaintainRoomSched.php">
                <div class="d-flex flex-wrap align-items-center column-gap-3">
                    <label for="selroom">Select Room:</label>
                    <select name="selroom" id="selroom">
                        <option value="0" selected="selected">Select Room</option>
                        <xsl:apply-templates select="/doc/query[@queryName='rooms']/row" />
                    </select>
                    <button type="submit" name="submit" class="btn btn-primary">Fetch Room</button>
                </div>
                <xsl:if test="$returnToPage != ''">
                    <a href="{$returnToPage}">Return to report</a>
                </xsl:if>
                <div class="form-check mt-3">
                    <input type="checkbox" class="form-check-input" id="showUnschedRmsCHK" name="showUnschedRmsCHK" value="1">
                        <xsl:if test="$showUnschedRmsCHK = '1'">
                            <xsl:attribute name="checked">checked</xsl:attribute>
                        </xsl:if>
                    </input>
                    <label class="form-check-label" for="showUnschedRmsCHK">Include unscheduled rooms</label>
                </div>
                <div class="text-dark mt-3">For any session where you are rescheduling, please read the Notes for Programming Committee.</div>
            </form>
            <hr />
        </div>
    </xsl:template>
    <xsl:template match="/doc/query[@queryName='rooms']/row">
        <option value="{@roomid}" data-is-scheduled="{@is_scheduled}">
            <xsl:value-of select="@roomname" />
            <xsl:if test="@function">
                <xsl:text> (</xsl:text><xsl:value-of select="@function" /><xsl:text>)</xsl:text>
            </xsl:if>
        </option>
    </xsl:template>
</xsl:stylesheet>
