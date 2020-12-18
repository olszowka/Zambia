<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2020-12-15;
	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="UpdateMessage" select="''"/>
  <xsl:param name="control" select="''"/>
  <xsl:param name="controliv" select="''"/>
  <xsl:param name="config" select="''"/>
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <xsl:if test="$UpdateMessage != ''">
      <div class="alert alert-success mt-4">
        <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
      </div>
    </xsl:if>
    <script type="text/javascript">
      var demographics = <xsl:value-of select="$config" disable-output-escaping="yes"/>;
    </script>
    <div class="row justify-content-center mt-4">
      <h4 class="col-auto">Demographics</h4>
    </div>
    <input type="hidden" id="PostCheck" name="PostCheck" value="POST"/>
    <input type="hidden" id="control" name="control" value="{$control}" />
    <input type="hidden" id="controliv" name="controliv" value="{$controliv}" />

      <div id="demographicsconfig"></div>
      
      <div class="row mt-4">
        <div class="col-auto">
          <button class="btn btn-secondary" id="add-demo" name="add-demo" value="new" type="button">Add New</button>
        </div>
        <div class="col-auto">
          <button class="btn btn-secondary" id="resetbtn" name="resetbtn" value="undo" type="button" onclick="FetchDemographics()">Reset</button>
        </div>
        <div class="col-auto">
          <button class="btn btn-primary" id="submitbtn" name="submitbtn" type="save" value="save" onclick="SaveDemographics()">Save</button>
        </div>
        <div id="saving_div" style="display: none;">
          <span style="color:blue">
            <b>Saving...</b>
          </span>
        </div>
      </div>
      <div class="clearboth">
        <p>
          Click demographic name to edit the configuration of that demographic.<br/>
          Drag slider icon to reorder the demographics.<br/>
          Use the Add New button to add a demographic to the table, then click the name from the table to further edit the configuration.
        </p>
      </div>
      
      <div id="general-demo-div" style="display: none;">
        <div id="general-header">
          <h3 class="col-auto">General Configuration</h3>
        </div>
        <div class="row mt-4">
          <div class="col col-2">Name:</div>
          <div class="col col-4">
            <input type="text" id="shortname" maxlength="100" size="50"/>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Description:</div>
          <div class="col col-6">
            <textarea id="description" rows="4" cols="80" maxlength="1024"></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Prompt:</div>
          <div class="col col-4">
            <input type="text" id="prompt" maxlength="512" size="50"/>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Hover:</div>
          <div class="col col-6">
            <textarea id="hover" rows="4" cols="80" maxlength="8192"></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Type:</div>
          <div class="col col-4">
            <select id="typename" name="typename">
              <xsl:for-each select="/doc/query[@queryName='demographictypes']/row">
              <option value="{@shortname}">
                <xsl:attribute name="data-typeid">
                  <xsl:value-of select="@typeid"/>
                </xsl:attribute>
                <xsl:value-of select="@shortname"/>
              </option>
              </xsl:for-each>
            </select>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Required:</div>
          <div class="col col-4">
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="required-1" name="required" value="1" checked="checked"/>
              <label class="form-check-label" for="required-1">Yes</label>
            </div>
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="required-0" name="required" value="0"/>
              <label class="form-check-label" for="required-0">No</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Publish:</div>
          <div class="col col-4">
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="publish-1" name="publish" value="Yes"/>
              <label class="form-check-label" for="publish-1">Yes</label>
            </div>
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="publish-0" name="publish" value="No" checked="checked"/>
              <label class="form-check-label" for="publish-0">No</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Allow user to set privacy:</div>
          <div class="col col-4">
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="privacy_user-1" name="privacy_user" value="Yes"/>
              <label class="form-check-label" for="privacy-1">Yes</label>
            </div>
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="privacy_user-0" name="privacy_user" value="No" checked="checked"/>
              <label class="form-check-label" for="privacy-0">No</label>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Searchable:</div>
          <div class="col col-4">
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="searchable-1" name="searchable" value="Yes"/>
              <label class="form-check-label" for="searchable-1">Yes</label>
            </div>
            <div class="form-check-inline">
              <input class="form-check-input" type="radio" id="searchable-0" name="searchable" value="No" checked="checked"/>
              <label class="form-check-label" for="searchable-0">No</label>
            </div>
          </div>
        </div>
        <div id="asc_desc">
          <div class="row">
            <div class="col col-2">Ascending/Descending:</div>
            <div class="col col-4">
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="ascending-1" name="ascending" value="" checked="checked"/>
                <label class="form-check-label" for="ascending-1">Ascending</label>
              </div>
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="ascending-0" name="ascending" value=""/>
                <label class="form-check-label" for="ascending-0">Descending</label>
              </div>
            </div>
          </div>
        </div>
        <div id="value_range">
          <div class="row">
            <div class="col col-2">Value Range:</div>
            <div class="col col-3">
              Minimum:
              <input type="number" id="min_value" size="10"/>
            </div>
            <div class="col col-3">
              Maximum:
              <input type="number" id="max_value" size="10"/>
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col col-2"></div>
          <div class="col-auto">
            <button class="btn btn-primary" id="add-row" name="add-row" value="addrow" type="button">Add Demographic</button>
          </div>
        </div>
      </div>
  </xsl:template>
</xsl:stylesheet>