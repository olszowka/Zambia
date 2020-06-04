<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2015-10-16;
	Copyright (c) 2011-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output encoding="UTF-8" indent="yes" method="html" />
	<xsl:template match="/">
    <table class="table table-condensed table-hover">
      <thead>
      <tr>
        <th class="y1">ID</th>
        <th style="width=100px" class="y2">Current Status</th>
        <th class="y3">Phase Name</th>
        <th class="y4">Notes</th>
      </tr>
      </thead>
      <tbody>
      <xsl:for-each select="/doc/query[@queryName='phase_info']/row">
        <tr>
          <td>
            <xsl:value-of select="@phaseid"/>
          </td>
          <td width="100">
            <select style="width: 100px">
              <xsl:attribute name="id">
                <xsl:text>phase_id_</xsl:text><xsl:value-of select="@phaseid"/></xsl:attribute>
              <option value="0">
                <xsl:if test="@current = 0">
                  <xsl:attribute name="selected"></xsl:attribute>
                </xsl:if> Inactive
              </option>
              <option value="1">
                <xsl:if test="@current = 1">
                  <xsl:attribute name="selected"></xsl:attribute>
                </xsl:if> Active
              </option>
            </select>
          </td>
          <td>
            <xsl:value-of select="@phasename"/>
          </td>
          <td>
            <xsl:value-of select="@notes"/>
          </td>
        </tr>
    </xsl:for-each>
      </tbody>
    </table>
	</xsl:template>
</xsl:stylesheet>
