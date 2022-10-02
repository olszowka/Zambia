<?xml version='1.0' encoding="UTF-8"?>
<!-- File created by Peter Olszowka July 17, 2020
     Copyright (c) 2020-2022 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <!-- "Staff" or "Participant" --><xsl:param name="header_version" select="'Participant'"/>
    <!-- "LOGIN", "SESSION_EXPIRED", "LOGOUT", "PASSWORD_RESET_COMPLETE", "NO_USER", "NORMAL" --><xsl:param name="top_section_behavior" select="'NORMAL'" />
    <xsl:param name="CON_NAME" select="''" />
    <xsl:param name="headerimg" select="'images/Z_illuminated.jpg'" />
    <xsl:param name="headerimgalt" select="'Zambia &quot;Z&quot; logo'" />
    <xsl:param name="badgename" select="''" />
    <xsl:param name="USER_ID_PROMPT" select="'Badge ID'" />
    <!-- TRUE/FALSE --><xsl:param name="RESET_PASSWORD_SELF" select="true()" />
    <xsl:template match="/">
        <header class="header-wrapper">
            <div id="reg-header-container" class="collapsible-wrapper">
                <div id="reg-header">
                    <xsl:choose>
                        <xsl:when test="$header_version='Participant'">
                            <xsl:attribute name="class">header participant-header collapsible</xsl:attribute>
                        </xsl:when>
                        <xsl:when test="$header_version='Staff'">
                            <xsl:attribute name="class">header staff-header collapsible</xsl:attribute>
                        </xsl:when>
                    </xsl:choose>
                    <div class="header-contents">
			    <img class="d-none d-lg-block" >
				    <xsl:attribute name="src"><xsl:value-of select="$headerimg" /></xsl:attribute>
				    <xsl:attribute name="alt"><xsl:value-of select="$headerimgalt" /></xsl:attribute>
			    </img>
                        <h1 class="d-none d-md-block">
                            <xsl:text>Zambia</xsl:text>
                            <span class="d-none d-lg-inline">
                                <xsl:text>: The </xsl:text>
                                <xsl:value-of select="$CON_NAME" />
                                <xsl:text> Scheduling Tool</xsl:text>
                            </span>
                        </h1>
                    </div>
                    <xsl:choose>
                        <xsl:when test="$top_section_behavior = 'NORMAL'">
                            <div id="welcome">
                                <p>
                                    <xsl:text>Welcome, </xsl:text>
                                    <xsl:value-of select="$badgename" />
                                </p>
                                <img id="hide-header-but" class="img-button float-right" aria-role="button"
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
                                <a href="logout.php" class="btn btn-primary float-right mr-2" title="Click to log out">Log out</a>
                            </div>
                        </xsl:when>
                        <xsl:when test="$top_section_behavior = 'LOGIN' or $top_section_behavior = 'SESSION_EXPIRED'
                            or $top_section_behavior = 'LOGOUT' or $top_section_behavior = 'PASSWORD_RESET_COMPLETE'">
                            <div>
                                <form id="login-form" name="loginform" class="form-horizontal" method="post" action="doLogin.php">
                                    <fieldset id="login-box">
                                        <xsl:choose>
                                            <xsl:when test="$top_section_behavior = 'SESSION_EXPIRED'">
                                                <div class="login-alert-container">
                                                    <span class="alert alert-danger">Session expired. Please log in again.</span>
                                                </div>
                                            </xsl:when>
                                            <xsl:when test="$top_section_behavior = 'LOGOUT'">
                                                <div class="login-alert-container">
                                                    <span class="alert alert-success">You have logged out successfully.</span>
                                                </div>
                                            </xsl:when>
                                            <xsl:when test="$top_section_behavior = 'PASSWORD_RESET_COMPLETE'">
                                                <div class="login-alert-container">
                                                    <span class="alert alert-success">You have changed your password successfully.</span>
                                                </div>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:attribute name="class">extended</xsl:attribute>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                        <div class="control-group">
                                            <label class="control-label vert-sep vert-sep-above" for="badgeid">
                                                <xsl:value-of select="$USER_ID_PROMPT"/>
                                                <xsl:text>:</xsl:text>
                                            </label>
                                            <div class="controls vert-sep vert-sep-above">
                                                <input type="text" name="badgeid" id="badgeid" class="pbox" placeholder="{$USER_ID_PROMPT}" title="Enter your {$USER_ID_PROMPT}" />
                                            </div>
                                            <label class="control-label" for="passwd">Password:</label>
                                            <div class="controls">
                                                <input type="password" id="passwd" name="passwd" class="pbox" placeholder="Password" title="Enter your password" />
                                            </div>
                                        </div>
                                        <div class="control-group">
                                            <div class="controls">
                                                <input type="submit" value="Login" class="btn btn-primary" title="Click to log in" />
                                            </div>
                                        </div>
                                        <xsl:if test="$RESET_PASSWORD_SELF">
                                            <div class="control-group">
                                                <div class="controls">
                                                    <a href="ForgotPassword.php">New user or forgot your password</a>
                                                </div>
                                            </div>
                                        </xsl:if>
                                    </fieldset>
                                </form>
                            </div>
                        </xsl:when>
                    </xsl:choose>
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
