<?xml version='1.0' encoding="UTF-8"?>
<!-- File created by Peter Olszowka February 24, 2024
     Copyright (c) 2024 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <!-- "Staff" or "Participant" --><xsl:param name="header_version" select="'Participant'"/>
    <!-- "LOGIN", "SESSION_EXPIRED", "LOGOUT", "PASSWORD_RESET_COMPLETE", "NO_USER", "NORMAL" --><xsl:param name="top_section_behavior" select="'NORMAL'" />
    <xsl:param name="CON_NAME" select="''" />
    <xsl:param name="badgename" select="''" />
    <xsl:param name="USER_ID_PROMPT" select="'Badge ID'" />
    <xsl:param name="header_error_message" select="''" />
    <!-- TRUE/FALSE --><xsl:param name="RESET_PASSWORD_SELF" select="true()" />
    <xsl:template match="/">
        <header class="row">
            <div id="reg-header-container" class="collapsible-wrapper col px-0">
                <div id="reg-header">
                    <xsl:choose>
                        <xsl:when test="$header_version='Participant'">
                            <xsl:attribute name="class">header participant-header collapsible</xsl:attribute>
                        </xsl:when>
                        <xsl:when test="$header_version='Staff'">
                            <xsl:attribute name="class">header staff-header collapsible</xsl:attribute>
                        </xsl:when>
                    </xsl:choose>
                    <div class="col-md-auto px-0">
                        <img src="images/Z_illuminated.jpg" alt="Zambia &quot;Z&quot; logo" class="d-none d-lg-block" style="width: 175px"/>
                    </div>
                    <div class="col">
                        <h1 class="d-none d-md-block px-2">
                            <xsl:text>Zambia</xsl:text>
                            <span class="d-none d-lg-inline">
                                <xsl:text>: The </xsl:text>
                                <xsl:value-of select="$CON_NAME" />
                                <xsl:text> Scheduling Tool</xsl:text>
                            </span>
                        </h1>
                    </div>
                    <xsl:if test="$top_section_behavior != 'NO_USER'">
                        <div class="col col-lg-4 col-xl-3 mt-4 pe-4">
                            <xsl:choose>
                                <xsl:when test="$top_section_behavior = 'NORMAL'">
                                    <div id="welcome">
                                        <p>
                                            <xsl:text>Welcome, </xsl:text>
                                            <xsl:value-of select="$badgename" />
                                        </p>
                                        <img id="hide-header-but" class="img-button float-end" aria-role="button"
                                             alt="Shrink header to a thin strip" title="Shrink header to a thin strip" >
                                            <xsl:choose>
                                                <xsl:when test="$header_version='Participant'">
                                                    <xsl:attribute name="src">images/blue-up.png</xsl:attribute>
                                                </xsl:when>
                                                <xsl:when test="$header_version='Staff'">
                                                    <xsl:attribute name="src">images/green-up.png</xsl:attribute>
                                                </xsl:when>
                                            </xsl:choose>
                                        </img>
                                        <a href="logout.php" class="btn btn-primary float-end mr-4" title="Click to log out">Log out</a>
                                    </div>
                                </xsl:when>
                                <xsl:when test="$top_section_behavior = 'LOGIN' or $top_section_behavior = 'SESSION_EXPIRED'
                                    or $top_section_behavior = 'LOGOUT' or $top_section_behavior = 'PASSWORD_RESET_COMPLETE'">
                                    <form id="login-form" name="loginform" method="post" action="doLogin.php">
                                        <fieldset id="login-box">
                                            <xsl:if test="$header_error_message = '' and not($top_section_behavior = 'SESSION_EXPIRED' and $top_section_behavior = 'LOGOUT' and $top_section_behavior = 'PASSWORD_RESET_COMPLETE')">
                                                <xsl:attribute name="class">extended</xsl:attribute>
                                            </xsl:if>
                                            <xsl:if test="not($header_error_message = '')">
                                                <div class="row mb-3">
                                                    <span class="alert alert-danger py-1"><xsl:value-of select="$header_error_message" /></span>
                                                </div>
                                            </xsl:if>
                                            <xsl:choose>
                                                <xsl:when test="$top_section_behavior = 'SESSION_EXPIRED'">
                                                    <div class="row mb-3">
                                                        <span class="alert alert-danger py-1">Session expired. Please log in again.</span>
                                                    </div>
                                                </xsl:when>
                                                <xsl:when test="$top_section_behavior = 'LOGOUT'">
                                                    <div class="row mb-3">
                                                        <span class="alert alert-success py-1">You have logged out successfully.</span>
                                                    </div>
                                                </xsl:when>
                                                <xsl:when test="$top_section_behavior = 'PASSWORD_RESET_COMPLETE'">
                                                    <div class="row mb-3">
                                                        <span class="alert alert-success py-1">You have changed your password successfully.</span>
                                                    </div>
                                                </xsl:when>
                                            </xsl:choose>
                                            <div class="row mb-3">
                                                <label for="badgeid" class="col-5 col-form-label">
                                                    <xsl:value-of select="$USER_ID_PROMPT"/>
                                                    <xsl:text>:</xsl:text>
                                                </label>
                                                <div class="col-7">
                                                    <input type="text" name="badgeid" id="badgeid" class="form-control"
                                                        placeholder="{$USER_ID_PROMPT}" title="Enter your {$USER_ID_PROMPT}" />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <label for="passwd" class="col-5 col-form-label">Password</label>
                                                <div class="col-7">
                                                    <input type="password" id="passwd" name="passwd" class="form-control"
                                                        placeholder="Password" title="Enter your password" />
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-7 offset-5">
                                                    <input type="submit" value="Login" class="btn btn-primary" title="Click to log in" />
                                                </div>
                                            </div>
                                            <xsl:if test="$RESET_PASSWORD_SELF">
                                                <div class="row mb-3">
                                                    <div class="col-12">
                                                        <div class="mx-auto" style="width:16rem">
                                                            <a href="ForgotPassword.php">New user or forgot your password</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </xsl:if>
                                        </fieldset>
                                    </form>
                                </xsl:when>
                            </xsl:choose>
                        </div>
                    </xsl:if><!-- End of 3rd column with user info -->
                </div><!-- End of reg-header -->
            </div><!-- End of reg-header-container -->
            <xsl:if test="$top_section_behavior = 'NORMAL'">
                <div id="alt-header-container" class="collapsible-wrapper hidden">
                    <div id="alt-header" class="collapsible">
                        <div>
                            <xsl:choose>
                                <xsl:when test="$header_version='Participant'">
                                    <xsl:attribute name="class">alt-header-contents participant-header</xsl:attribute>
                                </xsl:when>
                                <xsl:when test="$header_version='Staff'">
                                    <xsl:attribute name="class">alt-header-contents staff-header</xsl:attribute>
                                </xsl:when>
                            </xsl:choose>
                            <div class="alt-header-spacer d-none d-xl-block">
                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                            </div>
                            <div class="alt-header-title">
                                <xsl:text>Zambia</xsl:text>
                                <span class="d-none d-lg-inline">
                                    <xsl:text>: The </xsl:text>
                                    <xsl:value-of select="$CON_NAME" />
                                    <xsl:text> Scheduling Tool</xsl:text>
                                </span>
                            </div>
                            <div class="alt-header-controls">
                                <p>
                                    <xsl:text>Welcome, </xsl:text>
                                    <xsl:value-of select="$badgename" />
                                </p>
                                <a class="btn btn-primary btn-sm" href="logout.php" title="Click to log out">Log out</a>
                                <img id="show-header-but" alt="Expand header to normal size" title="Expand header to normal size"
                                    aria-role="button">
                                    <xsl:choose>
                                        <xsl:when test="$header_version='Participant'">
                                            <xsl:attribute name="src">images/blue-down.png</xsl:attribute>
                                        </xsl:when>
                                        <xsl:when test="$header_version='Staff'">
                                            <xsl:attribute name="src">images/green-down.png</xsl:attribute>
                                        </xsl:when>
                                    </xsl:choose>
                                </img>
                            </div>
                        </div>
                    </div>
                </div>
            </xsl:if>
        </header>
    </xsl:template>
</xsl:stylesheet>
