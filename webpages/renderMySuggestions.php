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
    if (!may_I('my_suggestions_write')) {
        echo "<P>We're sorry, but we are unable to accept your suggestions at this time.\n";
        }
    echo "<FORM name=\"addform\" method=POST action=\"SubmitMySuggestions.php\">\n";
    echo "<INPUT type=\"hidden\" name=\"newrow\" value= \"".($newrow?1:0)."\">\n";
    echo "<DIV class=\"titledtextarea\">\n";
    echo "    <LABEL for=\"paneltopics\">Program Topic Ideas:</LABEL>\n";
    echo "    <TEXTAREA name=\"paneltopics\" rows=6 cols=72";
    if (!may_I('my_suggestions_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($paneltopics,ENT_COMPAT)."</TEXTAREA>\n";
    echo "    </DIV>\n";
    echo "<DIV class=\"titledtextarea\">\n";
    echo "    <LABEL for=\"otherideas\">Other Programming Ideas:</LABEL>\n";
    echo "    <TEXTAREA name=\"otherideas\" rows=6 cols=72";
    if (!may_I('my_suggestions_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($otherideas,ENT_COMPAT)."</TEXTAREA>\n";
    echo "    </DIV>\n";    
    echo "<DIV class=\"titledtextarea\">\n";
    echo "    <LABEL for=\"suggestedguests\">Suggested Guests (please provide addresses and other contact information if possible):</LABEL>\n";
    echo "    <TEXTAREA name=\"suggestedguests\" rows=8 cols=72";
    if (!may_I('my_suggestions_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($suggestedguests,ENT_COMPAT)."</TEXTAREA>\n";
    echo "    </DIV>\n";
    echo "<DIV class=\"submit\">\n";
    if (may_I('my_suggestions_write')) {
        echo "<DIV id=\"submit\"><BUTTON class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save</BUTTON></DIV>\n";
        }
    echo "</DIV>\n";
    echo "</FORM>\n";
    } ?>
