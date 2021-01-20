<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2020-12-15;
	Copyright (c) 2020 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:param name="UpdateMessage" select="''"/>
  <xsl:param name="control" select="''"/>
  <xsl:param name="controliv" select="''"/>
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <div id="message" class="alert alert-success mt-4" style="display: none;">
      <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
    </div>
    <div id="unsavedWarningModal" class="modal" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Data Not Saved</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&#215;</span>
            </button>
          </div>
          <div class="modal-body" id="unsaved-div">
            <p>
              You have unsaved changes highlighted in the survey question configuration below!
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" id="cancelUnsavedBUTN" class="btn btn-primary" data-dismiss="modal">Cancel</button>
            <button type="button" id="discardUnsavedBUTN" class="btn btn-secondary" onclick="return discardChanges();" >Discard changes</button>
          </div>
        </div>
      </div>
    </div>
    <div class="row justify-content-center mt-4">
      <h4 class="col col-auto">Survey Questions</h4>
    </div>
    <input type="hidden" id="PostCheck" name="PostCheck" value="POST"/>
    <input type="hidden" id="control" name="control" value="{$control}" />
    <input type="hidden" id="controliv" name="controliv" value="{$controliv}" />

      <div id="surveyconfig"></div>
      
      <div class="row mt-4">
        <div class="col col-auto">
          <button class="btn btn-secondary" id="undo" name="undo" value="undo" type="button" onclick="Undo()" disabled="true">Undo</button>
        </div>
        <div class="col col-auto">
          <button class="btn btn-secondary" id="redo" name="redo" value="redo" type="button" onclick="Redo()" disabled="true">Redo</button>
        </div>
        <div class="col col-auto">
          <button class="btn btn-secondary" id="add-question" name="add-question" value="new" type="button">Add New</button>
        </div>
        <div class="col col-auto">
          <button class="btn btn-secondary" id="resetbtn" name="resetbtn" value="undo" type="button" onclick="FetchSurvey()">Reset</button>
        </div>
        <div class="col col-auto">
          <button class="btn btn-primary" id="submitbtn" name="submitbtn" type="save" value="save" onclick="SaveSurvey()">Save</button>
        </div>
        <div class="col col-auto">
          <button class="btn btn-info" id="previewbtn" name="previewbtn" type="button" value="preview" onclick="PreviewSurvey()">Preview Survey</button>
        </div>
        <div id="saving_div" style="display: none;">
          <span style="color:blue">
            <b>Saving...</b>
          </span>
        </div>
      </div>
      <div class="clearboth">
        <p>
          Click question name to edit the configuration of that question.<br/>
          Drag slider icon to reorder the questions.<br/>
          Use the Add New button to add a question to the survey, then click the name from the table to further edit the configuration.
        </p>
      </div>
      
      <div id="general-question-div" style="display: none;">
        <div id="general-header">
          <h3 class="col col-auto">General Configuration</h3>
        </div>
        <input type="hidden" id="questionid"/>
        <div class="row mt-4">
          <div class="col col-2">Name:</div>
          <div class="col col-4">
            <input type="text" id="shortname" maxlength="100" size="50" data-dirty="q" onchange="qfChange(this,false);"/>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Description:</div>
          <div class="col col-6">
            <textarea id="description" rows="4" cols="80" maxlength="1024" data-dirty="q" onchange="qfChange(this,false);"></textarea>
          </div>
        </div>
        <div id="prompt-div">
          <div class="row">
            <div class="col col-2">Prompt:</div>
            <div class="col col-auto" id="prompt-area">
              <input type="text" id="prompt" maxlength="512" size="80"/>
            </div>
          </div>
        </div>
        <div class="row">
          <div id="hover-title" class="col col-2">Hover:</div>
          <div class="col col-auto" id="hover-area">
            <textarea id="hover" rows="4" cols="80" maxlength="8192"></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col col-2">Type:</div>
          <div class="col col-4">
            <select id="typename" name="typename" data-dirty="q">
              <xsl:for-each select="/doc/query[@queryName='questiontypes']/row">
              <option value="{@shortname}">
                <xsl:attribute name="data-typeid">
                  <xsl:value-of select="@typeid"/>
                </xsl:attribute>
                <xsl:attribute name="title">
                  <xsl:value-of select="@description"/>
                </xsl:attribute>
                <xsl:value-of select="@shortname"/>
              </option>
              </xsl:for-each>
            </select>
          </div>
        </div>
        <div id="radio-div">
          <div class="row">
            <div class="col col-2">Display Only:</div>
            <div class="col col-auto" id="display_only">
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="display_only-1" name="display_only" value="Yes" data-dirty="q" onchange="qfChange(this,false);"/><label class="form-check-label" for="display_only-1">Yes</label>
              </div>
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="display_only-0" name="display_only" value="No" checked="checked" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="display_only-0">No</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col col-2">Required:</div>
            <div class="col col-auto" id="required">
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="required-1" name="required" value="1" checked="checked" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="required-1">Yes</label>
              </div>
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="required-0" name="required" value="0" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="required-0">No</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col col-2">Publish:</div>
            <div class="col col-auto" id="publish">
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="publish-1" name="publish" value="Yes" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="publish-1">Yes</label>
              </div>
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="publish-0" name="publish" value="No" checked="checked" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="publish-0">No</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col col-2">Allow user to set privacy:</div>
            <div class="col col-auto" id="privacy_user">
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="privacy_user-1" name="privacy_user" value="Yes" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="privacy-1">Yes</label>
              </div>
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="privacy_user-0" name="privacy_user" value="No" checked="checked" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="privacy-0">No</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col col-2">Searchable:</div>
            <div class="col col-auto" id="searchable">
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="searchable-1" name="searchable" value="Yes" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="searchable-1">Yes</label>
              </div>
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="searchable-0" name="searchable" value="No" checked="checked" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="searchable-0">No</label>
              </div>
            </div>
          </div>
        </div>
        <div id="asc_desc">
          <div class="row">
            <div class="col col-2">Ascending/Descending:</div>
            <div class="col col-auto" id="ascending">
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="ascending-1" name="ascending" value="" checked="checked" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="ascending-1">Ascending</label>
              </div>
              <div class="form-check-inline">
                <input class="form-check-input" type="radio" id="ascending-0" name="ascending" value="" data-dirty="q" onchange="qfChange(this,false);"/>
                <label class="form-check-label" for="ascending-0">Descending</label>
              </div>
            </div>
          </div>
        </div>
        <div id="value_range">
          <div class="row">
            <div id="range-label" class="col col-2">Value Range:</div>
            <div class="col col-3">
              <span id="min-prompt">Minimum:</span>&#160;
              <input type="number" id="min_value" size="10" data-dirty="q" onchange="qfChange(this,false);"/>
            </div>
            <div id="max-div" class="col col-3">
              <span id="max-prompt">Maximum:</span>&#160;
              <input type="number" id="max_value" size="10" data-dirty="q" onchange="qfChange(this,false);"/>
            </div>
          </div>
        </div>
        <div id="optiontable-div" style="display: none;">
          <br/>
          <div id="option-header">
            <h4 class="col col-auto">Options</h4>
          </div>
          <div id="option-table"></div>
        </div>
        <div id="preview"></div>
        <div class="row mt-4">
          <div id="add-option-div" class="col col-auto" style="display: none;">
            <div class="row">
              <div class="col col-auto">
                <button class="btn btn-secondary" id="optundo" name="optundo" value="optundo" type="button" onclick="OptUndo()" disabled="true">Undo</button>
              </div>
              <div class="col col-auto">
                <button class="btn btn-secondary" id="optredo" name="optredo" value="optredo" type="button" onclick="OptRedo()" disabled="true">Redo</button>
              </div>
              <div class="col col-auto">
                <button class="btn btn-secondary" id="add-option" name="add-option" value="addoption" type="button">Add Option Row</button>
              </div>
            </div>
          </div>
          <div class="col col-auto">
            <button class="btn btn-secondary" id="refresh" name="refresh" value="refresh" type="button" onclick="RefreshPreview()">Refresh Preview</button>
          </div>
          <div class="col col-auto">
            <button class="btn btn-primary" id="add-row" name="add-row" value="addrow" type="button">Add Question</button>
          </div>
        </div>
        <div id="optlegend-div" class="clearboth">
          <p>
            Click Add Option Row to add an option to this question.<br/>
            Drag slider icon to reorder the options for this question.<br/>
            Click in the table fields to edit the values of the option.<br/>
          </p>
        </div>
        <div class="clearboth">
          <p>
            Click Add/Update Question Table to update the question table in this page, clicking "Save" (at the top) is needed to save the changes permanently.
          </p>
        </div>
      </div>
  </xsl:template>
</xsl:stylesheet>