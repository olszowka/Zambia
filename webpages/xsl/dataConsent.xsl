<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2026-06-05;
    Copyright (c) 2026 Peter Olszowka. All rights reserved.
    See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="conName" select="''"/>
    <xsl:param name="firstName" select="''"/>
    <xsl:param name="lastName" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <xsl:variable name="consent" select="/doc/customText/@consent" />
        <div class="container">
            <div class="mt-2">
                <h3 class="mb-2">
                    <xsl:text>Consent for collection and usage of your data entered into Zambia for </xsl:text>
                    <xsl:value-of select="$conName"/>
                </h3>
                <xsl:choose>
                    <xsl:when test="string-length($consent) > 0">
                        <xsl:value-of select="$consent" disable-output-escaping="yes"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <p>
                            <xsl:text>We collect your personal data to allow us to schedule you onto programming items, to publish data about your participation in programming items, and to administer </xsl:text>
                            <xsl:value-of select="$conName"/>
                            <xsl:text>. We retain this data for the duration of this convention and to assist in planning future conventions.</xsl:text>
                        </p>
                        <p>
                            <xsl:text>We do not share personal data with other conventions or organizations. Public data, such as your biography, photo, and socials, will be published in our printed program guide and online schedule.</xsl:text>
                        </p>
                        <p>
                            <xsl:text>Without your consent to collect and use your data in this way, we are unable to have you as a program participant for </xsl:text>
                            <xsl:value-of select="$conName"/>
                            <xsl:text>.</xsl:text>
                        </p>
                    </xsl:otherwise>
                </xsl:choose>
                <form class="form-inline" name="consentform" method="POST" action="SubmitConsent.php">
                    <div id="update_section" class="form-group pr-2">
                        <label for="consent">
                            <xsl:text>I, </xsl:text>
                            <xsl:value-of select="$firstName"/>
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="$lastName"/>
                            <xsl:text>, grant consent for data collection of my personal data: </xsl:text>
                        </label>
                    </div>
                    <div class="form-group pr-4">
                        <select id="consent" name="consent">
                            <option value="0" selected="selected">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="submit" name="submit" >Update</button>
                    </div>
                </form>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
