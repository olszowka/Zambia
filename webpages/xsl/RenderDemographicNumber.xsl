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
  <xsl:param name="size" select="10"/>
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
            <xsl:attribute name="title">
              <xsl:value-of select="$hover"/>
            </xsl:attribute>
            <xsl:value-of select="$prompt"/>
            <xsl:if test="$required = 1">
              <span style="color: #990012;">
                <xsl:text>*</xsl:text>
              </span>
            </xsl:if>
          </span>
        </div>
        <div class="col col-3">
          <input type="number">
            <xsl:attribute name="id">
              <xsl:value-of select="$name"/>
              <xsl:text>-input</xsl:text>
            </xsl:attribute>
            <xsl:attribute name="name">
              <xsl:value-of select="$name"/>
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