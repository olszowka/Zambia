<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2020-010-28
	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="UpdateMessage" select="''"/>
  <xsl:param name="control" select="''"/>
  <xsl:param name="controliv" select="''"/>
  <xsl:param name="new_badgeid" select="''"/>
  <xsl:param name="firstname" select="''"/>
  <xsl:param name="lastname" select="''"/>
  <xsl:param name="badgename" select="''"/>
 <xsl:param name="pubsname" select="''"/>
  <xsl:param name="phone" select="''"/>
  <xsl:param name="email" select="''"/>
  <xsl:param name="postaddress1" select="''"/>
  <xsl:param name="postaddress2" select="''"/>
  <xsl:param name="postcity" select="''"/>
  <xsl:param name="poststate" select="''"/>
  <xsl:param name="postzip" select="''"/>
  <xsl:param name="postcountry" select="''"/>
  <xsl:param name="selected" select="''"/>
  <xsl:param name="override" select="''"/>
    
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <xsl:if test="$UpdateMessage != ''">
      <div class="alert alert-success mt-4">
        <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
      </div>
    </xsl:if>
    <div class="row justify-content-center mt-4">
      <h4 class="col-auto">Add New Zambia User</h4>
    </div>
    <form name="adduserform" method="POST" action="AddZambiaUser.php">
    <input type="hidden" id="PostCheck" name="PostCheck" value="POST"/>
    <input type="hidden" id="control" name="control" value="{$control}" />
    <input type="hidden" id="controliv" name="controliv" value="{$controliv}" />

    <div class="form-row">
      <div class="col-2">
        <label for="badgeid">Proposed Badgeid:</label>
      </div>
      <div class="controls">
        <input type="text" size="20" name="badgeid" value="{$new_badgeid}" id="badgeid" readonly="readonly" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="firstname">First Name:</label>
      </div>
      <div class="controls">
        <input type="text" size="30" name="firstname" value="{$firstname}" id="firstname" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="lastname">Last Name:</label>
      </div>
      <div class="controls">
        <input type="text" size="40" name="lastname" value="{$lastname}" id="lastname" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="badgename">Badge Name:</label>
      </div>
      <div class="controls">
        <input type="text" size="50" name="badgename" value="{$badgename}" id="badgename" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="pubsname">Publications Name:</label>
      </div>
      <div class="controls">
        <input type="text" size="50" name="pubsname" value="{$pubsname}" id="pubsname" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="phone">Phone Number:</label>
      </div>
      <div class="controls">
        <input type="text" size="100" name="phone" value="{$phone}" id="phone" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="email">Email Address:</label>
      </div>
      <div class="controls">
        <input type="text" size="100" name="email" value="{$email}" id="email" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="postaddress1">Postal Address:</label>
      </div>
      <div class="controls">
        <input type="text" size="100" name="postaddress1" value="{$postaddress1}" id="postaddress1" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="postaddress2"></label>
      </div>
      <div class="controls">
        <input type="text" size="100" name="postaddress2" value="{$postaddress2}" id="postaddress2" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="postcity">City: </label>
      </div>
      <div class="controls">
        <input type="text" size="50" name="postcity" value="{$postcity}" id="postcity" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="poststate">State:</label>
      </div>
      <div class="controls">
        <input type="text" size="25" name="poststate" value="{$poststate}" id="poststate" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="postzip">Postal Code:</label>
      </div>
      <div class="controls">
        <input type="text" size="10" name="postzip" value="{$postzip}" id="postzip" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="postcountry">Country:</label>
      </div>
      <div class="controls">
        <input type="text" size="25" name="postcountry" value="{$postcountry}" id="postcountry" class="userFormINPTXT"></input>
      </div>
    </div>
    <div class="form-row">
      <div class="col-2">
        <label for="regtype">Registration Type:</label>
      </div>
      <div class="controls">
        <select id="regtype" name="regtype" class="span4" style="max-width:400px;">
          <xsl:for-each select="/doc/query[@queryName='regtypes']/row">
            <option value="{@regtype}">
              <xsl:if test="@regtype = $selected">
                <xsl:attribute name="selected">selected</xsl:attribute>
              </xsl:if>
            <xsl:value-of select="@regtype"/> - <xsl:value-of select="@message"/>
            </option>
          </xsl:for-each>
        </select>
      </div>
    </div>
    <xsl:if test="$override != ''">
      <div class="form-row">
        <div class="col-2">
          <label for="regtype">Override and Force Add:</label>
        </div>
        <div class="controls">
          <select id="override" name="override" class="span4">
            <option value="0">No</option>
            <option value="1">YES</option>
          </select>
        </div>
      </div>
    </xsl:if>
    <div class="row justify-content-center mt-4">
      <div class="col-auto">
        <button class="btn btn-secondary" id="resetbtn" name="resetbtn" value="undo" type="button">Reset</button>
      </div>
        <div class="col-auto">
          <button class="btn btn-primary" id="submitbtn" name="submitbtn" type="submit" value="save" onclick="AddUser()">Add</button>
        </div>
    </div>
  </form>
  </xsl:template>
</xsl:stylesheet>