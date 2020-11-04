<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2011-07-24; Updated 2020-10-28 Syd Weinsteiin
	Copyright (c) 2011-2020 Peter Olszowka. All rights reserved.
	See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
<xsl:template match="/">
    <xsl:variable name="conName"><xsl:value-of select="/doc/options/@conName"/></xsl:variable>
    <xsl:variable name="enableBioEdit"><xsl:value-of select="/doc/options/@enableBioEdit"/></xsl:variable>
    <xsl:variable name="interested"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@interested"/></xsl:variable>
    <xsl:variable name="share_email"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@share_email"/></xsl:variable>
    <xsl:variable name="use_photo"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@use_photo"/></xsl:variable>
    <xsl:variable name="bestway"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@bestway"/></xsl:variable>
    <xsl:variable name="bioNote"><xsl:value-of select="/doc/customText/@biography_note"/></xsl:variable>
    <xsl:variable name="regURL"><xsl:value-of select="/doc/options/@reg_url"/></xsl:variable>
    <xsl:variable name="enable_reg_edit"><xsl:value-of select="/doc/options/@enable_reg_edit"/></xsl:variable>
    <script type="text/javascript">var maxBioLen = <xsl:value-of select="/doc/options/@maxBioLen"/>;</script>
    <div class="alert-block" id="resultBoxDIV">
      <span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.
      </span>
    </div>
    <div class="container-fluid">
      <form name="partform" class="container mt-2 mb-4" method="POST" action="SubmitMyContact.php">
        <div class="row mt-3">
          <legend>Permissions</legend>
        </div>
        <fieldset>
          <div class="row">
            <div class="col-auto">
              <label for="interested">
                I am interested and able to participate in programming for <xsl:value-of select="$conName"/>:
              </label>
            </div>
            <div class="col-auto">
              <select id="interested" name="interested" class="tcell" onchange="myProfile.anyChange('interested');"
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
                    No</option>
              </select>
            </div>
          </div>
        </fieldset>
        <xsl:choose>
          <xsl:when test="/doc/options/@enable_share_email_question">
            <fieldset>
              <div class="row">
                <div class="col-auto">
                  <label for="share_email">
                    I give permission for <xsl:value-of select="$conName"/> to share my email address with other participants:
                  </label>
                </div>
                <div class="col-auto">
                  <select id="share_email" name="share_email" class="tcell" onchange="myProfile.anyChange('share_email')"
                      onkeyup="myProfile.anyChange('share_email')">
                    <option value="null">
                      <xsl:if test="not($share_email) and $share_email!='0'">
                        <!-- is there an explicit test for null? -->
                        <xsl:attribute name="selected">selected</xsl:attribute>
                      </xsl:if>
                      <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                    </option>
                    <option value="0">
                      <xsl:if test="$share_email='0'">
                        <xsl:attribute name="selected">selected</xsl:attribute>
                      </xsl:if>
                      No
                    </option>
                    <option value="1">
                      <xsl:if test="$share_email='1'">
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
          <xsl:when test="/doc/options/@enable_use_photo_question">
            <fieldset>
              <div class="row">
                <div class="col-auto">
                  <label for="use_photo">I give permission for <xsl:value-of select="$conName"/> to photograph me while I am on panels and to use those images in the promotion of the convention: <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></label>
                  <select id="use_photo" name="use_photo" class="tcell" onchange="myProfile.anyChange('use_photo')"
                      onkeyup="myProfile.anyChange('use_photo')">
                  <option value="null">
                      <xsl:if test="not($use_photo) and $use_photo!='0'"><!-- is there an explicit test for null? -->
                          <xsl:attribute name="selected">selected</xsl:attribute>
                      </xsl:if>
                      <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></option>
                  <option value="0">
                      <xsl:if test="$use_photo='0'">
                          <xsl:attribute name="selected">selected</xsl:attribute>
                      </xsl:if>
                      No</option>
                  <option value="1">
                      <xsl:if test="$use_photo='1'">
                          <xsl:attribute name="selected">selected</xsl:attribute>
                      </xsl:if>
                      Yes</option>
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
        <xsl:when test="/doc/options/@enable_bestway_question">
          <fieldset>
            <div class="row">
              <div class="col-auto">
                <label for="bestway">Preferred mode of contact:</label>
              </div>
              <div class="col-auto">
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
                    <span class="radioLabel">
                      <label for="bwemailRB">Email</label>
                    </span>
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
                    <span class="radioLabel">
                      <label for="bwpmailRB">Postal Mail</label>
                    </span>
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
        <fieldset>
          <div class="row" id="passGroup">
            <div class="col-auto">
              <label for="password">New Password:&#160;</label>
              <input type="password" size="40" maxlength="40" name="password" id="password" onchange="myProfile.anyChange('password')" onkeyup="myProfile.anyChange('password')"/>
            </div>
            <div class="col-auto">
              <label for="cpassword">Confirm Password:&#160;</label>
              <input type="password" size="40" maxlength="40" name="cpassword" id="cpassword"  onchange="myProfile.anyChange('cpassword')" onkeyup="myProfile.anyChange('cpassword')"/>
              <span id="badPassword" class="help-inline"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>Passwords don't match!</span>
            </div>
          </div>
        </fieldset>
        <div class="row mt-3">
          <legend>Biography</legend>
        </div>
        <xsl:if test="$enableBioEdit!='1'">
          <div class="row">
            <h3 class="noteWLfPad">At this time, you may not edit either your biography or your name for publication. They have already gone to print.</h3>
          </div>
        </xsl:if>
        <div class="row">
          <div class="col-auto">
            <label for="pubsname">Your name as you wish to have it published:</label>
          </div>
          <div class="col-auto">
              <input type="text" size="20" maxlength="50" name="pubsname" value="{/doc/query[@queryName='participant_info']/row/@pubsname}"
                  id="pubsname" onchange="myProfile.anyChange('pubsname')" onkeyup="myProfile.anyChange('pubsname')"
                  class="userFormINPTXT">
                  <xsl:if test="$enableBioEdit!='1'">
                      <xsl:attribute name="readonly">readonly</xsl:attribute>
                  </xsl:if>
              </input>
          </div>
        </div>
        <div id="bioGroup">
          <div class="row">
            <div class="col-sm-12">
              <label for="htmlbio">
                HTML Version (<xsl:value-of select="/doc/options/@maxBioLen"/> characters or fewer including spaces):
              </label>
            </div>
          </div>
          <div class="row">
            <div class="col-sm-12">
              <textarea rows="5" cols="72" name="htmlbio" id="htmlbioTXTA" onchange="myProfile.anyChange('bioTXTA')"
                  onkeyup="myProfile.anyChange('bioTXTA')">
                <xsl:choose>
                  <xsl:when test="$enableBioEdit!='1'">
                    <xsl:attribute name="readonly">readonly</xsl:attribute>
                    <xsl:attribute name="class">col-sm-12 userFormTXT readonly</xsl:attribute>
                  </xsl:when>
                  <xsl:otherwise>
                    <xsl:attribute name="class">col-sm-12 userFormTXT</xsl:attribute>
                  </xsl:otherwise>
                </xsl:choose>
                <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@htmlbio"/>
              </textarea>
              <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
              <span id="badBio" class="help-inline">Biography is too long!</span>
              <label for="bio">Plain Text Version (Automatically derived from HTML version on pressing UPDATE):</label>
              <textarea rows="5" cols="72" name="bio" id="bioTXTA">
                <xsl:attribute name="readonly">readonly</xsl:attribute>
                <xsl:attribute name="class">col-sm-12 userFormTXT readonly</xsl:attribute>
                <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@bio"/>
              </textarea>
            </div>
          </div>
          <xsl:if test="$bioNote">
            <div class="row">
              <div class="col-auto">
                <xsl:value-of select="$bioNote" disable-output-escaping="yes"/>
              </div>
            </div>
          </xsl:if>
        </div>
        <xsl:if test="/doc/query[@queryName='credentials']/row">
          <div class="row mt-3">
            <legend>Professions</legend>
          </div>
          <div class="row">
            <div class="col-auto">
              <label>Please indicate if you are any of the following:</label>
            </div>
          </div>
                <xsl:for-each select="/doc/query[@queryName='credentials']/row">
                    <xsl:sort select="@display_order" data-type="number"/>
                  <div class="row">
                    <div class="col-auto">
                        <input class="checkbox" id="credentialCHK{@credentialid}" type="checkbox"
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
                      <label>
                        <xsl:value-of select="@credentialname"/>
                      </label>
                    </div>
                  </div>
                </xsl:for-each>
        </xsl:if>
        <xsl:if test="$enable_reg_edit='1'">
          <div id="congo_section">
            <fieldset>
              <div class="row mt-3">
                <legend>Contact Information</legend>
              </div>
              <div class="row">
                <p>
                  Please confirm your contact information.  Please provide missing information or correct what has changed.
                </p>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Badge ID</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgeid"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">First Name</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="30" maxlength="30" name="firstname" value="{/doc/query[@queryName='participant_info']/row/@firstname}"
                            id="firstname" onchange="myProfile.anyChange('firstname')" onkeyup="myProfile.anyChange('firstname')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Last Name</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="40" maxlength="40" name="lastname" value="{/doc/query[@queryName='participant_info']/row/@lastname}"
                            id="lastname" onchange="myProfile.anyChange('lastname')" onkeyup="myProfile.anyChange('lastname')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Badge Name</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="50" maxlength="50" name="badgename" value="{/doc/query[@queryName='participant_info']/row/@badgename}"
                            id="badgename" onchange="myProfile.anyChange('badgename')" onkeyup="myProfile.anyChange('badgename')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Phone Info</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="80" maxlength="100" name="phone" value="{/doc/query[@queryName='participant_info']/row/@phone}"
                            id="phone" onchange="myProfile.anyChange('phone')" onkeyup="myProfile.anyChange('phone')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Email Address</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="80" maxlength="100" name="email" value="{/doc/query[@queryName='participant_info']/row/@email}"
                            id="email" onchange="myProfile.anyChange('email')" onkeyup="myProfile.anyChange('email')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal Address</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="80" maxlength="100" name="postaddress1" value="{/doc/query[@queryName='participant_info']/row/@postaddress1}"
                            id="postaddress1" onchange="myProfile.anyChange('postaddress1')" onkeyup="myProfile.anyChange('postaddress1')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">&#160;</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="80" maxlength="100" name="postaddress2" value="{/doc/query[@queryName='participant_info']/row/@postaddress2}"
                            id="postaddress2" onchange="myProfile.anyChange('postaddress2')" onkeyup="myProfile.anyChange('postaddress2')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal City</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="50" maxlength="50" name="postcity" value="{/doc/query[@queryName='participant_info']/row/@postcity}"
                           id="postcity" onchange="myProfile.anyChange('postcity')" onkeyup="myProfile.anyChange('postcity')"
                           class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal State</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="25" maxlength="25" name="poststate" value="{/doc/query[@queryName='participant_info']/row/@poststate}"
                            id="poststate" onchange="myProfile.anyChange('poststate')" onkeyup="myProfile.anyChange('poststate')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal Code</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="10" maxlength="10" name="postzip" value="{/doc/query[@queryName='participant_info']/row/@postzip}"
                            id="postzip" onchange="myProfile.anyChange('postzip')" onkeyup="myProfile.anyChange('postzip')"
                            class="userFormINPTXT">
                  </input>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal Country</span>
                </div>
                <div class="col-auto">
                  <input type="text" size="10" maxlength="10" name="postcountry" 
                             value="{/doc/query[@queryName='participant_info']/row/@postcountry}"
                             id="postcountry" onchange="myProfile.anyChange('postcountry')" onkeyup="myProfile.anyChange('postcountry')"
                             class="userFormINPTXT">
                  </input>
                </div>
              </div>
            </fieldset>
          </div>
        </xsl:if>
        <div class="row mt-3">
          <button class="btn btn-primary" type="button" name="submitBTN" id="submitBTN" data-loading-text="Updating..." onclick="myProfile.updateBUTN();">Update</button>
        </div>
        <xsl:if test="$enable_reg_edit!='1'">
          <div id="congo_section">
            <fieldset>
              <div class="row mt-3">
                <legend>Data from Registration System</legend>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Badge ID</span>
                </div>
                <div class="col-auto">
                   <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgeid"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">First Name</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@firstname"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Last Name</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@lastname"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Badge Name</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgename"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Phone Info</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@phone"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Email Address</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@email"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal Address</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postaddress1"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">&#160;</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postaddress2"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal City</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postcity"/>,
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@poststate"/>
                  <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postzip"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-2 bg-secondary">
                  <span class="text-white">Postal Country</span>
                </div>
                <div class="col-auto">
                  <xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postcountry"/>
                </div>
              </div>
              <div class="row">
                <div class="col-auto">
                  <p>
                    Please confirm your contact information.  If it is not correct, please log into <xsl:value-of select="$conName"/>'s
                    <a href="{$regURL}" target="_blank">on-line registration system</a> and correct it there.
                    Please note that the password there is <span style="font-weight: bold">not the same</span> as the one you use
                    in Zambia. This data is downloaded periodically from the registration database, and should be correct within an hour.
                  </p>
                </div>
              </div>
            </fieldset>
          </div>
        </xsl:if>
        </form>
    </div>
</xsl:template>
</xsl:stylesheet>
