<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2011-01-02;
    Copyright (c) 2011-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="html" />
    <xsl:param name="conflict" select="'0'" />
    <xsl:param name="trackTagUsage" select="''" />
    <xsl:param name="conNumDays" select="1" />
    <xsl:template match="/">
        <xsl:variable name="room" select="/doc/query[@queryName='roomInfo']/row" />
        <form id="rmschdform" class="zambia-form" name="rmschdform" method="POST" action="MaintainRoomSched.php">
            <div class="container-xl">
                <input type="hidden" name="showUnschedRmsCHK" value="1" />
                <xsl:if test="$conflict = '1'">
                    <button type="submit" name="override" class="btn btn-danger">Save Anyway!</button>
                    <br /><hr />
                </xsl:if>
                <div class="d-flex justify-content-center">
                    <h3><xsl:value-of select="$room/@roomid" /><xsl:text> - </xsl:text><xsl:value-of select="$room/@roomname" /></h3>
                </div>
                <h4>
                    <span class="badge text-bg-info">Characteristics</span>
                </h4>
                <table class="mt-3 table table-clear table-bordered border-dark">
                    <thead>
                        <tr>
                            <th>Function</th>
                            <th>Floor</th>
                            <th>Dimensions</th>
                            <th>Area</th>
                            <th>Height</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><xsl:value-of select="$room/@function" /></td>
                            <td><xsl:value-of select="$room/@floor" /></td>
                            <td><xsl:value-of select="$room/@dimensions" /></td>
                            <td><xsl:value-of select="$room/@area" /></td>
                            <td><xsl:value-of select="$room/@height" /></td>
                        </tr>
                        <xsl:if test="$room/@notes">
                            <tr>
                                <td colspan="5">
                                    <div class="alert alert-info"><xsl:value-of select="$room/@notes" /></div>
                                </td>
                            </tr>
                        </xsl:if>
                    </tbody>
                </table>
                <h4 class="mt-4">
                    <span class="badge text-bg-info">Room Sets</span>
                </h4>
                <table class="mt-3 table table-clear table-bordered border-dark" style="max-width:550px">
                    <thead>
                        <tr>
                            <th>Room Set</th>
                            <th>Capacity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="/doc/query[@queryName='roomSets']/row" />
                    </tbody>
                </table>
            </div>
            <div class = "container-xxl">
                <hr class="mt-4" />
                <h4 class="label">Current Room Schedule</h4>
                <table class="mt-2 table table-clear table-bordered border-dark">
                    <thead>
                        <tr>
                            <th>Delete</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Title</th>
                            <xsl:if test="$trackTagUsage != 'TAG_ONLY'">
                                <th>Track</th>
                            </xsl:if>
                            <xsl:if test="$trackTagUsage != 'TRACK_ONLY'">
                                <th>Tags</th>
                            </xsl:if>
                            <th>Session ID</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <xsl:apply-templates select="/doc/query[@queryName='currentSchedule']/row" />
                    </tbody>
                </table>
            </div>
            <div class="container-xl">
                <h4 class="label">Add To Room Schedule</h4>
                <table id="add-to-room-schedule-table" class="table table-clear compressed align-middle">
                    <xsl:apply-templates select="/doc/query[@queryName='newRoomSlots']/row" />
                </table>
                <input type="hidden" name="selroom" value="{$room/@roomid}" />
                <input type="hidden" name="numrows" value="{count(/doc/query[@queryName='currentSchedule']/row)}" />
                <div class="SubmitDiv"><button type="submit" name="update" class="btn btn-primary">Update</button></div>
            </div>
        </form>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='roomSets']/row">
        <tr>
            <td><xsl:value-of select="@roomsetname" /></td>
            <td><xsl:value-of select="@capacity" /></td>
        </tr>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='currentSchedule']/row">
        <tr class="current-schedule-row">
            <td>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="del{position()}" value="1" />
                </div>
                <input type="hidden" name="row{position()}" value="{@scheduleid}" />
                <input type="hidden" name="rowsession{position()}" value="{@sessionid}" />
                <button type="button" class="btn btn-sm btn-outline-secondary revert-row-btn" title="Revert unsaved edits" style="display:none">&#8634;</button>
            </td>
            <td class="current-schedule-edit-cell">
                <div class="d-flex align-items-center gap-1 flex-wrap">
                    <xsl:if test="$conNumDays &gt; 1">
                        <select class="current-schedule-editable" name="editday{position()}" data-original="{@editday}">
                            <xsl:apply-templates select="/doc/query[@queryName='days']/row">
                                <xsl:with-param name="selectedValue" select="@editday" />
                            </xsl:apply-templates>
                        </select>
                    </xsl:if>
                    <input type="text" class="current-schedule-editable schedule-time-input" name="edittime{position()}" value="{@edittime}" data-original="{@edittime}" size="5" placeholder="h:mm" />
                    <select class="current-schedule-editable" name="editampm{position()}" data-original="{@editampm}">
                        <option value="0">
                            <xsl:if test="@editampm = '0'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:text>AM</xsl:text>
                        </option>
                        <option value="1">
                            <xsl:if test="@editampm = '1'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:text>PM</xsl:text>
                        </option>
                    </select>
                </div>
            </td>
            <td>
                <input type="text" class="current-schedule-editable schedule-duration-input" name="editduration{position()}" value="{@editduration}" data-original="{@editduration}" size="5" />
            </td>
            <td><xsl:value-of select="@title" /></td>
            <xsl:if test="$trackTagUsage != 'TAG_ONLY'">
                <td><xsl:value-of select="@trackname" /></td>
            </xsl:if>
            <xsl:if test="$trackTagUsage != 'TRACK_ONLY'">
                <td><xsl:value-of select="@taglist" /></td>
            </xsl:if>
            <td><a href="EditSession.php?id={@sessionid}"><xsl:value-of select="@sessionid" /></a></td>
            <td><xsl:value-of select="@typename" /></td>
        </tr>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='newRoomSlots']/row">
        <tr>
            <td>
                <xsl:if test="$conNumDays &gt; 1">
                    <span class="ps-2 pe-2">
                        <select class="ps-2 pe-2" name="day{@slot}">
                            <option value="0">
                                <xsl:if test="@day = '0'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                                <xsl:text>Day </xsl:text>
                            </option>
                            <xsl:apply-templates select="/doc/query[@queryName='days']/row">
                                <xsl:with-param name="selectedValue" select="@day" />
                            </xsl:apply-templates>
                        </select>
                    </span>
                </xsl:if>
                <span class="ps-2 pe-2">
                    <select class="ps-2 pe-2" name="hour{@slot}">
                        <option value="-1">
                            <xsl:if test="@hour = '-1'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:text>Hour </xsl:text>
                        </option>
                        <xsl:apply-templates select="/doc/query[@queryName='hours']/row">
                            <xsl:with-param name="selectedValue" select="@hour" />
                        </xsl:apply-templates>
                    </select>
                </span>
                <span class="ps-2 pe-2">
                    <select class="ps-2 pe-2" name="min{@slot}">
                        <option value="-1">
                            <xsl:if test="@min = '-1'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:text>Min </xsl:text>
                        </option>
                        <xsl:apply-templates select="/doc/query[@queryName='minutes']/row">
                            <xsl:with-param name="selectedValue" select="@min" />
                        </xsl:apply-templates>
                    </select>
                </span>
                <span class="ps-2 pe-2">
                    <select class="ps-2 pe-2" name="ampm{@slot}">
                        <option value="0">
                            <xsl:if test="@ampm = '0'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:text>AM</xsl:text>
                        </option>
                        <option value="1">
                            <xsl:if test="@ampm = '1'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                            <xsl:text>PM</xsl:text>
                        </option>
                    </select>
                </span>
            </td>
            <td class="room-select-td">
                <select class="ps-2 pe-2" name="sess{@slot}">
                    <option value="unset">
                        <xsl:if test="@sess = 'unset'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
                        <xsl:text>Select Session</xsl:text>
                    </option>
                    <xsl:apply-templates select="/doc/query[@queryName='availableSessions']/row">
                        <xsl:with-param name="selectedValue" select="@sess" />
                    </xsl:apply-templates>
                </select>
            </td>
        </tr>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='days']/row">
        <xsl:param name="selectedValue" />
        <option value="{@value}">
            <xsl:if test="@value = $selectedValue"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
            <xsl:value-of select="@name" />
        </option>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='hours']/row">
        <xsl:param name="selectedValue" />
        <option value="{@value}">
            <xsl:if test="@value = $selectedValue"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
            <xsl:value-of select="@display" />
        </option>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='minutes']/row">
        <xsl:param name="selectedValue" />
        <option value="{@value}">
            <xsl:if test="@value = $selectedValue"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
            <xsl:value-of select="@display" />
        </option>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='availableSessions']/row">
        <xsl:param name="selectedValue" />
        <option value="{@sessionid}">
            <xsl:if test="@sessionid = $selectedValue"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
            <xsl:choose>
                <xsl:when test="$trackTagUsage = 'TAG_ONLY'">
                    <xsl:value-of select="@typename" /><xsl:text> - </xsl:text><xsl:value-of select="@sessionid" /><xsl:text> - </xsl:text><xsl:value-of select="@title" />
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="@trackname" /><xsl:text> - </xsl:text><xsl:value-of select="@sessionid" /><xsl:text> - </xsl:text><xsl:value-of select="@title" />
                </xsl:otherwise>
            </xsl:choose>
        </option>
    </xsl:template>
</xsl:stylesheet>
