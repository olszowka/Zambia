<?xml version="1.0" encoding="UTF-8" ?>
<!--
	SearchMySessions1.xsl
	Created by Peter Olszowka on 2011-10-15.
	Copyright (c) 2011 __MyCompanyName__. All rights reserved.
-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output encoding="UTF-8" indent="yes" method="xml" />
    <xsl:variable name="mayISubmitPanelInterests" select="/doc/query[@queryName='may_I']/row/@my_panel_interests" />

    <xsl:template match="/">
      <div class="row-fluid">
        <form class="form-inline" name="resform" method="POST" action="SubmitMySessions1.php">
            <xsl:if test="$mayISubmitPanelInterests">
                <button class="btn btn-primary pull-right" type="submit" name="save">Save</button>
                <p>If you have selected any panels, please remember to <strong>SAVE</strong> before leaving the page. (Use either "Save" buttons at the top or bottom.)</p>
                <div class="alert-info">You will find the results of your search below. We have included the session id, track, title, duration, a check box for you to
                    indicate interest, followed by the description as it will appear in the program guide and some additional information for you as a
                    prospective panelist.
                </div>
            </xsl:if>
            <table id="searchMySessions1TAB">
                <xsl:apply-templates select="/doc/query[@queryName='sessions']/row"/>
            </table>
            <xsl:if test="$mayISubmitPanelInterests">
                <button class="btn btn-primary pull-right" type="submit" name="save2">Save</button>
            </xsl:if>
        </form>
      </div>
    </xsl:template>

    <xsl:template match="/doc/query[@queryName='sessions']/row">
        <tr>
            <td rowspan="4" class="border0000" id="sessidtcell">
                <span style="font-weight:bold; margin-right: 10px"><xsl:value-of select="@sessionid"/></span>
            </td>
            <td class="border0000">
                <span style="font-weight:bold;"><xsl:value-of select="@trackname"/></span>
            </td>
            <td class="border0000">
                <span style="font-weight:bold;"><xsl:value-of select="@title"/></span>
            </td>
            <td class="border0000">
                <span style="font-weight:bold;"><xsl:value-of select="@duration"/></span>
            </td>
        </tr>
        <xsl:if test="$mayISubmitPanelInterests">
            <tr>
                <td colspan="3" sessionid="{@sessionid}">
                    <label class="checkbox"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>Add this panel to my list<input type="checkbox" value="{@sessionid}" name="int{@sessionid}" id="int{@sessionid}">
                            <xsl:if test="@badgeid">
                                <xsl:attribute name="checked">checked</xsl:attribute>
                            </xsl:if>
                        </input>
                        <input type="hidden" value="{@sessionid}" name="dirty{@sessionid}" id="dirty{@sessionid}" disabled="disabled" />
                    </label>
                </td>
            </tr>
        </xsl:if>
        <tr>
            <td colspan="3" class="border0010">
                <xsl:value-of select="@progguiddesc" />
            </td>        
        </tr>
        <tr>
            <td colspan="3" class="border0000">
                <span class="alert" style="padding: 0"><xsl:value-of select="@persppartinfo" /></span>
            </td>        
        </tr>
        <tr>
            <td colspan="4" class="border0020">
                <hr />
            </td>        
        </tr>
    </xsl:template>

</xsl:stylesheet>
