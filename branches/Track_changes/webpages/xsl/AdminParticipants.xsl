<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="not(/doc/query[@queryName='searchParticipants']/row)">
            <div class="alert-info">No matching results found</div>
            </xsl:when>
            <xsl:otherwise>
              <table class="table table-condensed table-hover">
                <thead>
                  <tr>
                    <th>Last Name</th>
                    <th>Pubs Name</th>
                    <th>Badge Name</th>
                    <th>Badge Number</th>
                  </tr>
                </thead>
                <tbody>
                  <xsl:for-each select="/doc/query[@queryName='searchParticipants']/row">
                      <tr class="action" id="actionDIV_{@badgeid}" onclick="chooseParticipant('{@badgeid}', false);">
                          <td class="action" id="lnameSPAN_{@badgeid}"><xsl:value-of select="@lastname"/>, <xsl:value-of select="@firstname"/></td>
                          <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                          <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                          <td class="actionB" id="pnameSPAN_{@badgeid}"><xsl:value-of select="@pubsname"/></td>
                          <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>                
                          <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                          <td class="action" id="bnameSPAN_{@badgeid}"><xsl:value-of select="@badgename"/></td>
                          <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
                          <td class="action" id="bidSPAN_{@badgeid}"><xsl:value-of select="@badgeid"/></td>
                          <input type="hidden" id="interestedHID_{@badgeid}">
                              <xsl:attribute name="value"><xsl:value-of select="@interested"/></xsl:attribute>
                          </input>
                          <input type="hidden" id="bioHID_{@badgeid}">
                              <xsl:attribute name="value"><xsl:value-of select="@bio"/></xsl:attribute>
                          </input>
                          <input type="hidden" id="staffnotesHID_{@badgeid}">
                              <xsl:attribute name="value"><xsl:value-of select="@staff_notes"/></xsl:attribute>
                          </input>
                      </tr>
                  </xsl:for-each>
                </tbody>
              </table>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
