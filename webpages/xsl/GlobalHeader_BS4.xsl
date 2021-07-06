<?xml version='1.0' encoding="UTF-8"?>
<!-- File created by Peter Olszowka July 17, 2020
     Copyright (c) 2020-2021 Peter Olszowka. All rights reserved. See copyright document for more details. -->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:param name="header_version" select="'Participant'"/><!-- "Staff" or "Participant" -->
    <xsl:param name="logged_in" select="false()" /><!-- TRUE/FALSE -->
    <xsl:param name="login_page_status" select="'Normal'" /><!-- "Login", "Logout", "Normal", "No_Permission", "Password_Reset" -->
    <xsl:param name="no_user_required" select="false()" /><!-- TRUE/FALSE -->
    <xsl:param name="CON_NAME" select="''" />
    <xsl:param name="headerimg" select="'images/Z_illuminated.jpg'" />
    <xsl:param name="headerimgalt" select="'Zambia &quot;Z&quot; logo'" />
    <xsl:param name="badgename" select="''" />
    <xsl:param name="USER_ID_PROMPT" select="'Badge ID'" />
    <xsl:param name="header_error_message" select="''" />
    <xsl:param name="RESET_PASSWORD_SELF" select="true()" /><!-- TRUE/FALSE -->
    <xsl:template match="/">
        <header class="header-wrapper" data-pbo="GlobalHeader_BS4.xsl:15">
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
                        <xsl:when test="$logged_in and not($login_page_status = 'Login')">
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
                    </xsl:choose>
                </div><!-- End of reg-header -->
            </div><!-- End of reg-header-container -->
            <xsl:if test="$logged_in and not($login_page_status = 'Login')">
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
        <xsl:if test="not($logged_in) and not($no_user_required)">
            <section class="container mt-3">
                <xsl:choose>
                    <xsl:when test="$login_page_status='Normal'">
                        <div class="alert alert-danger">Session expired. Please log in again.</div>
                    </xsl:when>
                    <xsl:when test="$login_page_status='Logout'">
                        <div class="alert alert-success">You have logged out successfully.</div>
                    </xsl:when>
                    <xsl:when test="$login_page_status='Password_Reset'">
                        <div class="alert alert-success">You have changed your password successfully.</div>
                    </xsl:when>
                    <xsl:when test="$login_page_status='No_Permission'">
                        <div class="alert alert-danger">You do not have permission to access this page.</div>
                    </xsl:when>
                    <xsl:when test="$header_error_message != ''">
                        <div class="alert alert-danger"><xsl:value-of select="$header_error_message" /></div>
                    </xsl:when>
                </xsl:choose>

                <div class="card">
                    <div class="card-header">
                        <h2>Login</h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                        <form id="login-form" class="col-md-6" name="loginform" method="post" action="doLogin.php" data-pbo="GlobalHeader_BS4.xsl:60">
                            <fieldset id="login-box mt-3">
                                <div class="form-group">
                                    <label for="badgeid" class="sr-only"><xsl:value-of select="$USER_ID_PROMPT" /></label>
                                    <input type="text" name="badgeid" id="badgeid" class="form-control" placeholder="{$USER_ID_PROMPT}" title="Enter your {$USER_ID_PROMPT}" />
                                </div>
                                <div class="form-group">
                                    <label for="passwd" class="sr-only">Password</label>
                                    <input type="password" id="passwd" name="passwd" class="form-control" placeholder="Password" title="Enter your password" />
                                </div>
                                <div class="form-group text-center">
                                    <input type="submit" value="Login" class="btn btn-primary" title="Click to log in" />
                                </div>
                            </fieldset>
                        </form>
                        </div>
                        <xsl:if test="$RESET_PASSWORD_SELF">
                            <div class="row">
                                <div class="col-12 text-right"><small><a href="ForgotPassword.php" class="text-muted">New user or forgot your password</a></small></div>
                            </div>
                        </xsl:if>
                    </div>
                </div>
            </section>
        </xsl:if>

    </xsl:template>
</xsl:stylesheet>
