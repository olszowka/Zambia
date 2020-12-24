<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2020-12-23;
	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="name" select="''"/>
  <xsl:param name="prompt" select="''"/>
  <xsl:param name="hover" select="''"/>
  <xsl:param name="required" select="0"/>
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <div>
      <xsl:attribute name="id">
        <xsl:value-of select="$name"/>
      </xsl:attribute>
      <div class="row mt-4">
        <div class="col col-3">
          <span>
            <xsl:attribute name="id">
              <xsl:value-of select="$name"/>
              <xsl:text>-prompt</xsl:text>
            </xsl:attribute>
            <xsl:value-of select="$prompt"/>
            <xsl:if test="$required = 1">
              <span style="color: #990012;">
                <xsl:text disable-output-escaping="yes">*</xsl:text>
              </span>
            </xsl:if>
          </span>
        </div>
        <div class="col col-auto">
          <select>
            <xsl:attribute name="id">
              <xsl:value-of select="$name"/>
              <xsl:text>-month</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="name">
              <xsl:value-of select="$name"/>
            </xsl:attribute>
            <xsl:for-each select="/doc/query[@queryname='options']/row">
              <option value="{@value}">
                <xsl:attribute name="title">
                  <xsl:value-of select="@optionhover"/>
                </xsl:attribute>
                <xsl:value-of select="@optionshort"/>
              </option>
            </xsl:for-each>
          </select>
        </div>
      <div class="col col-auto">
          <select>
            <xsl:attribute name="id">
              <xsl:value-of select="$name"/>
              <xsl:text>-year</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="name">
              <xsl:value-of select="$name"/>
            </xsl:attribute>
            <xsl:for-each select="/doc/query[@queryname='year']/row">
              <option value="{@value}">
                <xsl:value-of select="@value"/>
              </option>
            </xsl:for-each>
          </select>
        </div>
      </div>
    </div>
  </xsl:template>
</xsl:stylesheet>