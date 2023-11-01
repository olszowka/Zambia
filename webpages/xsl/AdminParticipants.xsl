<?xml version='1.0'?>
<!-- Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:param name="userIdPrompt" select="'Badge ID'" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="not(/doc/query[@queryName='searchParticipants']/row)">
            <div class="alert-info">No matching results found</div>
            </xsl:when>
            <xsl:otherwise>
              <table class="table table-condensed table-hover">
                <thead>
                  <tr>
                    <th>Last, First Name</th>
                    <th>Pubs Name</th>
                    <th>Badge Name</th>
                    <th><xsl:value-of select="$userIdPrompt"/></th>
                  </tr>
                </thead>
                <tbody>
                  <xsl:for-each select="/doc/query[@queryName='searchParticipants']/row">
                      <tr class="action" id="actionDIV_{@badgeid}">
                          <td class="action" id='lnameSPAN_{@badgeid}' onclick="chooseParticipant('{@jsEscapedBadgeid}', false);"><xsl:value-of select="@lastname"/>, <xsl:value-of select="@firstname"/></td>
                          <td class="actionB" id="pnameSPAN_{@badgeid}" onclick="chooseParticipant('{@jsEscapedBadgeid}', false);"><xsl:value-of select="@pubsname"/></td>
                          <td class="action" id="bnameSPAN_{@badgeid}" onclick="chooseParticipant('{@jsEscapedBadgeid}', false);"><xsl:value-of select="@badgename"/></td>
                          <td class="action" id="bidSPAN_{@badgeid}" onclick="chooseParticipant('{@jsEscapedBadgeid}', false);"><xsl:value-of select="@badgeid"/></td>
                          <td><button type="button" class="btn btn-primary btn-small" onclick="window.open('StaffViewSurveyResults.php?badgeid={@jsEscapedBadgeid}', '_blank');">View Survey Results</button></td>
                          <input type="hidden" id="interestedHID_{@badgeid}">
                              <xsl:attribute name="value"><xsl:value-of select="@interested"/></xsl:attribute>
                          </input>
                          <input type="hidden" id="htmlbioHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@htmlbio"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="bioHID_{@badgeid}">
                              <xsl:attribute name="value"><xsl:value-of select="@bio"/></xsl:attribute>
                          </input>
                          <input type="hidden" id="staffnotesHID_{@badgeid}">
                              <xsl:attribute name="value"><xsl:value-of select="@staff_notes"/></xsl:attribute>
                          </input>
                          <input type="hidden" id="lastnameHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@lastname"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="firstnameHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@firstname"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="phoneHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@phone"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="emailHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@email"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="postaddress1HID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@postaddress1"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="postaddress2HID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@postaddress2"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="postcityHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@postcity"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="poststateHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@poststate"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="postzipHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@postzip"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="postcountryHID_{@badgeid}">
                            <xsl:attribute name="value">
                              <xsl:value-of select="@postcountry"/>
                            </xsl:attribute>
                          </input>
                          <input type="hidden" id="regmessageHID_{@badgeid}">
                              <xsl:attribute name="value">
                                  <xsl:value-of select="@regmessage"/>
                              </xsl:attribute>
                          </input>
                      </tr>
                  </xsl:for-each>
                </tbody>
              </table>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
