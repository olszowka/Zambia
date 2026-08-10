<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2026-08-10;
    Copyright (c) 2026 Peter Olszowka. All rights reserved.
    See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="readonly" select="'0'"/>
    <xsl:param name="error" select="'0'"/>
    <xsl:param name="message" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <div class="container-xl mt-2">
            <xsl:if test="$error = '1'">
                <p class="alert alert-danger"><xsl:value-of select="$message" disable-output-escaping="yes" /></p>
            </xsl:if>
            <xsl:if test="$error = '0' and $message != ''">
                <p class="alert alert-success"><xsl:value-of select="$message" disable-output-escaping="yes" /></p>
            </xsl:if>
            <xsl:if test="$readonly = '1'">
                <p class="alert alert-info">We're sorry, but we are unable to accept your suggestions at this time.</p>
            </xsl:if>
            <div class="my-suggestions-wrapper">
                <form name="addform" method="post" action="SubmitMySuggestions.php">
                    <div class="titledtextarea form-group mt-4">
                        <label for="paneltopics">
                            <xsl:choose>
                                <xsl:when test="string-length(doc/customText/@paneltopics_label) > 0">
                                    <xsl:value-of select="doc/customText/@paneltopics_label" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>Program Topic Ideas:</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </label>
                        <textarea class="form-control mt-2" id="paneltopics" name="paneltopics" rows="6" cols="72">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                                <xsl:attribute name="class">form-control mt-2 readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='mysuggestions']/row/@paneltopics" />
                        </textarea>
                    </div>
                    <div class="titledtextarea form-group mt-4">
                        <label for="otherideas">
                            <xsl:choose>
                                <xsl:when test="string-length(doc/customText/@otherideas_label) > 0">
                                    <xsl:value-of select="doc/customText/@otherideas_label" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>Other Programming Ideas:</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </label>
                        <textarea class="form-control mt-2" id="otherideas" name="otherideas" rows="6" cols="72">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                                <xsl:attribute name="class">form-control mt-2 readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='mysuggestions']/row/@otherideas" />
                        </textarea>
                    </div>
                    <div class="titledtextarea form-group mt-4">
                        <label for="suggestedguests">
                            <xsl:choose>
                                <xsl:when test="string-length(doc/customText/@suggestedguests_label) > 0">
                                    <xsl:value-of select="doc/customText/@suggestedguests_label" disable-output-escaping="yes" />
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:text>Suggested Guests (please provide addresses and other contact information if possible):</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </label>
                        <textarea class="form-control mt-2" id="suggestedguests" name="suggestedguests" rows="8" cols="72">
                            <xsl:if test="$readonly = '1'">
                                <xsl:attribute name="readonly">readonly</xsl:attribute>
                                <xsl:attribute name="class">form-control mt-2 readonly</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="doc/query[@queryName='mysuggestions']/row/@suggestedguests" />
                        </textarea>
                    </div>
                    <xsl:if test="$readonly = '0'">
                        <div id="submit" class="mt-4">
                            <button class="SubmitButton btn btn-primary" type="submit" name="submit">Save</button>
                        </div>
                    </xsl:if>
                </form>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
