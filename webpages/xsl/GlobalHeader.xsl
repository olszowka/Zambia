<?xml version='1.0' encoding="UTF-8"?>
<!-- Copyright (c) 2019 Peter Olszowka. All rights reserved. See copyright document for more details.-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:param name="header_version" select="'Participant'"/><!-- "Staff" or "Participant" -->
    <xsl:param name="logged_in" select="false()" /><!-- TRUE/FALSE -->
    <xsl:param name="login_page_status" select="'Direct'" /><!-- "Login", "Logout", "Normal", "No_Permission" -->
    <xsl:param name="no_user_required" select="false()" /><!-- TRUE/FALSE -->
    <xsl:param name="CON_NAME" select="''" />
    <xsl:param name="badgename" select="''" />
    <xsl:param name="USER_ID_PROMPT" select="'Badge ID'" />
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
                        <img src="images/Z_illuminated.jpg" id="header-logo" alt="Zambia &quot;Z&quot; logo" />
                        <h1 class="wide-medium-only">
                            <xsl:text>Zambia</xsl:text>
                            <span class="wide-only">
                                <xsl:text>: The </xsl:text>
                                <xsl:value-of select="$CON_NAME" />
                                <xsl:text> Scheduling Tool</xsl:text>
                            </span>
                        </h1>
                    </div>
                    <xsl:choose>
                        <xsl:when test="$logged_in">
                            <div id="welcome">
                                <p>
                                    <xsl:text>Welcome, </xsl:text>
                                    <xsl:value-of select="$badgename" />
                                </p>
                                <img id="hide-header" class="img-button pull-right" src="images/blue-up.png"
                                     alt="Shrink header to a thin strip" title="Shrink header to a thin strip" />
                                <a href="logout.php" class="btn btn-primary pull-right" title="Click to log out">Log out</a>
                            </div>
                        </xsl:when>
                        <xsl:when test="not($no_user_required)">
                            <div>
                                <form id="login-form" name="loginform" class="form-horizontal" method="post" action="doLogin.php">
                                    <fieldset id="login-box">
                                        <xsl:choose>
                                            <xsl:when test="$login_page_status='Normal'">
                                                <b class="alert alert-error pull-right">Session expired. Please log in again.</b>
                                            </xsl:when>
                                            <xsl:when test="$login_page_status='Logout'">
                                                <b class="alert alert-success pull-right">You have logged out successfully.</b>
                                            </xsl:when>
                                            <xsl:when test="$login_page_status='No_Permission'">
                                                <b class="alert alert-error pull-right">You do not have permission to access this page.</b>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <xsl:attribute name="class">extended</xsl:attribute>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                        <div class="control-group">
                                            <label class="control-label vert-sep" for="badgeid">
                                                <xsl:value-of select="$USER_ID_PROMPT"/>
                                                <xsl:text>:</xsl:text>
                                            </label>
                                            <div class="controls vert-sep">
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
                                        <div class="control-group">
                                            <div class="controls">
                                                <a href="forgotPassword.php">New user or forgot your password</a>
                                            </div>
                                        </div>
                                    </fieldset>
                                </form>
                            </div>
                        </xsl:when>
                    </xsl:choose>
                </div><!-- End of reg-header -->
            </div><!-- End of reg-header-container -->
            <xsl:if test="$logged_in">
                <div id="alt-header-container" class="collapsible-wrapper hidden">
                    <div id="alt-header" class="row-fluid collapsible">
                        <div class="alt-header-contents">
                            <div class="alt-header-spacer wide-only">
                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                            </div>
                            <div class="alt-header-title">Zambia</div>
                            <div class="alt-header-controls">
                                <p>
                                    <xsl:text>Welcome, </xsl:text>
                                    <xsl:value-of select="$badgename" />
                                </p>
                                <a class="btn btn-primary btn-mini" href="logout.php" title="Click to log out">Log out</a>
                                <img src="images/green-down.png" id="show-header" alt="Expand header to normal size" title="Expand header to normal size" />
                            </div>
                        </div>
                    </div>
                </div>
            </xsl:if>
        </header>
    </xsl:template>
</xsl:stylesheet>