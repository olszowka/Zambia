<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-19;
     Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="USER_ID_PROMPT" select="'Badge ID'" />
    <xsl:param name="EMAIL_LOGIN_SUPPORT" select="0" />
    <xsl:param name="error_message" select="''" />
    <xsl:param name="RECAPTCHA_SITE_KEY" select="''" />
    <xsl:template match="/">
        <div class="container mt-3">
            <xsl:if test="$error_message">
                <p class="alert alert-danger">
                    <xsl:value-of select="$error_message" />
                </p>
            </xsl:if>
            <xsl:choose>
                <xsl:when test="$EMAIL_LOGIN_SUPPORT=0">
                    <div class="alert alert-primary">Enter your <xsl:value-of select="$USER_ID_PROMPT" /> and email address.  Then a link to initialize or reset your password will be emailed to you.</div>
                </xsl:when>
                <xsl:otherwise>
                    <div class="alert alert-primary">Enter your email address.  Then a link to initialize or reset your password will be emailed to you.</div>
                </xsl:otherwise>
            </xsl:choose>
            <div class="card">
                <div class="card-header">
                    <h2>Reset Password</h2>
                </div>
                <div class="card-body">
                    <form method="POST" action="ForgotPasswordSubmit.php">
                        <div class="row">
                            <div class="col-lg-6">
                                <xsl:if test="$EMAIL_LOGIN_SUPPORT=0">
                                <div class="form-group">
                                    <div class="controls">
                                        <input class="form-control" name="badgeid" id="badgeid">
                                            <xsl:attribute  name="placeholder"><xsl:value-of select="$USER_ID_PROMPT"/></xsl:attribute>
                                        </input>
                                    </div>
                                </div>
                                </xsl:if>
                                <div class="form-group">
                                    <div class="controls">
                                        <input class="form-control" name="emailAddress" id="emailAddress" placeholder="Email address" />
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
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
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>