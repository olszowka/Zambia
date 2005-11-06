<?php
function renderMySuggestions ($title, $error, $message) {
    global $link, $paneltopics, $otherideas, $suggestedguests;
    global $newrow;
    participant_header($title);
    if ($error) {
            echo "<P class=\"errmsg\">Database not updated.<BR>".$message."</P>";
            }
        elseif ($message!="") {
            echo "<P class=\"regmsg\">".$message."</P>";
            }
?>
    <FORM name="addform" method=POST action="SubmitMySuggestions.php">
    <INPUT type=hidden name="newrow" value= <?php echo "\"".($newrow?1:0)."\""; ?> >
    <H3>My Suggestions</H3>
    <DIV class="titledtextarea">
        <LABEL for="paneltopics">Program Topic Ideas:</LABEL>
        <TEXTAREA name="paneltopics" rows=6 cols=72><?php echo htmlspecialchars($paneltopics,ENT_COMPAT); ?></TEXTAREA>
            
        </DIV>    
    <DIV class="titledtextarea">
        <LABEL for="otherideas">Other Programming Ideas:</LABEL>
        <TEXTAREA name="otherideas" rows=6 cols=72><?php echo htmlspecialchars($otherideas,ENT_COMPAT); ?></TEXTAREA>
        </DIV>    
    <DIV class="titledtextarea">
        <LABEL for="suggestedguests">Suggested Guests (please provide addresses and other contact information if possible):</LABEL>
        <TEXTAREA name="suggestedguests" rows=8 cols=72><?php echo htmlspecialchars($suggestedguests,ENT_COMPAT); ?></TEXTAREA>
        </DIV>    
    <DIV class="submit">
        <DIV id="submit"><BUTTON class="SubmitButton" type="submit" name="submit">Save</BUTTON></DIV>
        </DIV>
    </FORM>
<?php } ?>
