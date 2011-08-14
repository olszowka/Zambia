<?xml version="1.0" encoding="UTF-8" ?>
<!--
	my_profile
	Created by Peter Olszowka on 2011-07-24.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <xsl:variable name="conName"><xsl:value-of select="/doc/options/@conName"/></xsl:variable>
    <xsl:variable name="enableBioEdit"><xsl:value-of select="/doc/options/@enableBioEdit"/></xsl:variable>
    <xsl:variable name="interested"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@interested"/></xsl:variable>
    <xsl:variable name="share_email"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@share_email"/></xsl:variable>
    <xsl:variable name="bestway"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@bestway"/></xsl:variable>
    <xsl:variable name="bioNote"><xsl:value-of select="/doc/customText/@biography_note"/></xsl:variable>
    <script type="text/javascript">var maxBioLen = <xsl:value-of select="/doc/options/@maxBioLen"/>;</script>
    <div class="resultBox" id="resultBoxDIV"><span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.</span></div>
    <form name="partform" method="POST" action="SubmitMyContact.php">
        <div class="userFormDiv">
            <div class="labelNinput">
                <div class="widelabel">
                    <label for="interested">I am interested and able to participate in programming for <xsl:value-of select="$conName"/></label>
                </div>
                <span class="inputhadlabel"><select id="interested" name="interested" class="yesno" onchange="myProfile.anyChange('interested');"
                    onkeyup="myProfile.anyChange('interested')">
                    <option value="0">
                        <xsl:if test="$interested=0 or !$interested">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></option>
                    <option value="1">
                        <xsl:if test="$interested=1">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        Yes</option>
                    <option value="2">
                        <xsl:if test="$interested=2">
                            <xsl:attribute name="selected">selected</xsl:attribute>
                        </xsl:if>
                        No</option></select>
                </span>
            </div>
            <xsl:choose>
                <xsl:when test="/doc/options/@enable_share_email_question">
                    <div class="labelNinput">
                        <div class="widelabel">
                            <label for="share_email">I give permission for <xsl:value-of select="$conName"/> to share my email address with other participants</label>
                        </div>
                        <span class="inputhadlabel"><select id="share_email" name="share_email" class="yesno" onchange="myProfile.anyChange('share_email')"
                            onkeyup="myProfile.anyChange('share_email')">
                        <option value="null">
                            <xsl:if test="not($share_email) and $share_email!='0'"><!-- is there an explicit test for null? -->
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></option>
                        <option value="0">
                            <xsl:if test="$share_email='0'">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            No</option>
                        <option value="1">
                            <xsl:if test="$share_email='1'">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            Yes</option></select>
                        </span>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <input name="share_email" type="hidden" value="{$share_email}"/>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:choose>
                <xsl:when test="/doc/options/@enable_bestway_question">
                    <div class="labelNinput">
                        <div class="widelabel">
                            <label for="bestway">Preferred mode of contact</label>
                        </div>
                        <div class="verticalRadioButs">
                            <div class="radioNlabel">
                                <span class="radio">
                                    <input name="bestway" id="bwemailRB" value="Email" type="radio" onchange="myProfile.anyChange('bestway')"
                                        onkeyup="myProfile.anyChange('bestway')">
                                        <xsl:if test="$bestway='Email' or not($bestway)">
                                            <xsl:attribute name="checked" select="checked"/>
                                        </xsl:if>
                                    </input>
                                </span>
                                <span class="radioLabel"><label for="bwemailRB">Email</label></span>
                            </div>
                            <div class="radioNlabel">
                                <span class="radio">
                                    <input name="bestway" id="bwpmailRB" value="Postal mail" type="radio" onchange="myProfile.anyChange('bestway')"
                                        onkeyup="myProfile.anyChange('bestway')">
                                        <xsl:if test="$bestway='Postal mail'">
                                            <xsl:attribute name="checked" select="checked"/>
                                        </xsl:if>
                                    </input>
                                </span>
                                <span class="radioLabel"><label for="bwpmailRB">Postal Mail</label></span>
                            </div>
                            <div class="radioNlabel">
                                <span class="radio">
                                    <input name="bestway" id="bwphoneRB" value="Phone" type="radio" onchange="myProfile.anyChange('bestway')"
                                        onkeyup="myProfile.anyChange('bestway')">
                                        <xsl:if test="$bestway='Phone'">
                                            <xsl:attribute name="checked" select="checked"/>
                                        </xsl:if>
                                    </input>
                                </span>
                                <span class="radioLabel"><label for="bwphoneRB">Phone</label></span>
                            </div>
                        </div>
                    </div>
                </xsl:when>
                <xsl:otherwise>
                    <input name="bestway" id="bestway" type="hidden" value="{$bestway}"/>
                </xsl:otherwise>
            </xsl:choose>
            <div class="labelNinput">
                 <div class="widelabel">
                    <label for="password">Change Password</label>
                    <span id="badPassword" class="hiddenWarning"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>Passwords don't match!</span>
                </div>
                <span class="inputhadlabel"><input type="password" size="10" name="password" id="password"
                    onchange="myProfile.anyChange('password')" onkeyup="myProfile.anyChange('password')"/></span>
            </div>
            <div class="labelNinput">
                <div class="widelabel">
                    <label for="cpassword">Confirm New Password</label>
                </div>
                <span class="inputhadlabel">
                    <input type="password" size="10" name="cpassword" id="cpassword"
                        onchange="myProfile.anyChange('cpassword')" onkeyup="myProfile.anyChange('cpassword')"/>
                </span>
            </div>
            <xsl:if test="$enableBioEdit!='1'">
                <div class="noteWLfPad">At this time, you may not edit either your biography or your name for publication.  They have already gone to print.</div>
            </xsl:if>
            <div class="labelNinput">
                <div class="widelabel">
                    <label for="pubsname">Your name as you wish to have it published</label>
                </div>
                <span class="inputhadlabel">
                    <input type="text" size="20" name="pubsname" value="{/doc/query[@queryName='participant_info']/row/@pubsname}"
                        id="pubsname" onchange="myProfile.anyChange('pubsname')" onkeyup="myProfile.anyChange('pubsname')"
                        class="userFormINPTXT">
                        <xsl:if test="$enableBioEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
                    </input>
                </span>
            <div class="labelNinput">
                <div class="widelabel"><label for="bio">Your biography (<xsl:value-of select="/doc/options/@maxBioLen"/> characters or fewer):</label></div>
                <xsl:if test="$bioNote">
                    <div class="note"><xsl:value-of select="$bioNote" disable-output-escaping="yes"/></div>
                </xsl:if>
                <div>
                    <textarea rows="5" cols="72" name="bio" id="bioTXTA" onchange="myProfile.anyChange('bioTXTA')"
                        onkeyup="myProfile.anyChange('bioTXTA')"><xsl:choose>
                        <xsl:when test="$enableBioEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                            <xsl:attribute name="class">userFormTXT readonly</xsl:attribute>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="class">userFormTXT</xsl:attribute>
                        </xsl:otherwise>
                        </xsl:choose><xsl:value-of
                        select="/doc/query[@queryName='participant_info']/row/@bio"/></textarea>
                </div>
                </div><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text><span id="badBio" class="hiddenWarning">Biography is too long!</span></div>
            <xsl:if test="/doc/query[@queryName='credentials']/row">
                <div class="labelNinput">
                    <div>Please indicate if you are any of the following:</div>
                    <xsl:for-each select="/doc/query[@queryName='credentials']/row">
                        <xsl:sort select="@display_order" data-type="number"/>
                        <div class="checkboxList">
                            <span>
                                <input id="credentialCHK{@credentialid}" type="checkbox"
                                    onchange="myProfile.anyChange('credentialCHK{@credentialid}')"
                                    onkeyup="myProfile.anyChange('credentialCHK{@credentialid}')">
                                    <xsl:if test="@badgeid">
                                        <xsl:attribute name="checked" value="checked"/>
                                    </xsl:if>
                                    <xsl:if test="$enableBioEdit!='1'">
                                        <xsl:attribute name="disabled" value="disabled"/>
                                        <xsl:attribute name="class" value="readonly"/>
                                    </xsl:if>
                                </input>
                            </span>
                            <span>
                                <xsl:value-of select="@credentialname"/>
                            </span>
                        </div>
                    </xsl:for-each>
                </div>
            </xsl:if>
            <div class="SubmitDivNew">
                <div class="SubmitDiv2New">
                    <button class="SubmitButtonNew" type="button" name="submitBTN" id="submitBTN" onclick="myProfile.updateBUTN();">Update</button>
                </div>
            </div>
        </div>
        <div id="congo_section" class="border2222">
            <div class="congo_table">
                <div class="congo_data">
                    <span class="label">Badge ID</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgeid"/></span>
                </div>
                <div class="congo_data">
                    <span class="label">First Name</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@firstname"/></span>
                </div>
                <div class="congo_data">
                    <span class="label">Last Name</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@lastname"/></span>
                </div>
                <div class="congo_data">
                    <span class="label">Badge Name</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgename"/></span>
                </div>
                <div class="congo_data">
                    <span class="label">Phone Info</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@phone"/></span>
                </div>
                <div class="congo_data">
                    <span class="label">Email Address</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@email"/></span>
                </div>
                <div class="congo_data">
                    <span class="label">Postal Address</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postaddress1"/></span>
                </div>
                <xsl:if test="/doc/query[@queryName='participant_info']/row/@postaddress2">
                    <div class="congo_data">
                        <span class="label"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></span>
                        <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postaddress2"/></span>
                    </div>
                </xsl:if>
                <div class="congo_data">
                    <span class="label"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postcity"/>, <xsl:value-of
                        select="/doc/query[@queryName='participant_info']/row/@poststate"/> <xsl:value-of
                        select="/doc/query[@queryName='participant_info']/row/@postzip"/></span>
                </div>
                <xsl:if test="/doc/query[@queryName='participant_info']/row/@postcountry">
                    <div class="congo_data">
                        <span class="label"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></span>
                        <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postcountry"/></span>
                    </div>
                </xsl:if>
            </div>
            <p class="congo-note">Please confirm your contact information.  If it is not correct, please log into Arisia's
                <a HREF="http://arisia.stonekeep.com" target="_blank">on-line registration system</a> and correct it there.
                Please note that the password there is <span style="font-weight: bold">not the same</span> as the one you use
                in Zambia. This data is downloaded periodically from the registration database, and should be correct within an hour.</p>
        </div>
    </form>
</xsl:template>
</xsl:stylesheet>
