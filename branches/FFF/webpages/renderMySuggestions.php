<?php
function renderMySuggestions ($title, $error, $message) {
    global $link, $paneltopics, $otherideas, $suggestedguests;
    global $newrow;
    require_once("RenderEditCreateSession.php");
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
    echo "<P>Proposed Suggestions already submitted:</P>";
    RenderEditCreateSession("propose","","","");
?>
    <DIV class="formbox">
        <FORM name="sessform" class="bb"  method=POST action="SubmitEditCreateSession.php">
        <INPUT type="hidden" name="divisionid" value="<?php echo $session["divisionid"]; ?>">
        <INPUT type="hidden" name="pubstatusid" value="<?php echo $session["pubstatusid"]; ?>">
        <INPUT type="hidden" name="pubno" value="<?php echo htmlspecialchars($session["pubno"],ENT_COMPAT);?>">
        <INPUT type="hidden" name="languagestatusid" value="<?php echo $session["languagestatusid"]; ?>">
        <INPUT type="hidden" name="duration" value="<?php echo htmlspecialchars($session["duration"],ENT_COMPAT);?>">
        <INPUT type="hidden" name="atten" value="<?php echo htmlspecialchars($session["atten"],ENT_COMPAT);?>">
        <INPUT type="hidden" name="kids" value="<?php echo $session["kids"];?>">
        <INPUT type="hidden" name="status" value="<?php echo $session["status"];?>">
        <INPUT type="hidden" name="action" value="participant">
        <INPUT type=submit ID="sButtonTop" value="Save">
        <TABLE>
            <TR>
                <TD class="form1">
                   <LABEL for="name" ID="name">Your name:</LABEL><BR>
                   <INPUT TYPE="TEXT" NAME="name" onKeyPress="return checkSubmitButton();"
                   <?php if ($name!="")
                            echo "value=\"$name\" "; ?>
                       ></TD></TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
                   <LABEL for="email" ID="email">Your email address:</LABEL><BR>
                   <INPUT TYPE="TEXT" NAME="email" size="50" onKeyPress="return checkSubmitButton();"
                   <?php if ($email!="")
                            echo "value=\"$email\" "; ?>
                       ></TD></TR> 
            <TR>
                <TABLE>
                   <TR>
                       <TD class="form1">&nbsp;<BR>
                           <LABEL for="track" ID="track">Track:</LABEL><BR><SELECT name="track" onChange="return checkSubmitButton();">
                           <?php populate_select_from_table("$ReportDB.Tracks", $session["track"], "SELECT", FALSE); ?>
                           </SELECT></TD>
                       <TD class="form1">&nbsp;<BR>
                           <LABEL for="type" ID="type">Type:</LABEL><BR><SELECT name="type" onChange="return checkSubmitButton();">
                           <?php populate_select_from_table("$ReportDB.Types", $session["type"], "Panel", FALSE); ?>
                           </SELECT></TD>
	<INPUT type="hidden" name="roomset" value="<?php echo $session["roomset"];?>">
                       </TR>
		   </TABLE>
                </TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
          <LABEL for="title" ID="title">Title: </LABEL><BR>
            <?php echo "<INPUT type=text size=\"50\" name=\"title\" value=\"";
            echo htmlspecialchars($session["title"],ENT_COMPAT)."\" onKeyPress=\"return checkSubmitButton();\">"; ?>
                </TD>
             </TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
          <LABEL for="progguiddesc" id="progguiddesc">Description:</LABEL><BR>
            <TEXTAREA cols="70" rows="5" name="progguiddesc" onKeyPress="return checkSubmitButton();"><?php echo htmlspecialchars($session["progguiddesc"],ENT_NOQUOTES); ?></TEXTAREA>
                </TD>
             </TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
          <LABEL for="notesforprog">Additional info (including if there is a particular presenter you want to present this) for Programming Committee:</LABEL><BR>
            <TEXTAREA cols="70" rows="7" name="notesforprog" ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES); ?></TEXTAREA>
                </TD>
             </TR>
         </TABLE><BR>
        <INPUT type=submit ID="sButtonBottom" value="Save">
      </FORM>
  </DIV>
<?php
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
