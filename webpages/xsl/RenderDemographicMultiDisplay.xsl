<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2020-12-20;
	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="name" select="''"/>
  <xsl:param name="prompt" select="''"/>
  <xsl:param name="hover" select="''"/>
  <xsl:param name="required" select="0"/>
  <xsl:param name="typename" select="''"/>
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
            <xsl:value-of select="$prompt"/>
            <xsl:if test="$required = 1">
              <span style="color: #990012;">
                <xsl:text disable-output-escaping="yes">*</xsl:text>
              </span>
            </xsl:if>
          </span>
        </div>
        <div class="border border-dark">
          <div class="form-row">
            <div class="col col-auto">
              <label style="margin-left: 10px;">
                <xsl:attribute name="for">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-source</xsl:text>
                </xsl:attribute>
                Possible:
              </label>
              <select class="form-control" style="height: 120px; width: 250px">
                <xsl:attribute name="id">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-source</xsl:text>
                </xsl:attribute>
                <xsl:attribute name="name">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                </xsl:attribute>
                <xsl:attribute name="multiple"/>
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
            <div class="col col-auto" style="vertical-align: middle;">
              <button class="btn btn-light" type="button" style="margin-bottom: 10px; margin-top: 50px;">
                <xsl:attribute name="name">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-right</xsl:text>
                </xsl:attribute>
                <xsl:attribute name="onclick">
                  <xsl:text>fadditems(document.getElementById('</xsl:text>
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-source'),document.getElementById('</xsl:text>
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-dest'));</xsl:text>
                </xsl:attribute>
                &#160;&#x21D2;&#160;
              </button>
              <br/>
              <button class="btn btn-light" type="button">
                <xsl:attribute name="name">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-right</xsl:text>
                </xsl:attribute>
                <xsl:attribute name="onclick">
                  <xsl:text>fdropitems(document.getElementById('</xsl:text>
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-source'),document.getElementById('</xsl:text>
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-dest'));</xsl:text>
                </xsl:attribute>
                &#160;&#x21D0;&#160;
              </button>
            </div>
            <div class="col col-auto">
              <label style="margin-left: 10px;">
                <xsl:attribute name="for">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-dest</xsl:text>
                </xsl:attribute>
                Selected:
              </label>
              <select class="form-control" style="height: 120px; width: 250px;">
                <xsl:attribute name="id">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                  <xsl:text>-dest</xsl:text>
                </xsl:attribute>
                <xsl:attribute name="name">
                  <xsl:value-of select="translate($name, ' ', '_')"/>
                </xsl:attribute>
                <xsl:attribute name="multiple"/>
              </select>
            </div>
          </div>
        </div>
      </div>
    </div>
  </xsl:template>
</xsl:stylesheet>