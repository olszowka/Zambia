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
    <xsl:variable name="enableRegEdit"><xsl:value-of select="/doc/options/@enableRegEdit"/></xsl:variable>
    <xsl:variable name="interested"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@interested"/></xsl:variable>
    <xsl:variable name="share_email"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@share_email"/></xsl:variable>
    <xsl:variable name="bestway"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@bestway"/></xsl:variable>
    <xsl:variable name="bioNote"><xsl:value-of select="/doc/customText/@biography_note"/></xsl:variable>
   	<xsl:variable name="currentRegType"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@regtype"/></xsl:variable>

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
                        <xsl:if test="$interested=0 or not ($interested)">
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
                <div class="widelabel"><label for="bio">Your biography (<xsl:value-of select="/doc/options/@maxBioLen"/> characters or fewer including spaces):</label></div>
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
     </form>
 	 <form name="regform" method="POST" action="SubmitMyRegistration.php">
        <div id="congo_section" class="border2222">
            <div class="congo_table">
	            <div class="congo_data">
                    <span class="label">Program Participant ID</span>
                    <span class="value"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgeid"/></span>
                </div>
				<div class="congo_data">
                    <span class="label">Program Registration</span>
                    <span class="value">
						<select name="regtype" id="regtype" class="userFormINPTXT" onchange="myRegistration.anyChange('regtype')">
						<xsl:choose>
							<xsl:when test="$enableRegEdit!='1'"><xsl:attribute name="disabled">disabled</xsl:attribute></xsl:when>
							<xsl:when test="$currentRegType='Comp'">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
								<option selected="selected">Comp</option>
							</xsl:when>
							<xsl:when test="$currentRegType='ConfirmedParticipant'">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
								<option selected="selected">Participant Membership Granted</option>
							</xsl:when>
							<xsl:when test="$currentRegType='Guest of Honor'">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
								<option selected="selected">Guest of Honor</option>
							</xsl:when>
							<xsl:when test="$currentRegType='Staff'">
								<xsl:attribute name="disabled">disabled</xsl:attribute>
								<option selected="selected">Staff</option>
							</xsl:when>
                        </xsl:choose>							
						<option value="None">
							<xsl:if test="'None'=$currentRegType">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							Please select a membership type
						</option>
						<option value="BSFS">
							<xsl:if test="'BSFS'=$currentRegType">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>
							I am a BSFS member
						</option>
						<option value="Paid">
							<xsl:if test="'Paid'=$currentRegType">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>							
							I purchased a registration this year
						</option>
						<option value="Dealer">
							<xsl:if test="'Dealer'=$currentRegType">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>	
							I have registered through another department
						</option>
						<option value="Volunteer">
							<xsl:if test="'Volunteer'=$currentRegType">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>	
							I earned a membership by Volunteering last year
						</option>
						<option value="Participant">
							<xsl:if test="'Participant'=$currentRegType">
								<xsl:attribute name="selected">selected</xsl:attribute>
							</xsl:if>	
							I would like to request Membership as a Participant this year
						</option>
						</select>
					</span>
                </div>
                <div class="congo_data">
                    <span class="label">First Name</span>
					<input type="text" size="20" name="firstname" value="{/doc/query[@queryName='participant_info']/row/@firstname}"
                        id="firstname" onchange="myRegistration.anyChange('firstname')" onkeyup="myRegistration.anyChange('firstname')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
				<div class="congo_data">
                    <span class="label">Middle Initial</span>
					<input type="text" size="1" name="middleInit" value="{/doc/query[@queryName='participant_info']/row/@middleInit}"
                        id="middleInit" onchange="myRegistration.anyChange('middleInit')" onkeyup="myRegistration.anyChange('middleInit')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
                <div class="congo_data">
                    <span class="label">Last Name</span>
					<input type="text" size="20" name="lastname" value="{/doc/query[@queryName='participant_info']/row/@lastname}"
                        id="lastname" onchange="myRegistration.anyChange('lastname')" onkeyup="myRegistration.anyChange('lastname')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
                <div class="congo_data">
                    <span class="label">Badge Name</span>
                    <input type="text" size="20" name="badgename" value="{/doc/query[@queryName='participant_info']/row/@badgename}"
                        id="badgename" onchange="myRegistration.anyChange('badgename')" onkeyup="myRegistration.anyChange('badgename')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
                <div class="congo_data">
                    <span class="label">Phone Info</span>
                    <input type="text" size="20" name="phone" value="{/doc/query[@queryName='participant_info']/row/@phone}"
                        id="phone" onchange="myRegistration.anyChange('phone')" onkeyup="myRegistration.anyChange('phone')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
                <div class="congo_data">
                    <span class="label">Email Address</span><input type="text" size="20" name="email" value="{/doc/query[@queryName='participant_info']/row/@email}"
                        id="email" onchange="myRegistration.anyChange('email')" onkeyup="myRegistration.anyChange('email')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
				<hr/>
                <div class="congo_data">
                    <span class="label">Postal Address</span><input type="text" size="41" name="postaddress1" value="{/doc/query[@queryName='participant_info']/row/@postaddress1}"
                        id="postaddress1" onchange="myRegistration.anyChange('postaddress1')" onkeyup="myRegistration.anyChange('postaddress1')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
                <div class="congo_data">
                    <span class="label"><xsl:text disable-output-escaping="yes">company/address2</xsl:text></span>
                    <input type="text" size="41" name="postaddress2" value="{/doc/query[@queryName='participant_info']/row/@postaddress2}"
						id="postaddress2" onchange="myRegistration.anyChange('postaddress2')" onkeyup="myRegistration.anyChange('postaddress2')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
                <div class="congo_data">
                    <span class="label"><xsl:text disable-output-escaping="yes">city, state zip</xsl:text></span>
                    <input type="text" size="20" name="postcity" value="{/doc/query[@queryName='participant_info']/row/@postcity}"
                        id="postcity" onchange="myRegistration.anyChange('postcity')" onkeyup="myRegistration.anyChange('postcity')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>,
					<input type="text" size="2" name="poststate" value="{/doc/query[@queryName='participant_info']/row/@poststate}"
                        id="poststate" onchange="myRegistration.anyChange('poststate')" onkeyup="myRegistration.anyChange('poststate')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
					<input type="text" size="10" name="postzip" value="{/doc/query[@queryName='participant_info']/row/@postcode}"
                        id="postzip" onchange="myRegistration.anyChange('postzip')" onkeyup="myRegistration.anyChange('postcode')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
				<div class="congo_data">
                    <span class="label"><xsl:text disable-output-escaping="yes">country</xsl:text></span>
					<input type="text" size="20" name="postcode" value="{/doc/query[@queryName='participant_info']/row/@postcountry}"
                        id="postcountry" onchange="myRegistration.anyChange('postcountry')" onkeyup="myRegistration.anyChange('postcountry')"
                        class="userFormINPTXT">
						<xsl:if test="$enableRegEdit!='1'">
                            <xsl:attribute name="readonly">readonly</xsl:attribute>
                        </xsl:if>
					</input>
                </div>
            </div>
			<div class="SubmitDivNew">
                <div class="SubmitDiv2New">
                    <button class="SubmitButtonNew" type="button" name="regSubmitBTN" id="regSubmitBTN" onclick="myRegistration.updateBUTN();">Update</button>
                </div>
            </div>
			<hr/>
            <ul class="congo-note">
				<li class="congo-note"><strong>Please confirm your contact information and registration type.</strong></li>
				<br/>
				<li>First and Last name should reflect your legal name; they are used for identification purposes only and will be required when you pick up your badge and participant packet.</li>
				<br/>
				<li>Postal Address information is required for Participant and Staff memberships;</li>
				<li>Participant and Staff memberships may only be granted by convention staff.</li>
			</ul>
        </div>
    </form>
</xsl:template>
</xsl:stylesheet>
