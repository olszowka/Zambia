<?xml version='1.0'?>
<!-- Copyright (c) 2011-2019 Peter Olszowka. All rights reserved. See copyright document for more details.-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:param name="control" select="''"/>
  <xsl:param name="controliv" select="''"/>
  <xsl:param name="UpdateMessage" select="''"/>
  <xsl:param name="MessageAlertType" select="'success'"/>
  <xsl:template match="/">
    <div id="message" alert-dismissible="true" fade="true" show="true">
      <xsl:attribute name="class">
        <xsl:text>alert mt-4 alert-</xsl:text>
        <xsl:value-of select="$MessageAlertType"/>
      </xsl:attribute>
      <xsl:if test="not($UpdateMessage) or ($UpdateMessage = '')">
        <xsl:attribute name="style">
          <xsl:text>display: none</xsl:text>
        </xsl:attribute>
      </xsl:if>
      <button type="button" class="close" data-dismiss="alert">&#215;</button>
      <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
    </div>
    <div class="row mt-2">
      <div class="col col-12">
        <p id="invite-participants-intro">Use this tool to put sessions marked "invited guests only" on a participant's interest list.</p>
      </div>
    </div>
    <form name="invform" method="POST" action="InviteParticipants.php">
      <div class="row mt-2">
        <div class="col col-5">
          <label for="participant-select">Select Participant:&#160;</label>
          <select id="participant-select" name="selpart">
            <option value="" selected="selected" disabled="true">Select Participant</option>
            <xsl:for-each select="/doc/query[@queryName='participants']/row">
              <option>
                <xsl:attribute name="value">
                  <xsl:value-of select="@badgeid"/>
                </xsl:attribute>
                <xsl:value-of select="@name"/>
              </option>
            </xsl:for-each>
          </select>
        </div>
        <div class="col col-5">
          <label for="session-select">Select Session:&#160;</label>
          <select id="session-select" name="selsess">
            <option value="0" selected="selected" disabled="true">Select Session</option>
            <xsl:for-each select="/doc/query[@queryName='sessions']/row">
              <option>
                <xsl:attribute name="value">
                  <xsl:value-of select="@sessionid"/>
                </xsl:attribute>
                <xsl:value-of select="@title"/>
              </option>
            </xsl:for-each>
          </select>
        </div>
      </div>
      <div class="row mt-4">
        <div class="col col-auto">
          <button class="btn btn-primary" type="submit" name="Invite" >Invite</button>
        </div>
      </div>
    </form>
  </xsl:template>
</xsl:stylesheet>
