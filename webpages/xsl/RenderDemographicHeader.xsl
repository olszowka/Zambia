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
        <div class="col col-12">
          <h4>
            <xsl:value-of select="$prompt"/>
          </h4>
          <xsl:value-of select="$hover" disable-output-escaping="yes"/>
        </div>
      </div>
    </div>
  </xsl:template>
</xsl:stylesheet>