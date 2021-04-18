<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2020-10-28
	Copyright (c) 2020-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="userIdPrompt" select="'Badge ID'" />
    <xsl:param name="updateMessage" select="''" />
    <xsl:param name="errorMessage" select="''" />
    <xsl:param name="control" select="''" />
    <xsl:param name="controliv" select="''" />
    <xsl:param name="new_badgeid" select="''" />
    <xsl:param name="firstname" select="''" />
    <xsl:param name="lastname" select="''" />
    <xsl:param name="badgename" select="''" />
    <xsl:param name="pubsname" select="''" />
    <xsl:param name="phone" select="''" />
    <xsl:param name="email" select="''" />
    <xsl:param name="postaddress1" select="''" />
    <xsl:param name="postaddress2" select="''" />
    <xsl:param name="postcity" select="''" />
    <xsl:param name="poststate" select="''" />
    <xsl:param name="postzip" select="''" />
    <xsl:param name="postcountry" select="''" />
    <xsl:param name="selected" select="''" />
    <xsl:param name="override" select="''" />

    <xsl:output encoding="UTF-8" indent="yes" method="html"/>
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="$errorMessage != ''">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-danger" role="alert">
                            <xsl:value-of select="$errorMessage" disable-output-escaping="yes" />
                        </div>
                    </div>
                </div>
            </xsl:when>
            <xsl:when test="$updateMessage != ''">
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-success" role="alert">
                            <xsl:value-of select="$updateMessage" disable-output-escaping="yes" />
                        </div>
                    </div>
                </div>
            </xsl:when>
        </xsl:choose>
        <div class="row justify-content-center mt-4">
            <h4 class="col-auto">Add New Zambia User</h4>
        </div>
        <form name="adduserform" class="container" method="POST" action="AddZambiaUser.php">
            <input type="hidden" id="PostCheck" name="PostCheck" value="POST"/>
            <input type="hidden" id="control" name="control" value="{$control}"/>
            <input type="hidden" id="controliv" name="controliv" value="{$controliv}"/>

            <div class="row">
                <div class="col-sm-5 col-md-4 col-lg-2p5 col-xl-2p25">
                    <h5>
                        <label for="badgeid" class="badge badge-secondary badge-full-width">
                            Proposed
                            <xsl:value-of select="$userIdPrompt" />
                        </label>
                    </h5>
                </div>
                <div class="col">
                    <input type="text" id="badgeid" name="badgeid" value="{$new_badgeid}" readonly="readonly"
                        size="20" class="disabled" />
                </div>
            </div>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">First Name</xsl:with-param>
                <xsl:with-param name="value" select="$firstname" />
                <xsl:with-param name="id">firstname</xsl:with-param>
                <xsl:with-param name="maxlength" select="30" />
                <xsl:with-param name="fieldsize" select="30" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Last Name</xsl:with-param>
                <xsl:with-param name="value" select="$lastname" />
                <xsl:with-param name="id">lastname</xsl:with-param>
                <xsl:with-param name="maxlength" select="40" />
                <xsl:with-param name="fieldsize" select="40" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Badge Name</xsl:with-param>
                <xsl:with-param name="value" select="$badgename" />
                <xsl:with-param name="id">badgename</xsl:with-param>
                <xsl:with-param name="maxlength" select="50" />
                <xsl:with-param name="fieldsize" select="50" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Name for Publication</xsl:with-param>
                <xsl:with-param name="value" select="$pubsname" />
                <xsl:with-param name="id">pubsname</xsl:with-param>
                <xsl:with-param name="maxlength" select="50" />
                <xsl:with-param name="fieldsize" select="50" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Phone Number</xsl:with-param>
                <xsl:with-param name="value" select="$phone" />
                <xsl:with-param name="id">phone</xsl:with-param>
                <xsl:with-param name="maxlength" select="80" />
                <xsl:with-param name="fieldsize" select="80" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Email Address</xsl:with-param>
                <xsl:with-param name="value" select="$email" />
                <xsl:with-param name="id">email</xsl:with-param>
                <xsl:with-param name="maxlength" select="100" />
                <xsl:with-param name="fieldsize" select="80" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Postal Address</xsl:with-param>
                <xsl:with-param name="value" select="$postaddress1" />
                <xsl:with-param name="id">postaddress1</xsl:with-param>
                <xsl:with-param name="maxlength" select="100" />
                <xsl:with-param name="fieldsize" select="80" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">(Line 2)</xsl:with-param>
                <xsl:with-param name="value" select="$postaddress2" />
                <xsl:with-param name="id">postaddress2</xsl:with-param>
                <xsl:with-param name="maxlength" select="100" />
                <xsl:with-param name="fieldsize" select="80" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">City</xsl:with-param>
                <xsl:with-param name="value" select="$postcity" />
                <xsl:with-param name="id">postcity</xsl:with-param>
                <xsl:with-param name="maxlength" select="50" />
                <xsl:with-param name="fieldsize" select="50" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">State</xsl:with-param>
                <xsl:with-param name="value" select="$poststate" />
                <xsl:with-param name="id">poststate</xsl:with-param>
                <xsl:with-param name="maxlength" select="25" />
                <xsl:with-param name="fieldsize" select="25" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Postal Code</xsl:with-param>
                <xsl:with-param name="value" select="$postzip" />
                <xsl:with-param name="id">postzip</xsl:with-param>
                <xsl:with-param name="maxlength" select="10" />
                <xsl:with-param name="fieldsize" select="10" />
            </xsl:call-template>
            <xsl:call-template name="regRowContents">
                <xsl:with-param name="label">Country</xsl:with-param>
                <xsl:with-param name="value" select="$postcountry" />
                <xsl:with-param name="id">postcountry</xsl:with-param>
                <xsl:with-param name="maxlength" select="25" />
                <xsl:with-param name="fieldsize" select="25" />
            </xsl:call-template>
            <div class="row">
                <div class="col-sm-5 col-md-4 col-lg-2p5 col-xl-2p25">
                    <h5>
                        <div class="badge badge-secondary badge-full-width">Permission Roles</div>
                    </h5>
                </div>
                <div class="col">
                    <xsl:choose>
                        <xsl:when test="count(doc/query[@queryName='roles']/row) = 1">
                            <select id="roles" name="permissionRoles[]" readonly="readonly" class="disabled">
                                <xsl:for-each select="doc/query[@queryName='roles']/row">
                                    <option value="{@permroleid}" selected="selected"><xsl:value-of select="@permrolename" /></option>
                                </xsl:for-each>
                            </select>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="tag-chk-container" id="role-container">
                                <xsl:for-each select="doc/query[@queryName='roles']/row">
                                    <xsl:variable name="permroleid" select="@permroleid" />
                                    <div class="tag-chk-label-wrapper">
                                        <label class="tag-chk-label">
                                            <input type="checkbox" name="permissionRoles[]" class="tag-chk" value="{$permroleid}" >
                                                <xsl:if test="/doc/query[@queryName='selectedRoles']/row[@value=$permroleid]">
                                                    <xsl:attribute name="checked">checked</xsl:attribute>
                                                </xsl:if>
                                            </input>
                                            <xsl:value-of select="@permrolename" />
                                        </label>
                                    </div>
                                </xsl:for-each>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                </div>
            </div>
            <xsl:if test="$override != ''">
                <div class="row mt-2">
                    <div class="col-sm-7 col-md-5 col-lg-4 col-xl-3">
                        <h6 class="pt-1">
                            <label for="override">
                                Override and Force Add
                                <xsl:value-of select="$userIdPrompt" />
                            </label>
                        </h6>
                    </div>
                    <div class="col">
                        <select id="override" name="override" class="span4">
                            <option value="0" selected="selected">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </div>
            </xsl:if>
            <div class="row justify-content-center mt-4">
                <div class="col-auto">
                    <button class="btn btn-secondary" id="resetbtn" name="resetbtn" value="undo" type="button">Reset
                    </button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" id="submitbtn" name="submitbtn" type="submit" value="save"
                            onclick="AddUser()">Add
                    </button>
                </div>
            </div>
        </form>
    </xsl:template>

    <xsl:template name="regRowContents">
        <xsl:param name="label" />
        <xsl:param name="value" />
        <xsl:param name="id" />
        <xsl:param name="fieldsize" />
        <xsl:param name="maxlength" />
        <div class="row">
            <div class="col-sm-5 col-md-4 col-lg-2p5 col-xl-2p25">
                <h5>
                    <label for="{$id}" class="badge badge-secondary badge-full-width">
                        <xsl:value-of select="$label" />
                    </label>
                </h5>
            </div>
            <div class="col">
                <input id="{$id}" name="{$id}" value="{$value}" type="text"
                       size="{$fieldsize}" maxlength="{$maxlength}" />
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>
