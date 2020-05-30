<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-19;
     Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="USER_ID_PROMPT" select="'Badge ID'" />
    <xsl:param name="error_message" select="''" />
    <xsl:param name="RECAPTCHA_SITE_KEY" select="''" />
    <xsl:template match="/">
        <xsl:if test="$error_message">
            <p class="alert alert-error vert-sep vert-sep-above">
                <xsl:value-of select="$error_message" />
            </p>
        </xsl:if>
        <div class="row-fluid vert-sep-above">
            <form method="POST" action="ForgotPasswordSubmit.php" class="well form-horizontal">
                <div class="vert-sep">Enter your <xsl:value-of select="$USER_ID_PROMPT" /> and email address.  Then a link to initialize or reset your password will be emailed to you.</div>
                <div class="row-fluid">
                    <div class="span6">
                        <div class="control-group">
                            <label for="badgeid" class="control-label">
                                <xsl:value-of select="$USER_ID_PROMPT" />
                                <xsl:text>:</xsl:text>
                            </label>
                            <div class="controls">
                                <input name="badgeid" id="badgeid" />
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="emailAddress" class="control-label">Email address:</label>
                            <div class="controls">
                                <input name="emailAddress" id="emailAddress" />
                            </div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="g-recaptcha" data-sitekey="{$RECAPTCHA_SITE_KEY}" data-callback="recaptchaCheckedCallback"></div>
                        <div id="recaptcha-error-message" class="alert alert-error hidden">This page requires access to www.google.com in order to load reCAPTCHA tool to prevent robots.  There is a problem accessing that domain.</div>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </xsl:template>
</xsl:stylesheet>