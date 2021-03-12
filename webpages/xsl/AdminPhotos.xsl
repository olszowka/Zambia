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
                    <th>Approval Status</th>
                    <th>Badge Name</th>
                    <th><xsl:value-of select="$userIdPrompt"/></th>
                  </tr>
                </thead>
                <tbody>
                  <xsl:for-each select="/doc/query[@queryName='searchParticipants']/row">
                      <tr class="action" id="actionDIV_{@badgeid}" onclick="chooseParticipant('{@jsEscapedBadgeid}', false);">
                        <td class="action" id='lnameSPAN_{@badgeid}'><xsl:value-of select="@lastname"/>, <xsl:value-of select="@firstname"/></td>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        <td class="action" id='statustextSPAN_{@badgeid}'><xsl:value-of select='@statustext'/></td>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        <td class="action" id="bnameSPAN_{@badgeid}"><xsl:value-of select="@badgename"/></td>
                        <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                        <td class="action" id="bidSPAN_{@badgeid}"><xsl:value-of select="@badgeid"/></td>
                        <input type="hidden" id="denialOtherTextHID_{@badgeid}">
                            <xsl:attribute name="value"><xsl:value-of select="@photodenialreasonothertext"/></xsl:attribute>
                        </input>
                        <input type="hidden" id="uploadedphotoHID_{@badgeid}">
                          <xsl:attribute name="value">
                            <xsl:value-of select="@uploadedphotofilename"/>
                          </xsl:attribute>
                        </input>
                        <input type="hidden" id="approvedphotoHID_{@badgeid}">
                          <xsl:attribute name="value">
                            <xsl:value-of select="@approvedphotofilename"/>
                          </xsl:attribute>
                        </input>
                        <input type="hidden" id="photouploadstatusHID_{@badgeid}">
                          <xsl:attribute name="value">
                            <xsl:value-of select="@photouploadstatus"/>
                          </xsl:attribute>
                        </input>
                        <input type="hidden" id="reasontextHID_{@badgeid}">
                          <xsl:attribute name="value">
                            <xsl:value-of select="@reasontext"/>
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
