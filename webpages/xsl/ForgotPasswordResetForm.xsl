<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-21;
     Copyright (c) 2020-2024 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="control" select="''" />
    <xsl:param name="controliv" select="''" />
    <xsl:param name="user_name" select="''" />
    <xsl:param name="badgeid" select="''" />
    <xsl:param name="error_message" select="''" />
    <xsl:template match="/">
        <div class="container-xxxl">
            <xsl:if test="$error_message">
                <div class="row">
                    <div class="col-12 mt-4">
                        <div class="alert alert-danger"><xsl:value-of select="$error_message" /></div>
                    </div>
                </div>
            </xsl:if>
            <form method="POST" action="ForgotPasswordResetSubmit.php">
                <input type="hidden" id="control" name="control" value="{$control}" />
                <input type="hidden" id="controliv" name="controliv" value="{$controliv}" />
                <div class="row">
                    <div class="col-12 mt-4">
                        <span>Enter new password for <xsl:value-of select="$user_name" />, badgeid: <xsl:value-of select="$badgeid" /></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-3 col-lg-4 col-md-6 mt-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" />
                            <span class="input-group-text px-0 py-0">
                                <button type="button" id="revealPassword" class="btn btn-outline-light px-0 py-0"><img src="images/eye.svg" width="30"/></button>
                            </span>
                            <div id="passwordfeedback" class="invalid-feedback">Passwords must be 6 characters or longer.</div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-3 col-lg-4 col-md-6 mt-4">
                        <label for="cpassword" class="form-label">Confirm password</label>
                        <input type="password" name="cpassword" id="cpassword" class="form-control" />
                        <div id="cpasswordfeedback" class="invalid-feedback">Passwords do not match.</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-xl-3 col-lg-4 col-md-6 mt-4">
                        <button type="submit" id="submit-button" class="btn btn-primary">Submit</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mt-4">
                        <p>After changing your password, you will be taken to the login page.</p>
                    </div>
                </div>
            </form>
        </div>
    </xsl:template>
</xsl:stylesheet>
