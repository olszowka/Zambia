<?xml version='1.0' encoding="UTF-8"?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="/">

        <form action="TableTents.php" method="GET" target="_blank">
            <div class="card mt-3">
                <div class="card-header">
                    <h2>Table Tents</h2>
                </div>
                <div class="card-body">
                    <p>The Table Tents feature creates printable "tents" that name the participants on a session (typically used for panels).
                    In many cons, the tents are printed out in the Green Room, and the panel moderator picks them up just before the panel 
                    is scheduled to begin.</p>

                    <p>To use these, you'll need a prism-shaped stand. The stands are reusable, and can remain in a room. When a new group of
                    session participants comes in, they can fold the tents in half and lay them over the stand, with the name portion visible to
                    the audience.</p>

                    <div class="text-center">
                        <img src="./images/table-tent.svg" style="height: 325px; width: 500px;" />
                    </div>

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