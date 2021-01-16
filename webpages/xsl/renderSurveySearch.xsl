<?xml version="1.0" encoding="UTF-8" ?>
<!--
	Created by Syd Weinstein on 2021-01-12;
	Copyright (c) 2021 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output encoding="UTF-8" indent="yes" method="html" />
  <xsl:template match="/">
    <div id="surveysearch-div">
      <div class="row mt-4">
        <div class="col col-12">
          <h3 align="center">Filter Participants by Survey Responses</h3>
        </div>
      </div>
      <div class="row mt-2">
        <xsl:for-each select="/doc/query[@queryName='questions']/row">
          <div class="col col-auto">
            <div style="text-align: center;">
              <div>
                <label>
                  <xsl:attribute name="for">
                    <xsl:value-of select="translate(@shortname, ' ', '_')"/>
                  </xsl:attribute>
                  <xsl:attribute name="title">
                    <xsl:value-of select="@hover" disable-output-escaping="yes"/>
                  </xsl:attribute>
                  <xsl:attribute name="data-toggle">
                    <xsl:text>tooltip</xsl:text>
                  </xsl:attribute>
                  <xsl:attribute name="data-html">
                    <xsl:text>true</xsl:text>
                  </xsl:attribute>
                  <xsl:attribute name="data-placement">
                    <xsl:text>right</xsl:text>
                  </xsl:attribute>
                  <xsl:value-of select="@shortname"/>
                </label>
                </div>
              </div>
            <xsl:choose>
              <xsl:when test="@typename = 'openend' or @typename = 'text' or @typename = 'html-text'">
                <xsl:call-template name="openend">
                  <xsl:with-param name="questionid" select="@questionid" />
                </xsl:call-template>
              </xsl:when>
              <xsl:when test="@typename = 'number'">
                <xsl:call-template name="number">
                  <xsl:with-param name="questionid" select="@questionid" />  
                </xsl:call-template>
              </xsl:when>
              <xsl:when test="@typename = 'monthyear'">
                <xsl:call-template name="monthyear">
                  <xsl:with-param name="questionid" select="@questionid" />
                </xsl:call-template>
              </xsl:when>
              <xsl:otherwise>
                <xsl:call-template name="selectlist">
                  <xsl:with-param name="questionid" select="@questionid" />
                </xsl:call-template>
              </xsl:otherwise>          
            </xsl:choose>
          </div>
        </xsl:for-each>
      </div>
      <div class="row mt-2">
        <div class="col col-auto">
          Match:
          <div>
            <xsl:attribute name="class">
              <xsl:text>form-check-inline</xsl:text>
            </xsl:attribute>
            <input class="form-check-input" type="radio" id="match-any" name="match-type" value="any" checked="true"/>
            <label class="form-check-label" style="margin-right: 10px;">
              <xsl:attribute name="for">
                <xsl:text>match-any</xsl:text>
              </xsl:attribute>
              selections in any column <i>(or)</i>
            </label>
            <input class="form-check-input" type="radio" id="match-all" name="match-type" value="all"/>
            <label class="form-check-label" style="margin-right: 10px;">
              <xsl:attribute name="for">
                <xsl:text>match-all</xsl:text>
              </xsl:attribute>
              selections in all column <i>(and)</i>
            </label>
          </div>
        </div>
      </div>
    </div>
  </xsl:template>
  <xsl:template name="openend">
    <xsl:param name="questionid" select="''"/>
    <input type="text" data-filter="text">
      <xsl:attribute name="id">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search</xsl:text>
      </xsl:attribute>
      <xsl:attribute name="name">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search</xsl:text>
      </xsl:attribute>
      <xsl:attribute name="size">
        <xsl:value-of select="20"/>
      </xsl:attribute>
    </input>
  </xsl:template>
  <xsl:template name="number">
    <xsl:param name="questionid" select="''"/>
    <label style="margin-right: 12px;">
      <xsl:attribute name="for">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search-min</xsl:text>
      </xsl:attribute>
      Min:
    </label>
    <input type="number" data-filter="min">
      <xsl:attribute name="id">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search-min</xsl:text>
      </xsl:attribute>
      <xsl:attribute name="name">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search-min</xsl:text>
      </xsl:attribute>
      <xsl:attribute name="style">
        <xsl:text>width: 100px;</xsl:text>
      </xsl:attribute>
    </input>
    <br/>
    <label style="margin-right: 10px;">
      <xsl:attribute name="for">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search-max</xsl:text>
      </xsl:attribute>
      Max:
    </label>
    <input type="number" data-filter="max">
      <xsl:attribute name="id">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search-max</xsl:text>
      </xsl:attribute>
      <xsl:attribute name="name">
        <xsl:value-of select="$questionid"/>
        <xsl:text>-search-max</xsl:text>
      </xsl:attribute>
      <xsl:attribute name="style">
        <xsl:text>width: 100px;</xsl:text>
      </xsl:attribute>
    </input>
  </xsl:template>
  <xsl:template name="monthyear">
    <xsl:param name="questionid" select="''"/>
    <div class="row">
      <div class="col col-auto">
        <div class="tag-chk-container" style="min-width: 2rem;">
          <xsl:for-each select="/doc/query[@queryName='options']/row[@questionid=$questionid]">
            <label class="tag-chk-label">
              <xsl:attribute name="title">
                <xsl:value-of select="@optionhover"/>
              </xsl:attribute>
              <input type="checkbox" class="tag-chk" data-filter="month">
                <xsl:attribute name="id">
                  <xsl:value-of select="$questionid"/>
                  <xsl:text>-</xsl:text>
                  <xsl:value-of select="translate(@optionshort, ' ', '_')"/>
                </xsl:attribute>
                <xsl:attribute name="name">
                  <xsl:value-of select="$questionid"/>
                  <xsl:text>-search[]</xsl:text>
                </xsl:attribute>
              </input>
              <xsl:value-of select="@optionshort" />
            </label>
          </xsl:for-each>
        </div>
      </div>
      <div class="col col-auto">
        <div class="tag-chk-container" style="min-width: 2rem;">
        <xsl:for-each select="/doc/query[@queryName='years']/row[@questionid=$questionid]">
            <label class="tag-chk-label">
              <xsl:attribute name="title">
                <xsl:value-of select="@optionhover"/>
              </xsl:attribute>
              <input type="checkbox" class="tag-chk" data-filter="year">
                <xsl:attribute name="id">
                  <xsl:value-of select="$questionid"/>
                  <xsl:text>-</xsl:text>
                  <xsl:value-of select="translate(@optionshort, ' ', '_')"/>
                </xsl:attribute>
                <xsl:attribute name="name">
                  <xsl:value-of select="$questionid"/>
                  <xsl:text>-search[]</xsl:text>
                </xsl:attribute>
              </input>
              <xsl:value-of select="@optionshort" />
            </label>
          </xsl:for-each>
        </div>
      </div>      
    </div>
  </xsl:template>
  <xsl:template name="selectlist">
    <xsl:param name="questionid" select="''"/>
    <div class="tag-chk-container" style="min-width: 2rem;">
      <xsl:for-each select="/doc/query[@queryName='options']/row[@questionid=$questionid]">
        <label class="tag-chk-label">
          <xsl:attribute name="title">
            <xsl:value-of select="@optionhover"/>
          </xsl:attribute>
          <input type="checkbox" class="tag-chk" data-filter="check">
            <xsl:attribute name="id">
              <xsl:value-of select="$questionid"/>
              <xsl:text>-</xsl:text>
              <xsl:value-of select="translate(@value, ' ', '_')"/>
            </xsl:attribute>
            <xsl:attribute name="name">
              <xsl:value-of select="$questionid"/>
              <xsl:text>-search[]</xsl:text>
            </xsl:attribute>
          </input>
          <xsl:value-of select="@optionshort" />
        </label>        
      </xsl:for-each>
    </div>
  </xsl:template>
</xsl:stylesheet>