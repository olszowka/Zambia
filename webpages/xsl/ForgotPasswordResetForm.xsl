<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-21;
     Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="selector" select="''" />
    <xsl:param name="validator" select="''" />
    <xsl:param name="badgeid" select="''" />
    <xsl:param name="error_message" select="''" />
    <xsl:template match="/">
        <div class="row-fluid vert-sep-above">
            <xsl:if test="$error_message">
                <p class="alert alert-error"><xsl:value-of select="$error_message" /></p>
            </xsl:if>
            <form method="POST" action="ForgotPasswordResetSubmit.php" class="well form-horizontal">
                <input type="hidden" id="selector" name="selector" value="{$selector}" />
                <input type="hidden" id="validator" name="validator" value="{$validator}" />
                <input type="hidden" id="badgeid" name="badgeid" value="{$badgeid}" />
                <div id="password-instructions" class="vert-sep">
                    <span>Provide your new password.</span>
                    <span id="passwords-dont-match" class="text-error hidden">Passwords do not match.</span>
                    <span id="passwords-too-short" class="text-error hidden">Passwords must be 6 characters or longer.</span>
                </div>
                <div class="control-group control-group-input">
                    <label for="password" class="control-label">Password</label>
                    <div class="controls">
                        <input type="password" name="password" id="password" />
                        <button type="button" id="revealPassword"><img src="images/eye.svg" /></button>
                    </div>
                </div>
                <div class="control-group control-group-input">
                    <label for="cpassword" class="control-label">Confirm password</label>
                    <div class="controls">
                        <input type="password" name="cpassword" id="cpassword" />
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