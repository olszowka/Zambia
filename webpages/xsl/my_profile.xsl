<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2011-07-24;
	Copyright (c) 2011-2021 Peter Olszowka. All rights reserved.
	See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="conName" select="''"/>
    <xsl:param name="enableShareEmailQuestion" select="'0'"/>
    <xsl:param name="enableUsePhotoQuestion" select="'0'"/>
    <xsl:param name="enableBestwayQuestion" select="'0'"/>
    <xsl:param name="enablePronouns" select="'0'"/>
    <xsl:param name="useRegSystem" select="0"/>
    <xsl:param name="maxBioLen" select="500"/>
    <xsl:param name="enableBioEdit" select="'0'"/>
    <xsl:param name="userIdPrompt" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:template match="/">
        <xsl:variable name="use_photo" select="/doc/query[@queryName='participant_info']/row/@use_photo" />
        <xsl:variable name="share_email" select="/doc/query[@queryName='participant_info']/row/@share_email" />
        <xsl:variable name="interested" select="/doc/query[@queryName='participant_info']/row/@interested" />
        <xsl:variable name="bestway" select="/doc/query[@queryName='participant_info']/row/@bestway" />
        <xsl:variable name="bioNote" select="/doc/customText/@biography_note" />
        <xsl:variable name="regDataNote" select="/doc/customText/@registration_data" />
        <div id="resultBoxDIV">
            <span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span>
        </div>
        <form name="partform" class="container mt-2 mb-4">
            <div class="card">
                <div class="card-header">
                    <h2>My Profile</h2>
                </div>
                <div class="card-body">
                    <div class="row mt-3">
                        <legend class="col-auto">Permissions</legend>
                    </div>
                    <fieldset>
                        <div class="row">
                            <div class="col-auto">
                                <label for="interested">
                                    I am interested and able to participate in programming for <xsl:value-of select="$conName"/>:
                                </label>
                            </div>
                            <div class="col-auto">
                                <select id="interested" name="interested" class="mb-2 pl-2 pr-4 mycontrol">
                                    <option value="0">
                                        <xsl:if test="$interested=0 or not ($interested)">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                    </option>
                                    <option value="1">
                                        <xsl:if test="$interested=1">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        Yes
                                    </option>
                                    <option value="2">
                                        <xsl:if test="$interested=2">
                                            <xsl:attribute name="selected">selected</xsl:attribute>
                                        </xsl:if>
                                        No
                                    </option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <xsl:choose>
                        <xsl:when test="$enableShareEmailQuestion = '1'">
                            <fieldset>
                                <div class="row">
                                    <div class="col-auto">
                                        <label for="share_email">
                                            I give permission for <xsl:value-of select="$conName"/>
                                            to share my email address with other participants:
                                        </label>
                                    </div>
                                    <div class="col-auto">
                                        <select id="share_email" name="share_email" class="mb-2 pl-2 pr-4 mycontrol">
                                            <option value="null">
                                                <xsl:if test="not($share_email) and $share_email !='0'"><!-- is there an explicit test for null? -->
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                            </option>
                                            <option value="0">
                                                <xsl:if test="$share_email = '0'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                No
                                            </option>
                                            <option value="1">
                                                <xsl:if test="$share_email = '1'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="share_email" type="hidden" value="{$share_email}"/>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:choose>
                        <xsl:when test="$enableUsePhotoQuestion = '1'">
                            <fieldset>
                                <div class="row">
                                    <div class="col-auto">
                                        <label for="use_photo">
                                            I give permission for <xsl:value-of select="$conName"/> to photograph me while
                                            I am on panels and to use those images in the promotion of the convention:
                                        </label>
                                    </div>
                                    <div class="col-auto">
                                        <select id="use_photo" name="use_photo" class="mb-2 pl-2 pr-4 mycontrol">
                                            <option value="null">
                                                <xsl:if test="not($use_photo) and $use_photo != '0'"><!-- is there an explicit test for null? -->
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                                            </option>
                                            <option value="0">
                                                <xsl:if test="$use_photo = '0'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                No
                                            </option>
                                            <option value="1">
                                                <xsl:if test="$use_photo = '1'">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                Yes
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="use_photo" type="hidden" value="{$use_photo}"/>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:choose>
                        <xsl:when test="$enableBestwayQuestion = '1'">
                            <fieldset>
                                <div class="row">
                                    <div class="col-auto">
                                        <label for="bestway">Preferred mode of contact:</label>
                                    </div>
                                    <div class="col-auto">
                                        <div class="verticalRadioButs">
                                            <div class="radioNlabel">
                                                <span class="radio">
                                                    <input name="bestway" id="bwemailRB" value="Email" type="radio" class="mycontrol">
                                                        <xsl:if test="$bestway='Email' or not($bestway)">
                                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                                        </xsl:if>
                                                    </input>
                                                </span>
                                                <span class="radioLabel">
                                                    <label for="bwemailRB">Email</label>
                                                </span>
                                            </div>
                                            <div class="radioNlabel">
                                                <span class="radio">
                                                    <input name="bestway" id="bwpmailRB" value="Postal mail" type="radio" class="mycontrol">
                                                        <xsl:if test="$bestway='Postal mail'">
                                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                                        </xsl:if>
                                                    </input>
                                                </span>
                                                <span class="radioLabel">
                                                    <label for="bwpmailRB">Postal Mail</label>
                                                </span>
                                            </div>
                                            <div class="radioNlabel">
                                                <span class="radio">
                                                    <input name="bestway" id="bwphoneRB" value="Phone" type="radio" class="mycontrol">
                                                        <xsl:if test="$bestway='Phone'">
                                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                                        </xsl:if>
                                                    </input>
                                                </span>
                                                <span class="radioLabel">
                                                    <label for="bwphoneRB">Phone</label>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </xsl:when>
                        <xsl:otherwise>
                            <input name="bestway" id="bestway" type="hidden" value="{$bestway}"/>
                        </xsl:otherwise>
                    </xsl:choose>
                    <div class="row mt-3">
                        <legend class="col-auto">Change password</legend>
                    </div>
                    <div class="row mb-3">
                        <div class="col-auto">Leave passwords fields blank to leave password unchanged.</div>
                    </div>
                    <fieldset id="passGroup" class="control-group">
                        <div class="row">
                            <div class="col-2">
                                <label for="password">New Password:</label>
                            </div>
                            <div class="col-4">
                                <input type="password" size="40" maxlength="40" name="password" id="password"
                                    class="form-control mycontrol mb-2" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-2">
                                <label for="cpassword">Confirm Password:</label>
                            </div>
                            <div class="col-4 mb-2">
                                <input type="password" size="40" maxlength="40" name="cpassword" id="cpassword"
                                    class="form-control mycontrol mb-2" />
                                <div class="invalid-feedback">
                                    Passwords don't match!
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <div class="row mt-3">
                            <legend class="col-auto">Published Information</legend>
                        </div>
                        <xsl:if test="$enableBioEdit!='1'">
                            <div class="row">
                                <h3 class="noteWLfPad">At this time, you may not edit either your biography or your name for
                                    publication. They have already gone to print.
                                </h3>
                            </div>
                        </xsl:if>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="pubsname">Your name as you wish to have it published:</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" size="20" maxlength="50" name="pubsname"
                                    value="{/doc/query[@queryName='participant_info']/row/@pubsname}"
                                    id="pubsname" class="mycontrol userFormINPTXT">
                                    <xsl:if test="$enableBioEdit!='1'">
                                        <xsl:attribute name="readonly">readonly</xsl:attribute>
                                    </xsl:if>
                                </input>
                            </div>
                        </div>
                        <xsl:if test="$enablePronouns=1">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="pronouns">Pronouns (optional):</label>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" size="20" maxlength="25" name="pronouns"
                                        value="{/doc/query[@queryName='participant_info']/row/@pronouns}"
                                        id="pronouns" class="mycontrol userFormINPTXT" placeholder="e.g. she/her, they/them">
                                        <xsl:if test="$enableBioEdit!='1'">
                                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                                        </xsl:if>
                                    </input>
                                </div>
                            </div>
                        </xsl:if>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="bio">
                                    Biography (<xsl:value-of select="$maxBioLen"/> characters or fewer including
                                    spaces):
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <textarea rows="5" cols="72" name="bio" id="bioTXTA" data-max-length="{$maxBioLen}">
                                    <xsl:choose>
                                        <xsl:when test="$enableBioEdit!='1'">
                                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                                            <xsl:attribute name="class">span12 userFormTXT readonly mycontrol</xsl:attribute>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:attribute name="class">span12 userFormTXT mycontrol</xsl:attribute>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                    <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@bio"/>
                                </textarea>
                                <div id="badBio" class="invalid-feedback">Biography is too long!</div>
                            </div>
                        </div>
                        <xsl:if test="$bioNote">
                            <div class="row mt-1">
                                <div class="col note">
                                    <xsl:value-of select="$bioNote" disable-output-escaping="yes"/>
                                </div>
                            </div>
                        </xsl:if>
                    </fieldset>
                    <xsl:if test="/doc/query[@queryName='credentials']/row">
                        <fieldset>
                            <div class="row mt-3">
                                <legend class="col-auto">Professions</legend>
                            </div>
                            <div class="row mb-2">
                                <div class="col-sm-12">
                                    <div>Please indicate if you are any of the following:</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-9 col-lg-6">
                                    <div class="row">
                                        <xsl:for-each select="/doc/query[@queryName='credentials']/row">
                                            <xsl:sort select="@display_order" data-type="number"/>
                                            <div class="col-sm-6">
                                                <label class="checkbox">
                                                    <input class="checkbox mycontrol mr-3" id="credentialCHK{@credentialid}" type="checkbox">
                                                        <xsl:if test="@badgeid">
                                                            <xsl:attribute name="checked">checked</xsl:attribute>
                                                        </xsl:if>
                                                        <xsl:if test="$enableBioEdit!='1'">
                                                            <xsl:attribute name="disabled">disabled</xsl:attribute>
                                                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                                                        </xsl:if>
                                                    </input>
                                                    <xsl:value-of select="@credentialname"/>
                                                </label>
                                            </div>
                                        </xsl:for-each>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </xsl:if>
                    <xsl:if test="$useRegSystem = 1"><!-- show button here if using reg system because data below not editable in that case -->
                        <div class="row mt-3">
                            <button class="btn btn-primary" type="button" name="submitBTN" id="submitBTN"
                                data-loading-text="Updating..." onclick="myProfile.updateBUTN();">
                                Update
                            </button>
                        </div>
                    </xsl:if>
                    <xsl:choose>
                        <xsl:when test="$useRegSystem = 1">
                            <div class="row mt-3">
                                <legend class="col-auto">Data from Registration System</legend>
                            </div>
                            <xsl:if test="$regDataNote != ''">
                                <div class="row">
                                    <div class="col">
                                        <xsl:value-of select="$regDataNote" disable-output-escaping="yes" />
                                    </div>
                                </div>
                            </xsl:if>
                        </xsl:when>
                        <xsl:otherwise>
                            <div class="row mt-3">
                                <legend class="col-auto">Contact Information</legend>
                            </div>
                            <div class="row">
                                <div class="col">Please confirm your contact information. Please provide missing information or correct what has changed.</div>
                            </div>
                        </xsl:otherwise>
                    </xsl:choose>
                    <fieldset>
                        <div>
                            <xsl:choose>
                                <xsl:when test="$useRegSystem = 1">
                                    <xsl:attribute name="class">row</xsl:attribute>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:attribute name="class">row mt-1 mb-2</xsl:attribute>
                                </xsl:otherwise>
                            </xsl:choose>
                            <div class="col-sm-3 col-md-2p5 col-lg-2">
                                <h5>
                                    <div class="badge badge-secondary badge-full-width">
                                        <xsl:value-of select="$userIdPrompt" />
                                    </div>
                                </h5>
                            </div>
                            <div class="col">
                                <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgeid" />
                            </div>
                        </div>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">First Name</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@firstname" />
                            <xsl:with-param name="id">fname</xsl:with-param>
                            <xsl:with-param name="maxlength" select="30" />
                            <xsl:with-param name="fieldsize" select="30" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Last Name</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@lastname" />
                            <xsl:with-param name="id">lname</xsl:with-param>
                            <xsl:with-param name="maxlength" select="40" />
                            <xsl:with-param name="fieldsize" select="40" />

                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Badge Name</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@badgename" />
                            <xsl:with-param name="id">badgename</xsl:with-param>
                            <xsl:with-param name="maxlength" select="50" />
                            <xsl:with-param name="fieldsize" select="50" />

                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Phone Info</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@phone" />
                            <xsl:with-param name="id">phone</xsl:with-param>
                            <xsl:with-param name="maxlength" select="80" />
                            <xsl:with-param name="fieldsize" select="80" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Email Address</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@email" />
                            <xsl:with-param name="id">email</xsl:with-param>
                            <xsl:with-param name="maxlength" select="100" />
                            <xsl:with-param name="fieldsize" select="80" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Postal Address</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@postaddress1" />
                            <xsl:with-param name="id">postaddress1</xsl:with-param>
                            <xsl:with-param name="maxlength" select="100" />
                            <xsl:with-param name="fieldsize" select="80" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">(line 2)</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@postaddress2" />
                            <xsl:with-param name="id">postaddress2</xsl:with-param>
                            <xsl:with-param name="maxlength" select="100" />
                            <xsl:with-param name="fieldsize" select="80" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Postal City</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@postcity" />
                            <xsl:with-param name="id">postcity</xsl:with-param>
                            <xsl:with-param name="maxlength" select="50" />
                            <xsl:with-param name="fieldsize" select="50" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Postal State</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@poststate" />
                            <xsl:with-param name="id">poststate</xsl:with-param>
                            <xsl:with-param name="maxlength" select="25" />
                            <xsl:with-param name="fieldsize" select="25" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Zip Code</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@postzip" />
                            <xsl:with-param name="id">postzip</xsl:with-param>
                            <xsl:with-param name="maxlength" select="10" />
                            <xsl:with-param name="fieldsize" select="10" />
                        </xsl:call-template>
                        <xsl:call-template name="regRowContents">
                            <xsl:with-param name="label">Country</xsl:with-param>
                            <xsl:with-param name="value" select="/doc/query[@queryName='participant_info']/row/@postcountry" />
                            <xsl:with-param name="id">postcountry</xsl:with-param>
                            <xsl:with-param name="maxlength" select="25" />
                            <xsl:with-param name="fieldsize" select="25" />
                        </xsl:call-template>
                    </fieldset>
                    <xsl:if test="$useRegSystem != 1"><!-- show button here if not using reg system -->
                        <div class="row mt-3">
                            <div class="col-auto">
                                <button class="btn btn-primary" type="button" name="submitBTN" id="submitBTN"
                                data-loading-text="Updating..." onclick="myProfile.updateBUTN();">
                                Update
                                </button>
                            </div>
                        </div>
                    </xsl:if>
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
            <div class="col-sm-3p5 col-md-3 col-lg-2">
                <h5>
                    <xsl:choose>
                        <xsl:when test="$useRegSystem = 1">
                            <div class="badge badge-secondary badge-full-width">
                                <xsl:value-of select="$label" />
                            </div>
                        </xsl:when>
                        <xsl:otherwise>
                            <label for="{$id}" class="badge badge-secondary badge-full-width">
                                <xsl:value-of select="$label" />
                            </label>
                        </xsl:otherwise>
                    </xsl:choose>
                </h5>
            </div>
            <div class="col">
                <xsl:choose>
                    <xsl:when test="$useRegSystem = 1">
                        <xsl:value-of select="$value" />
                    </xsl:when>
                    <xsl:otherwise>
                        <input id="{$id}" name="{$id}" value="{$value}" type="text"
                            size="{$fieldsize}" maxlength="{$maxlength}" class="mycontrol" />
                    </xsl:otherwise>
                </xsl:choose>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>
