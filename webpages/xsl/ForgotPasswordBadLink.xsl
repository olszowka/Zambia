<?xml version='1.0' encoding="UTF-8"?>
<!-- Created by Peter Olszowka on 2020-04-21;
     Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">
        <h3 class="alert alert-error vert-sep-above">Unable to reset password.</h3>
        <p class="vert-sep-above">This problem may be due to one of the following reasons:</p>
        <ul class="vert-sep-above">
            <li>The password reset link has expired.</li>
            <li>The password reset link has been used already.</li>
            <li>A new password reset link has been requested for this user.</li>
            <li>The password reset link was incomplete or modified.</li>
        </ul>
        <p class="vert-sep-above">
            <a href="login.php" class="btn btn-primary">Return to Login Page</a>
        </p>
    </xsl:template>
</xsl:stylesheet>