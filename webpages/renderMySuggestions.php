<?php
function renderMySuggestions ($title, $error, $message) {
    global $link, $paneltopics, $otherideas, $suggestedguests;
    global $newrow;
    participant_header($title);
    if ($error) {
            echo "<P class=\"errmsg\">Database not updated.<BR>".$message."</P>";
            }
        elseif ($message!="") {
            echo "<P>".$message."</P>";
            }
?>
    <FORM name="addform" method=POST action="SubmitMySuggestions.php">
    <INPUT type=hidden name="newrow" value= <?php echo "\"".($newrow?1:0)."\""; ?> >
    <DIV>
        <DIV><LABEL for="paneltopics">Program Topic Ideas:</LABEL></DIV>
        <DIV><TEXTAREA name="paneltopics" rows=6 cols=72><?php echo htmlspecialchars($paneltopics,ENT_COMPAT); ?></TEXTAREA>
            </DIV>
        </DIV>    
    <DIV>
        <DIV><LABEL for="otherideas">Other Programming Ideas:</LABEL></DIV>
        <DIV><TEXTAREA name="otherideas" rows=6 cols=72><?php echo htmlspecialchars($otherideas,ENT_COMPAT); ?></TEXTAREA>
            </DIV>
        </DIV>    
    <DIV>
        <DIV><LABEL for="suggestedguests">Suggested Guests (please provide addresses and other contact information if possible):</LABEL></DIV>
        <DIV><TEXTAREA name="suggestedguests" rows=8 cols=72><?php echo htmlspecialchars($suggestedguests,ENT_COMPAT); ?></TEXTAREA>
            </DIV>
        </DIV>    
    <DIV class="submit">
        <DIV id="submit"><BUTTON type="submit" name="submit">Save</BUTTON></DIV>
        </DIV>
    </FORM>
<?php } ?>
