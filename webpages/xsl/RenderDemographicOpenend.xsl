<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2020-12-20;
	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="name" select="''"/>
  <xsl:param name="prompt" select="''"/>
  <xsl:param name="hover" select="''"/>
  <xsl:param name="max" select="''"/>
  <xsl:param name="size" select="50"/>
  <xsl:param name="required" select="0"/>
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <div>
      <xsl:attribute name="id">
        <xsl:value-of select="translate($name, ' ', '_')"/>
      </xsl:attribute>
      <div class="row mt-4">
        <div class="col col-3">
          <span>
            <xsl:attribute name="id">
              <xsl:value-of select="translate($name, ' ', '_')"/>
              <xsl:text>-prompt</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="title">
              <xsl:text>Placeholder</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="data-toggle">
              <xsl:text>tooltip</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="data-placement">
              <xsl:text>right</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="data-html">
              <xsl:text>true</xsl:text>
            </xsl:attribute>
            <xsl:value-of select="$prompt" disable-output-escaping="yes"/>
            <xsl:if test="$required = 1">
              <span style="color: #990012;">
                <xsl:text>*</xsl:text>
              </span>
            </xsl:if>
          </span>
        </div>
        <div class="col col-9">
          <input type="text">
            <xsl:attribute name="id">
              <xsl:value-of select="translate($name, ' ', '_')"/>
              <xsl:text>-input</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="name">
              <xsl:value-of select="translate($name, ' ', '_')"/>
            </xsl:attribute>
            <xsl:attribute name="maxlength">
              <xsl:value-of select="$max"/>
            </xsl:attribute>
            <xsl:attribute name="size">
              <xsl:value-of select="$size"/>
            </xsl:attribute>
          </input>
        </div>
      </div>
    </div>
  </xsl:template>
</xsl:stylesheet>