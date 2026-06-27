<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2024-11-16;
    Copyright (c) 2024-2026 Peter Olszowka. All rights reserved.
    See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="readonly" select="'0'"/>
    <xsl:param name="db_updated" select="'0'"/>
    <xsl:param name="EditParticipantName" select="''"/>
    <xsl:param name="EditBadgeId" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <div class="container-xxxl">
            <form name="addform" method="POST" action="my_interests.php" class="mt-2">
                <xsl:if test="$readonly = '1'">
                    <div class="row">
                        <div class="col-12 mt-3">
                            <div class="alert alert-warning" role="alert">
                                We're sorry, but we are unable to accept your suggestions at this time.
                            </div>
                        </div>
                    </div>
                </xsl:if>
                <xsl:if test="$db_updated = '1'">
                    <div class="row">
                        <div class="col-12 mt-3">
                            <div class="alert alert-success" role="alert">
                                Database updated successfully.
                            </div>
                        </div>
                    </div>
                </xsl:if>
                <xsl:if test="$EditBadgeId != ''">
                    <div class="row">
                        <div class="col-12 mt-3">
                            <div class="alert alert-primary" role="alert">
                                <xsl:text>Editing participant </xsl:text>
                                <xsl:value-of select="$EditParticipantName" />
                                <input type="hidden" id="edit_badgeid" name="edit_badgeid" value="{$EditBadgeId}" />
                            </div>
                        </div>
                    </div>
                </xsl:if>
                <div class="row">
                    <div class="col-12 col-md-6 mt-4">
                        <label for="yespanels" class="form-label">
                            <xsl:choose>
                                <xsl:when test="string-length(doc/customText/@stuff_id_like_to_run) > 0">
                                    <xsl:value-of select="doc/customText/@stuff_id_like_to_run" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>Workshops or presentations I'd like to run:</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </label>
                        <textarea id="yespanels" name="yespanels" rows="5" cols="72" class="form-control">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='participantinterests']/row/@yespanels" />
                        </textarea>
                    </div>
                    <div class="col-12 col-md-6 mt-4">
                        <label for="nopanels" class="form-label">
                            <xsl:choose>
                                <xsl:when test="string-length(/doc/customText/@panel_types_not_int) > 0">
                                    <xsl:value-of select="/doc/customText/@panel_types_not_int" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>Panel types I am not interested in participating in:</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </label>
                        <textarea name="nopanels" rows="5" cols="72" class="form-control">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='participantinterests']/row/@nopanels" />
                        </textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6 mt-4">
                        <label for="yespeople" class="form-label">
                            <xsl:choose>
                                <xsl:when test="string-length(/doc/customText/@people_want_on_sess_label) > 0">
                                    <xsl:value-of select="/doc/customText/@people_want_on_sess_label" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>People with whom I'd like to be on a session: (Leave blank for none)</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </label>
                        <textarea name="yespeople" rows="5" cols="72" class="form-control">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='participantinterests']/row/@yespeople" />
                        </textarea>
                    </div>
                    <div class="col-12 col-md-6 mt-4">
                        <label for="nopeople" class="form-label">
                            <xsl:choose>
                                <xsl:when test="string-length(/doc/customText/@people_dont_want_label) > 0">
                                    <xsl:value-of select="/doc/customText/@people_dont_want_label" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>People with whom I'd rather not be on a session: (Leave blank for none)</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </label>
                        <textarea name="nopeople" rows="5" cols="72" class="form-control">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='participantinterests']/row/@nopeople" />
                        </textarea>
                    </div>
                </div>
                <fieldset>
                    <div class="row mt-4">
                        <div class="col-12">
                            <legend>
                                <xsl:choose>
                                    <xsl:when test="string-length(doc/customText/@roles_checkboxes_label) > 0">
                                        <xsl:value-of select="doc/customText/@roles_checkboxes_label" disable-output-escaping="yes" />
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:text>Roles I'm willing to take on:</xsl:text>
                                    </xsl:otherwise>
                                </xsl:choose>
                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                            </legend>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 roles-list-container">
                            <xsl:apply-templates select="doc/query[@queryName='participantroles']/row[@roleid > 1]" />
                            <xsl:apply-templates select="doc/query[@queryName='participantroles']/row[@roleid = 1]" />
                        </div>
                    </div>
                </fieldset>
                <div class="row">
                    <div class="col-12 col-md-6 mt-4">
                        <label for="otherroles" class="form-label">
                            <xsl:choose>
                                <xsl:when test="string-length(/doc/customText/@other_role_desc) > 0">
                                    <xsl:value-of select="/doc/customText/@other_role_desc" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>Description for "Other" Roles:</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        </label>
                        <textarea name="otherroles" rows="5" cols="72" class="form-control">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='participantinterests']/row/@otherroles" />
                        </textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4 col-md-2 mt-4">
                        <button class="btn btn-primary" type="submit" name="submit">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </xsl:template>
    <xsl:template match="doc/query[@queryName='participantroles']/row">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="willdorole{@roleid}" name="willdorole{@roleid}">
                <xsl:if test="@badgeid">
                    <xsl:attribute name="checked">checked</xsl:attribute>
                </xsl:if>
                <xsl:if test="$readonly = '1'">
                    <xsl:attribute name="disabled">disabled</xsl:attribute>
                </xsl:if>
            </input>
            <label class="form-check-label" for="willdorole{@roleid}">
                <xsl:value-of select="@rolename" />
            </label>
        </div>
    </xsl:template>
</xsl:stylesheet>
