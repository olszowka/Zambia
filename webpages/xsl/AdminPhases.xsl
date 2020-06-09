<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2015-10-16;
	Copyright (c) 2011-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <form name="phaseform" class="form-inline form-more-whitespace" method="POST" action="SubmitAdminPhases.php">
      <table id="phase_table" class="table table-condensed table-striped">
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
                <xsl:attribute name="id">
                  <xsl:text>phase_id_num_</xsl:text>
                  <xsl:value-of select="@phaseid"/>
                </xsl:attribute>
                <xsl:value-of select="@phaseid"/>
              </td>
              <td style="width: 100px">
                <select style="width: 100%">
                  <xsl:attribute name="id">
                    <xsl:text>phase_id_</xsl:text>
                    <xsl:value-of select="@phaseid"/>
                  </xsl:attribute>
                  <xsl:attribute name="onchange">
                    <xsl:text>ChangePhase(</xsl:text>
                    <xsl:value-of select="@phaseid"/>
                    <xsl:text>, this);</xsl:text>
                  </xsl:attribute>
                  <option value="0">
                    <xsl:if test="@current = 0">
                      <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>
                    Inactive
                  </option>
                  <option value="1">
                    <xsl:if test="@current = 1">
                      <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>
                    Active
                  </option>
                </select>
              </td>
              <td>
                <xsl:attribute name="id">
                  <xsl:text>phase_name_</xsl:text>
                  <xsl:value-of select="@phaseid"/>
                </xsl:attribute>
                <xsl:value-of select="@phasename"/>
              </td>
              <td>
                <xsl:value-of select="@notes"/>
              </td>
            </tr>
          </xsl:for-each>
        </tbody>
      </table>
      <div id="buttonBox" class="clearfix">
        <div class="pull-right">
          <button class="btn" type="reset" value="reset" onclick="ResetCol1();">Reset</button>
          <button class="btn btn-primary" type="submit" value="save" onclick="mysubmit()">Save</button>
        </div>
      </div>
    </form>
  </xsl:template>
</xsl:stylesheet>