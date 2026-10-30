<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <!-- File created by Peter Olszowka September 23, 2026
    Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details. -->
    <xsl:param name="reportcategoryid" />
    <xsl:param name="reporthidingenabled" />

    <xsl:template match="/">
        <div class="container-lg">
            <xsl:apply-templates select="doc/query[@queryName='reports']/row" />
        </div>
    </xsl:template>

    <xsl:template match="doc/query[@queryName='reports']/row">
        <div class="row mt-2">
            <xsl:if test="$reporthidingenabled = '1'">
                <div class="col-md-12 d-flex justify-content-end align-items-start">
                    <xsl:if test="$reportcategoryid">
                        <form method="POST" action="HideReport_POST.php" onsubmit="return confirm('Hide this report from this category?');"
                              class="d-inline-block">
                            <input type="hidden" name="reportFileName" value="{@reportfilename}" />
                            <input type="hidden" name="scope" value="category" />
                            <input type="hidden" name="category" value="{$reportcategoryid}" />
                            <input type="hidden" name="reportcategory" value="{$reportcategoryid}" />
                            <button type="submit" class="btn btn-sm btn-info ms-2 me-2 mb-2">Hide from this category</button>
                        </form>
                    </xsl:if>
                    <form method="POST" action="HideReport_POST.php" onsubmit="return confirm('Hide this report from all categories?');"
                        class="d-inline-block">
                        <input type="hidden" name="reportFileName" value="{@reportfilename}" />
                        <input type="hidden" name="scope" value="all" />
                        <xsl:if test="$reportcategoryid">
                            <input type="hidden" name="reportcategory" value="{$reportcategoryid}" />
                        </xsl:if>
                        <button type="submit" class="btn btn-sm btn-secondary ms-2 me-2 mb-2">Hide from all categories</button>
                    </form>
                </div>
            </xsl:if>
            <div>
                <xsl:attribute name="class">
                    <xsl:text>col-md-24</xsl:text>
                    <xsl:if test="not ($reporthidingenabled = '1')">
                        <xsl:text> offset-md-8</xsl:text>
                    </xsl:if>
                </xsl:attribute>
                <h5>
                    <a href="generateReport.php?reportName={@reportfilename}">
                        <xsl:value-of select="@reportname" />
                    </a>
                </h5>
                <p>
                    <xsl:value-of select="@reportdescription" />
                </p>
            </div>
        </div>
    </xsl:template>

</xsl:stylesheet>
