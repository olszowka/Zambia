<?xml version='1.0'?>
<!-- Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:param name="userIdPrompt" select="'Badge ID'" />
    <xsl:template match="/">
        <xsl:choose>
            <xsl:when test="not(/doc/query[@queryName='searchReg']/row)">
            <div class="alert-info">No matching results found</div>
            </xsl:when>
            <xsl:otherwise>
              <table class="table table-condensed table-hover">
                <thead>
                  <tr>
                    <th>Import</th>
                    <th>Badge ID</th>
                    <th>Last, First Name</th>
                    <th>Badge Name</th>
                    <th>Email</th>
                    <th>City, State, Zip</th>
                  </tr>
                </thead>
                <tbody>
                  <xsl:for-each select="/doc/query[@queryName='searchReg']/row">
                      <tr class="action" id="actionDIV_{@id}">
                        <td class="action">
                          <input type="checkbox" class="id-chk mycontrol" id="importBOX_{@id}" value="${@id}"></input>
                        </td>
                        <td><xsl:value-of select="@id"/></td>
                        <td><xsl:value-of select="@last_name"/>, <xsl:value-of select="@first_name"/></td>
                        <td><xsl:value-of select="@badge_name"/></td>
                        <td><xsl:value-of select="@email_address"/></td>
                        <td>
                          <xsl:value-of select="@city"/>, <xsl:value-of select="@state"/> <xsl:value-of select="@zip"/>
                        </td>
                      </tr>
                  </xsl:for-each>
                </tbody>
              </table>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>
