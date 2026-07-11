<?xml version="1.0" encoding="UTF-8" ?>
<!--
    Created by Syd Weinstein on 2020-09-03;
    Copyright (c) 2020-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:param name="UpdateMessage" select="''"/>
    <xsl:param name="control" select="''"/>
    <xsl:param name="controliv" select="''"/>
    <xsl:param name="selected" select="''"/>
    <xsl:param name="initialtext" select="''"/>
    <xsl:param name="initialactive" select="''"/>
    <xsl:param name="htmlblocklevel" select="''"/>
    <xsl:output encoding="UTF-8" indent="yes" method="html"/>
    <xsl:template match="/">
        <xsl:if test="$UpdateMessage != ''">
            <div class="alert alert-success mt-4">
                <xsl:value-of select="$UpdateMessage" disable-output-escaping="yes"/>
            </div>
        </xsl:if>
        <div class="row justify-content-center mt-4">
            <h4 class="col-auto">Custom Text Entries</h4>
        </div>
        <form name="customtextform" method="POST" action="EditCustomText.php">
            <input type="hidden" id="PostCheck" name="PostCheck" value="POST"/>
            <input type="hidden" id="control" name="control" value="{$control}"/>
            <input type="hidden" id="controliv" name="controliv" value="{$controliv}"/>

            <div id="select_custom_text_item" class="form-row">
                <div class="col-auto">
                    <label for="customtextid">Entry to edit:&#160;&#160;</label>
                    <select id="customtextid" name="customtextid">
                        <xsl:if test="$selected = ''">
                            <option value="-1">Select entry to edit</option>
                        </xsl:if>
                        <xsl:for-each select="/doc/query[@queryName='custom_text']/row">
                            <option value="{@customtextid}">
                                <xsl:attribute name="data-initialtext">
                                    <xsl:value-of select="@textcontents"/>
                                </xsl:attribute>
                                <xsl:attribute name="data-initialactive">
                                    <xsl:value-of select="@active"/>
                                </xsl:attribute>
                                <xsl:attribute name="data-htmlblocklevel">
                                    <xsl:value-of select="@html_block_level"/>
                                </xsl:attribute>
                                <xsl:if test="@customtextid = $selected">
                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                </xsl:if>
                                <xsl:value-of select="@page"/>
                                <xsl:text>&#160;-&gt;&#160;</xsl:text>
                                <xsl:value-of select="@tag"/>
                            </option>
                        </xsl:for-each>
                    </select>
                </div>
            </div>
            <div id="activeeditor" class="row mt-4">
                <xsl:if test="$selected = ''">
                    <xsl:attribute name="style">display: none;</xsl:attribute>
                </xsl:if>
                <div class="col-auto">
                    <label for="active">Active:&#160;&#160;&#160;</label>
                    <input type="checkbox" id="active" name="active" value="1">
                        <xsl:if test="$initialactive = '1'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
                    </input>
                    <xsl:text>&#160;&#160;&#160;&#160;&#160;&#160;Entries must be marked as "active" to be in effect.</xsl:text>
                </div>
            </div>
            <div id="texteditor" class="form-row mt-4">
                <xsl:if test="$selected = ''">
                    <xsl:attribute name="style">display: none;</xsl:attribute>
                </xsl:if>
                <div class="col-6">
                    <label for="textcontents">Custom Text</label>
                </div>
                <div class="col">
                    <textarea id="textcontents" name="textcontents" rows="15" style="width: 90%">
                        <xsl:value-of select="$initialtext"/>
                    </textarea>
                </div>
            </div>
            <div class="form-row mt-4">
                <p id="htmlBlockInstructions">
                    <xsl:if test="not($htmlblocklevel = '1')">
                        <xsl:attribute name="style">display: none;</xsl:attribute>
                    </xsl:if>
                    This entry may be HTML block level.  That means it may have multiple paragraphs, a bullet list, etc.  By default it will have one paragraph.
                </p>
                <p id="notHtmlBlockInstructions">
                    <xsl:if test="not($htmlblocklevel = '0')">
                        <xsl:attribute name="style">display: none;</xsl:attribute>
                    </xsl:if>
                    This entry must be HTML in-line level.  That means it may have formatting such as italics, but it is a single blurb of text.
                </p>
            </div>
            <div class="row justify-content-center mt-4">
                <div class="col-auto">
                    <button class="btn btn-secondary" id="resetbtn" name="resetbtn" value="undo" type="button">Reset</button>
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" id="submitbtn" name="submitbtn" type="submit" value="save">Save</button>
                </div>
            </div>
        </form>
    </xsl:template>
</xsl:stylesheet>
