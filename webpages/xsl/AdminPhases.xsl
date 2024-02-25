<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Peter Olszowka on 2020-06-01;
    Copyright (c) 2020-2024 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="UpdateMessage" select="''"/>
  <xsl:param name="control" select="''"/>
  <xsl:param name="controliv" select="''"/>
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <xsl:choose>
      <xsl:when test="$UpdateMessage != ''">
        <div class="alert alert-success">
          <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
        </div>
      </xsl:when>
    </xsl:choose>
    <div class="row justify-content-center mt-4 mb-1">
      <h2 class="col col-auto">Current Zambia Phase Status</h2>
    </div>
    <form name="phaseform" class="form-inline form-more-whitespace" method="POST" action="AdminPhases.php">
      <input type="hidden" id="PostCheck" name="PostCheck" value="POST"/>
      <input type="hidden" id="control" name="control" value="{$control}" />
      <input type="hidden" id="controliv" name="controliv" value="{$controliv}" />
      <table id="phase_table" class="table table-striped zambia-table">
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
                  <xsl:attribute name="name">
                    <xsl:text>select_phase_</xsl:text>
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
        <div class="float-end">
          <button class="btn btn-secondary mx-2" type="reset" value="reset" onclick="ResetCol1();">Reset</button>
          <button class="btn btn-primary mx-2" type="submit" value="save" onclick="mysubmit()">Save</button>
        </div>
      </div>
    </form>
  </xsl:template>
</xsl:stylesheet>
