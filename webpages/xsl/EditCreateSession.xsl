<?xml version="1.0" encoding="UTF-8" ?>
<!--
    EditCreateSession.xsl
    Created by Peter Olszowka on 2023-11-29.
    Copyright (c) 2023-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
    Page intended for BS4
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output encoding="UTF-8" indent="yes" method="xml"/>
    <xsl:param name="messageSuccess" />
    <xsl:param name="messageWarning" />
    <xsl:param name="messageFatal" />
    <xsl:param name="action" />
    <xsl:param name="name" />
    <xsl:param name="email" />
    <!-- The pubno field is no longer used on the form, but the code expects it.-->
    <xsl:param name="pubno" />
    <xsl:variable name="pocketprogtext" select="/doc/session/@pocketprogtext" /><!-- used for 2nd description -->
    <xsl:variable name="secondtitle" select="/doc/session/@secondtitle" />
    <xsl:param name="languagestatusid" />
    <xsl:param name="track_tag_usage" />
    <xsl:param name="sessionid" />
    <xsl:variable name="title" select="/doc/session/@title" />
    <xsl:param name="track" />
    <xsl:param name="divisionid" />
    <xsl:param name="type" />
    <xsl:param name="pubstatusid" />
    <xsl:param name="kids" />
    <xsl:param name="invguest" />
    <xsl:param name="atten" />
    <xsl:param name="duration" />
    <xsl:param name="roomset" />
    <xsl:param name="status" />
    <xsl:param name="signup" />
    <xsl:variable name="progguidhtml" select="/doc/session/@progguidhtml" />
    <xsl:variable name="progguiddesc" select="/doc/session/@progguiddesc" />
    <xsl:variable name="persppartinfo" select="/doc/session/@persppartinfo" />
    <xsl:variable name="notesforpart" select="/doc/session/@notesforpart" />
    <xsl:variable name="servnotes" select="/doc/session/@servnotes" />
    <xsl:variable name="notesforprog" select="/doc/session/@notesforprog" />
    <xsl:variable name="mlink" select="/doc/session/@mlink" />
    <xsl:variable name="plink" select="/doc/session/@plink" />
    <xsl:variable name="rlink" select="/doc/session/@rlink" />
    <xsl:variable name="clink" select="/doc/session/@clink" />
    <xsl:param name="showmeetinglink" />
    <xsl:param name="showparticipantlink" />
    <xsl:param name="showrecordinglink" />
    <xsl:param name="showcaptionlink" />
    <xsl:param name="bilingual" />
    <xsl:param name="secondtitlecaption" />
    <xsl:param name="seconddescriptioncaption" />

    <xsl:template match="/">
        <div class="container container-xl">
            <xsl:choose>
                <xsl:when test="$messageFatal">
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-danger" role="alert">
                                <xsl:value-of select="$messageFatal" />
                            </div>
                        </div>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:if test="$messageWarning">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-danger" role="alert">
                                    <xsl:value-of select="$messageWarning" disable-output-escaping="yes" />
                                </div>
                            </div>
                        </div>
                    </xsl:if>
                    <xsl:if test="$messageSuccess">
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="alert alert-success" role="alert">
                                    <xsl:value-of select="$messageSuccess" disable-output-escaping="yes" />
                                </div>
                            </div>
                        </div>
                    </xsl:if>
                    <form name="sessform" class="mb-4" method="POST" action="SubmitEditCreateSession.php">
                        <input type="hidden" name="action" value="{$action}" />
                        <input type="hidden" name="name" value="{$name}" />
                        <input type="hidden" name="email" value="{$email}" />
                        <!-- The pubno field is no longer used on the form, but the code expects it.-->
                        <input type="hidden" name="pubno" value="{$pubno}" />
                        <!-- The languagestatusid field is used only for bilingual support, but that field isn't currently implemented.-->
                        <input type="hidden" name="languagestatusid" value="{$languagestatusid}" />
                        <div class="row justify-content-end mt-3">
                            <div class="col-1">
                                <button type="reset" class="btn btn-secondary" id="reset1" name="reset1" value="reset">Reset</button>
                            </div>
                            <div class="col-1">
                                <button type="submit" class="btn btn-primary" id="save1" name="save1" value="save" onclick="mysubmit()">Save</button>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="form-group col-md-2">
                                <label for="sessionid">Session #</label>
                                <input type="text" class="form-control" name="sessionid" id="sessionid" disabled="disabled" readonly="readonly"
                                       value="{$sessionid}" style="width:50%" />
                            </div>
                            <div class="form-group col-md-5">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" name="title" id="title" value="{$title}" />
                            </div>
                            <xsl:choose>
                                <xsl:when test="$track_tag_usage != 'TAG_ONLY'">
                                    <div class="form-group col-md-3 offset-md-1">
                                        <label for="track">Track</label>
                                        <select name="track" id="track" class="custom-select">
                                            <xsl:if test="not($track) or $track='0'">
                                                <option value="0" selected="selected">SELECT</option>
                                            </xsl:if>
                                            <xsl:apply-templates select="/doc/query[@queryName='tracks']" />
                                        </select>
                                    </div>
                                </xsl:when>
                                <xsl:otherwise>
                                    <input type="hidden" name="track" id="track" value="{$track}" />
                                </xsl:otherwise>
                            </xsl:choose>
                        </div><!-- end of 1st group with "session #" -->
                        <div class="row mt-1">
                            <div class="form-group col-md-6">
                                <label for="progguidhtml">Formatted Description</label>
                                <textarea rows="4" cols="70" name="progguidhtml" id="progguidhtml">
                                    <xsl:value-of select="$progguidhtml" disable-output-escaping="yes" />
                                </textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label for="progguiddesc">Plain Text Description</label>
                                        <textarea rows="4" cols="70" name="progguiddesc" id="progguiddesc" class="form-control">
                                            <xsl:value-of select="$progguiddesc" />
                                        </textarea>
                                    </div>
                                </div>
                                <div class="row mt-1">
                                    <div class="form-group col-md-8">
                                        <xsl:choose>
                                            <xsl:when test="$track_tag_usage = 'TRACK_ONLY'">
                                                <label for="notesforprog">Notes for Program Committee</label>
                                                <textarea rows="5" cols="70" name="notesforprog" id="notesforprog" class="form-control tall-textarea">
                                                    <xsl:value-of select="$notesforprog" />
                                                </textarea>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <span class="label" id="tags-container-label">Tags</span>
                                                <div class="checkbox-list-container form-control" id="features-container" role="group" aria-labelledby="tags-container-label">
                                                    <xsl:apply-templates select="/doc/query[@queryName='tags']" />
                                                </div>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end of 2nd group with "Formatted Description" -->
                        <xsl:choose>
                            <xsl:when test="$bilingual">
                                <div class="row mt-1">
                                    <div class="form-group col-md-5">
                                        <label for="secondtitle"><xsl:value-of select="$secondtitlecaption" /></label>
                                        <input type="text" class="form-control" name="secondtitle" id="secondtitle" value="{$secondtitle}" />
                                    </div>
                                    <div class="form-group col-md-6 offset-md-1">
                                        <label for="pocketprogtext"><xsl:value-of select="$seconddescriptioncaption" /></label>
                                        <textarea rows="4" cols="70" name="pocketprogtext" id="pocketprogtext" class="form-control">
                                            <xsl:value-of select="$pocketprogtext" />
                                        </textarea>
                                    </div>
                                </div><!-- end of 3rd (optional) group with "2nd Title" -->
                            </xsl:when>
                            <xsl:otherwise>
                                <input type="hidden" name="pocketprogtext" value="{$pocketprogtext}" />
                                <input type="hidden" name="secondtitle" value="{$secondtitle}" />
                            </xsl:otherwise>
                        </xsl:choose>
                        <div class="row mt-1">
                            <xsl:choose>
                                <xsl:when test="count(/doc/query[@queryName='divisions']/row) > 1">
                                    <div class="form-group col-md-2p01 special-col-adjustment">
                                        <label for="divisionid">Division</label>
                                        <select name="divisionid" id="divisionid" class="custom-select">
                                            <xsl:if test="not($divisionid) or $divisionid = '0'">
                                                <option value="0" selected="selected">SELECT</option>
                                            </xsl:if>
                                            <xsl:apply-templates select="/doc/query[@queryName='divisions']" />
                                        </select>
                                    </div>
                                </xsl:when>
                                <xsl:otherwise>
                                    <input type="hidden" name="divisionid" id="divisionid" value="{$divisionid}" />
                                </xsl:otherwise>
                            </xsl:choose>
                            <div class="form-group col-md-2p01 special-col-adjustment">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="custom-select">
                                    <xsl:if test="not($type) or $type = '0'">
                                        <option value="0" selected="selected">SELECT</option>
                                    </xsl:if>
                                    <xsl:apply-templates select="/doc/query[@queryName='types']" />
                                </select>
                            </div>
                            <div class="form-group col-md-2p01 special-col-adjustment">
                                <label for="pubstatusid">Publication Status</label>
                                <select name="pubstatusid" id="pubstatusid" class="custom-select">
                                    <xsl:if test="not($pubstatusid) or $pubstatusid = '0'">
                                        <option value="0" selected="selected">SELECT</option>
                                    </xsl:if>
                                    <xsl:apply-templates select="/doc/query[@queryName='pubstatuses']" />
                                </select>
                            </div>
                            <xsl:choose>
                                <xsl:when test="count(/doc/query[@queryName='kidscategories']/row) > 1">
                                    <div class="form-group col-md-2p01 special-col-adjustment">
                                        <label for="kids">Adult/Children Category</label>
                                        <select name="kids" id="kids" class="custom-select">
                                            <xsl:if test="not($kids) or $kids = '0'">
                                                <option value="0" selected="selected">SELECT</option>
                                            </xsl:if>
                                            <xsl:apply-templates select="/doc/query[@queryName='kidscategories']" />
                                        </select>
                                    </div>
                                </xsl:when>
                                <xsl:otherwise>
                                    <input type="hidden" name="kids" id="kids" value="{$kids}" />
                                </xsl:otherwise>
                            </xsl:choose>
                            <div class="form-group col-md-2p01 special-col-adjustment">
                                <label for="atten">Estimated Attendance</label>
                                <input type="text" class="form-control" name="atten" id="atten" value="{$atten}" style="width:50%" />
                            </div>
                            <div class="form-group col-md-2p01 special-col-adjustment">
                                <label for="duration">Duration</label>
                                <input type="text" class="form-control" name="duration" id="duration" value="{$duration}" style="width:50%" />
                            </div>
                            <xsl:choose>
                                <xsl:when test="count(/doc/query[@queryName='roomsets']/row) > 1">
                                    <div class="form-group col-md-2p01 special-col-adjustment">
                                        <label for="roomset">Room Set</label>
                                        <select name="roomset" id="roomset" class="custom-select">
                                            <xsl:if test="not($roomset) or $roomset = '0'">
                                                <option value="0" selected="selected">SELECT</option>
                                            </xsl:if>
                                            <xsl:apply-templates select="/doc/query[@queryName='roomsets']" />
                                        </select>
                                    </div>
                                </xsl:when>
                                <xsl:otherwise>
                                    <input type="hidden" name="roomset" id="roomset" value="{$roomset}" />
                                </xsl:otherwise>
                            </xsl:choose>
                            <div class="form-group col-md-2p01 special-col-adjustment">
                                <label for="status">Workflow Status</label>
                                <select name="status" id="status" class="custom-select">
                                    <xsl:if test="not($status) or $status = '0'">
                                        <option value="0" selected="selected">SELECT</option>
                                    </xsl:if>
                                    <xsl:apply-templates select="/doc/query[@queryName='sessionstatuses']" />
                                </select>
                            </div>
                            <div class="form-group col-md-2p01 special-col-adjustment">
                                <label for="invguest">Requires <em>Attendee</em> Signup</label>
                                <input type="checkbox" class="form-check" name="signup" id="signup" value="signup"
                                       style="transform:scale(1.6);transform-origin:0 -50%;">
                                    <xsl:if test="$signup = '1'">
                                        <xsl:attribute name="checked">checked</xsl:attribute>
                                    </xsl:if>
                                </input>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="invguest">Prevent participants from self nominating</label>
                                <input type="checkbox" class="form-check" name="invguest" id="invguest" value="invguest"
                                       style="transform:scale(1.6);transform-origin:0 -50%;">
                                    <xsl:if test="$invguest = '1'">
                                        <xsl:attribute name="checked">checked</xsl:attribute>
                                    </xsl:if>
                                </input>
                            </div>
                        </div><!-- end of 3rd(4th) group starting with "Division" -->
                        <div class="row mt-1">
                            <xsl:if test="not($track_tag_usage = 'TRACK_ONLY')">
                                <div class="form-group col-md-4">
                                    <label for="notesforprog">Notes for Program Committee</label>
                                    <textarea rows="5" cols="70" name="notesforprog" id="notesforprog" class="form-control tall-textarea">
                                        <xsl:value-of select="$notesforprog" />
                                    </textarea>
                                </div>
                            </xsl:if>
                            <xsl:if test="count(/doc/query[@queryName='features']/row) > 0">
                                <div class="form-group col-md-4">
                                    <span class="label" id="features-container-label">Required Room Features</span>
                                    <div class="checkbox-list-container form-control" id="features-container" role="group" aria-labelledby="features-container-label">
                                        <xsl:apply-templates select="/doc/query[@queryName='features']" />
                                    </div>
                                </div>
                            </xsl:if>
                            <xsl:if test="count(/doc/query[@queryName='services']/row) > 0">
                                <div class="form-group col-md-4">
                                    <span class="label" id="services-container-label">Required Room Services</span>
                                    <div class="checkbox-list-container form-control" id="features-container" role="group" aria-labelledby="services-container-label">
                                        <xsl:apply-templates select="/doc/query[@queryName='services']" />
                                    </div>
                                </div>
                            </xsl:if>
                            <div class="form-group col-md-4">
                                <label for="persppartinfo">Prospective Participant Info</label>
                                <textarea rows="4" cols="70" name="persppartinfo" id="persppartinfo" class="form-control tall-textarea">
                                    <xsl:value-of select="$persppartinfo" />
                                </textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="notesforpart">Notes for Participants</label>
                                <textarea rows="5" cols="70" name="notesforpart" id="notesforpart" class="form-control">
                                    <xsl:value-of select="$notesforpart" />
                                </textarea>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="servnotes">Notes for Tech or Hotel</label>
                                <textarea rows="5" cols="70" name="servnotes" id="servnotes" class="form-control">
                                    <xsl:value-of select="$servnotes" />
                                </textarea>
                            </div>
                        </div><!-- end of 4th(5th) group with "Required Room Features" -->
                        <div class="row mt-1">
                            <xsl:if test="$showmeetinglink">
                                <div class="form-group col-md-6">
                                    <label for="mlink">Meeting Link</label>
                                    <input type="text" class="form-control" name="mlink" id="mlink" value="{$mlink}" maxlength="510" />
                                </div>
                            </xsl:if>
                            <xsl:if test="$showparticipantlink">
                                <div class="form-group col-md-6">
                                    <label for="plink">Participant Link</label>
                                    <input type="text" class="form-control" name="plink" id="plink" value="{$plink}" maxlength="510" />
                                </div>
                            </xsl:if>
                            <xsl:if test="$showrecordinglink">
                                <div class="form-group col-md-6">
                                    <label for="rlink">Recording Link</label>
                                    <input type="text" class="form-control" name="rlink" id="rlink" value="{$rlink}" maxlength="510" />
                                </div>
                            </xsl:if>
                            <xsl:if test="$showcaptionlink">
                                <div class="form-group col-md-6">
                                    <label for="clink">Caption Link</label>
                                    <input type="text" class="form-control" name="clink" id="clink" value="{$clink}" maxlength="510" />
                                </div>
                            </xsl:if>
                        </div><!-- end of 5th(6th) group with "Meeting Link" -->
                        <div class="row justify-content-end mt-3">
                            <div class="col-1">
                                <button type="reset" class="btn btn-secondary" id="reset2" name="reset2" value="reset">Reset</button>
                            </div>
                            <div class="col-1">
                                <button type="submit" class="btn btn-primary" id="save2" name="save2" value="save" onclick="mysubmit()">Save</button>
                            </div>
                        </div>
                    </form>
                </xsl:otherwise>
            </xsl:choose>
        </div>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='divisions']/row">
        <option value="{@divisionid}">
            <xsl:if test="@divisionid = $divisionid">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@divisionname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='tracks']/row">
        <option value="{@trackid}">
            <xsl:if test="@trackid = $track">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@trackname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='types']/row">
        <option value="{@typeid}">
            <xsl:if test="@typeid = $type">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@typename" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='pubstatuses']/row">
        <option value="{@pubstatusid}">
            <xsl:if test="@pubstatusid = $pubstatusid">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@pubstatusname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='kidscategories']/row">
        <option value="{@kidscatid}">
            <xsl:if test="@kidscatid = $kids">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@kidscatname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='roomsets']/row">
        <option value="{@roomsetid}">
            <xsl:if test="@roomsetid = $roomset">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@roomsetname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='sessionstatuses']/row">
        <option value="{@statusid}">
            <xsl:if test="@statusid = $status">
                <xsl:attribute name="selected">selected</xsl:attribute>
            </xsl:if>
            <xsl:value-of select="@statusname" />
        </option>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='features']/row">
        <div class="checkbox-list-label-wrapper">
            <label class="checkbox-list-label">
                <input type="checkbox" name="featdest[]" id="feature_{@featureid}" class="checkbox-list-check mycontrol feature-check" value="{@featureid}">
                    <xsl:if test="@selected = '1'">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <xsl:value-of select="@featurename" />
            </label>
        </div>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='services']/row">
        <div class="checkbox-list-label-wrapper">
            <label class="checkbox-list-label">
                <input type="checkbox" name="servdest[]" id="service_{@serviceid}" class="checkbox-list-check mycontrol service-check" value="{@serviceid}">
                    <xsl:if test="@selected = '1'">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <xsl:value-of select="@servicename" />
            </label>
        </div>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='tags']/row">
        <div class="checkbox-list-label-wrapper">
            <label class="checkbox-list-label">
                <input type="checkbox" name="tagdest[]" id="tag_{@tagid}" class="checkbox-list-check mycontrol tag-check" value="{@tagid}">
                    <xsl:if test="@selected = '1'">
                        <xsl:attribute name="checked">checked</xsl:attribute>
                    </xsl:if>
                </input>
                <xsl:value-of select="@tagname" />
            </label>
        </div>
    </xsl:template>

</xsl:stylesheet>
