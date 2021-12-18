<?xml version='1.0' encoding="UTF-8"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">

        <form action="TableTents.php" method="GET" target="_blank">
            <div class="card mt-3">
                <div class="card-header">
                    <h2>Table Tents</h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label for="paper">Paper type:</label>
                            <select id="paper" name="paper" class="form-control">
                                <option value="LETTER">Letter-sized</option>
                                <option value="A4">A4-sized</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </div>
        </form>

    </xsl:template>
</xsl:stylesheet>