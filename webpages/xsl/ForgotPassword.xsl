<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-19;
     Copyright (c) 2020-2024 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="USER_ID_PROMPT" select="'Badge ID'" />
    <xsl:param name="error_message" select="''" />
    <xsl:param name="RECAPTCHA_SITE_KEY" select="''" />
    <xsl:template match="/">
        <div class="container-xxxl">
            <div class="row">
                <div class="col-12 mt-4">
                    <h3 class="mx-auto" style="width:12.5rem">Reset Password</h3>
                </div>
            </div>
            <xsl:if test="$error_message != ''">
                <div class="row">
                    <div class="col-12 mt-4">
                        <div class="alert alert-error" role="alert">
                            <xsl:value-of select="$error_message" />
                        </div>
                    </div>
                </div>
            </xsl:if>
            <form method="POST" action="ForgotPasswordSubmit.php">
                <div class="row">
                    <div class="col12 my-4">
                        <p>Enter your <xsl:value-of select="$USER_ID_PROMPT" /> and email address.  Then a link to initialize or reset your password will be emailed to you.</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-5">
                        <label for="badgeid" class="form-label">
                            <xsl:value-of select="$USER_ID_PROMPT" />:
                        </label>
                        <input name="badgeid" id="badgeid" class="form-control" />
                        <label for="emailAddress" class="form-label mt-4">Email address:</label>
                        <input name="emailAddress" id="emailAddress" class="form-control"/>
                    </div>
                    <div class="col-6 offset-1">
                        <div class="g-recaptcha" data-sitekey="{$RECAPTCHA_SITE_KEY}" data-callback="recaptchaCheckedCallback"></div>
                        <div id="recaptcha-error-message" class="alert alert-error hidden">This page requires access to www.google.com in order to load reCAPTCHA tool to prevent robots.  There is a problem accessing that domain.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-4">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </xsl:template>
</xsl:stylesheet>
