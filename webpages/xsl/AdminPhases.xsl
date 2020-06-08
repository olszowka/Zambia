<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Peter Olszowka on 2015-10-16;
	Copyright (c) 2011-2019 The Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
  <script type="text/javascript">
    function setColor(item, element, match)
    {
    var options = element.options;
    var sel = options.item(element.selectedIndex).defaultSelected;
    var me = document.getElementById(match)
    var defBG = window.getComputedStyle(me, null).getPropertyValue("background-color").toString();
    if (sel == false)
    {
    $(item).css('background', '#FF7F7F');
    }
    else
    {
    $(item).css('background', defBG);
    }
    }
    function ResetCol1()
    {
    var table = document.getElementById("phase_table");
    for (var i = 0, row; row = table.rows[i]; i++) {
        var col = row.cells[0];
        var bg = row.cells[3];
        var defBG = window.getComputedStyle(bg, null).getPropertyValue("background-color").toString();
        col.style.backgroundColor = defBG;
        }
    }
  </script>
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
                    <xsl:text>setColor('#phase_id_num_</xsl:text>
                    <xsl:value-of select="@phaseid"/>
                    <xsl:text>', this, 'phase_name_</xsl:text>
                    <xsl:value-of select="@phaseid"/>
                    <xsl:text>');</xsl:text>
                  </xsl:attribute>
                  <xsl:attribute name="defaultValue">
                    <xsl:value-of select="@current"/>
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