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
    <div class="alert-block" id="resultBoxDIV">
      <span class="beforeResult" id="resultBoxSPAN">Result messages will appear here.
      </span>
    </div>
    <div class="row-fluid">
      <div class="span12">
        <form name="partform" class="form-horizontal" method="POST" action="SubmitMyContact.php">
          <fieldset>
          <legend>Permissions</legend>
            <div class="control-group">
              <label for="interested" class="control-label nowidth">I am interested and able to participate in programming for <xsl:value-of select="$conName"/>:</label>
              <div class="controls">
                <select id="interested" name="interested" class="yesno span2" onchange="myProfile.anyChange('interested');"
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
                <div class="control-group">
                  <label for="share_email" class="control-label nowidth">I give permission for <xsl:value-of select="$conName"/> to share my email address with other participants:</label>
                  <div class="controls">
                    <select id="share_email" name="share_email" class="span2" onchange="myProfile.anyChange('share_email')"
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
                        Yes</option>
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
            <xsl:when test="/doc/options/@enable_bestway_question">
              <label for="bestway">Preferred mode of contact</label>
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
            </xsl:when>
            <xsl:otherwise>
              <input name="bestway" id="bestway" type="hidden" value="{$bestway}"/>
            </xsl:otherwise>
          </xsl:choose>
          <fieldset>
            <div class="control-group" id="passGroup">
              <label for="password" class="control-label xnowidth">New Password:</label>
              <div class="controls">
                <input type="password" size="10" name="password" id="password" onchange="myProfile.anyChange('password')" onkeyup="myProfile.anyChange('password')"/>
              </div>
              <label for="cpassword" class="control-label xnowidth">Confirm Password:</label>
              <div class="controls">
                <input type="password" size="10" name="cpassword" id="cpassword"  onchange="myProfile.anyChange('cpassword')" onkeyup="myProfile.anyChange('cpassword')"/>
                <span id="badPassword" class="help-inline"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>Passwords don't match!</span>
              </div>
            </div>
          </fieldset>
          <legend>Biography</legend>
          <div class="control-group">
          <xsl:if test="$enableBioEdit!='1'">
              <h3 class="noteWLfPad">At this time, you may not edit either your biography or your name for publication. They have already gone to print.</h3>
          </xsl:if>
              <label for="pubsname" class="control-label nowidth">Your name as you wish to have it published:</label>
              <div class="controls">
                  <input type="text" size="20" name="pubsname" value="{/doc/query[@queryName='participant_info']/row/@pubsname}"
                      id="pubsname" onchange="myProfile.anyChange('pubsname')" onkeyup="myProfile.anyChange('pubsname')"
                      class="userFormINPTXT">
                      <xsl:if test="$enableBioEdit!='1'">
                          <xsl:attribute name="readonly">readonly</xsl:attribute>
                      </xsl:if>
                  </input>
              </div>
    			</div>
          <div class="control-group" id="bioGroup">
            <label for="bio">Your biography (<xsl:value-of select="/doc/options/@maxBioLen"/> characters or fewer including spaces):</label>
            <div>
              <textarea class="span12" rows="5" cols="72" name="bio" id="bioTXTA" onchange="myProfile.anyChange('bioTXTA')"
                  onkeyup="myProfile.anyChange('bioTXTA')"><xsl:choose>
                  <xsl:when test="$enableBioEdit!='1'">
                      <xsl:attribute name="readonly">readonly</xsl:attribute>
                      <xsl:attribute name="class">span12 userFormTXT readonly</xsl:attribute>
                  </xsl:when>
                  <xsl:otherwise>
                      <xsl:attribute name="class">span12 userFormTXT</xsl:attribute>
                  </xsl:otherwise>
                  </xsl:choose><xsl:value-of
                  select="/doc/query[@queryName='participant_info']/row/@bio"/>
              </textarea>
              <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text><span id="badBio" class="help-inline">Biography is too long!</span>
            </div>
            <xsl:if test="$bioNote">
                <div class="note"><xsl:value-of select="$bioNote" disable-output-escaping="yes"/></div>
            </xsl:if>
          </div>
          <xsl:if test="/doc/query[@queryName='credentials']/row">
          		<legend>Professions</legend>
              <div class="control-group">
                <label>Please indicate if you are any of the following:</label>
                <div>
                  <xsl:for-each select="/doc/query[@queryName='credentials']/row">
                      <xsl:sort select="@display_order" data-type="number"/>
                      <label class="checkbox">
                          <xsl:value-of select="@credentialname"/>
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
                      </label>
                  </xsl:for-each>
                </div>
              </div>
          </xsl:if>
            <button class="btn btn-primary" type="button" name="submitBTN" id="submitBTN" data-loading-text="Updating..." onclick="myProfile.updateBUTN();">Update</button>
            <div id="congo_section">
        		  <fieldset>
          			<legend>Congo Data</legend>
          			<div class="row-fluid">
            			<div class="pull-left span5">
                    <div class="congo_table">
                        <div class="congo_data row-fluid">
                            <span class="label span4">Badge ID</span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgeid"/></span>
                        </div>
                        <div class="congo_data row-fluid">
                            <span class="label span4">First Name</span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@firstname"/></span>
                        </div>
                        <div class="congo_data row-fluid">
                            <span class="label span4">Last Name</span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@lastname"/></span>
                        </div>
                        <div class="congo_data row-fluid">
                            <span class="label span4">Badge Name</span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@badgename"/></span>
                        </div>
                        <div class="congo_data row-fluid">
                            <span class="label span4">Phone Info</span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@phone"/></span>
                        </div>
                        <div class="congo_data row-fluid">
                            <span class="label span4">Email Address</span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@email"/></span>
                        </div>
                        <div class="congo_data row-fluid">
                            <span class="label span4">Postal Address</span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postaddress1"/></span>
                        </div>
                        <xsl:if test="/doc/query[@queryName='participant_info']/row/@postaddress2">
                            <div class="congo_data row-fluid">
                                <span class="label span4"><xsl:text disable-output-escaping="yes">Postal Address</xsl:text></span>
                                <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postaddress2"/></span>
                            </div>
                        </xsl:if>
                        <div class="congo_data row-fluid">
                            <span class="label span4"><xsl:text disable-output-escaping="yes">Postal City</xsl:text></span>
                            <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postcity"/>, <xsl:value-of
                                select="/doc/query[@queryName='participant_info']/row/@poststate"/> <xsl:value-of
                                select="/doc/query[@queryName='participant_info']/row/@postzip"/></span>
                        </div>
                        <xsl:if test="/doc/query[@queryName='participant_info']/row/@postcountry">
                            <div class="congo_data row-fluid">
                                <span class="label span4"><xsl:text disable-output-escaping="yes">Postal Country</xsl:text></span>
                                <span class="value span7"><xsl:value-of select="/doc/query[@queryName='participant_info']/row/@postcountry"/></span>
                            </div>
                        </xsl:if>
                    </div>
                  </div>
                  <div class="pull-left span7">
                    <p class="congo_table">Please confirm your contact information.  If it is not correct, please log into Arisia's
                        <a HREF="http://arisia.stonekeep.com" target="_blank">on-line registration system</a> and correct it there.
                        Please note that the password there is <span style="font-weight: bold">not the same</span> as the one you use
                        in Zambia. This data is downloaded periodically from the registration database, and should be correct within an hour.</p>
                  </div>
                </div>
        		  </fieldset>
            </div>
        </form>
      </div>
    </div>
    <script type="text/javascript">
      $(document).ready(
        function() {
        }
      );
    </script>
</xsl:template>
</xsl:stylesheet>
