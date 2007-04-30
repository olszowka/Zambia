<?php
    // This function will output the page with the form to add or create a session
    // Variables
    //     action: "brainstorm"
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function BrainstormRenderCreateSession ($action, $session, $message1, $message2) {
    require_once("BrainstormCommonCode.php");
    $_SESSION['return_to_page']='BrainstormRenderCreateSession.php';
    $title="Brainstorm New Session";
    brainstorm_header($title);
    
    // still inside function RenderAddCreateSession
    if (strlen($message1)>0) {
      echo "<P id=\"message1\">".$message1."</P>\n";
    }
    if (strlen($message2)>0) {
      echo "<P id=\"message2\">".$message2."</P>\n";
      exit(); // If there is a message2, then there is a fatal error.
    }
    //error_log("Zambia: ".print_r($session,TRUE));
  ?>
    <DIV class="formbox">
        <FORM name="sessform" class="bb"  method=POST action="SubmitEditCreateSession.php">
        <INPUT type="hidden" name="type" value="<?php echo $session["type"]; ?>">
        <INPUT type="hidden" name="divisionid" value="<?php echo $session["divisionid"]; ?>">
        <INPUT type="hidden" name="roomset" value="<?php echo $session; ?>">
        <INPUT type="hidden" name="pubstatusid" value="<?php echo $session["pubstatusid"]; ?>">
        <INPUT type="hidden" name="pubno" value="<?php echo htmlspecialchars($session["pubno"],ENT_COMPAT);?>">
        <INPUT type="hidden" name="duration" value="<?php echo htmlspecialchars($session["duration"],ENT_COMPAT);?>">
        <INPUT type="hidden" name="atten" value="<?php echo htmlspecialchars($session["atten"],ENT_COMPAT);?>">
        <INPUT type="hidden" name="kids" value="<?php echo $session["kids"];?>">
        <INPUT type="hidden" name="status" value="<?php echo $session["status"];?>">
        <INPUT type="hidden" name="action" value="brainstorm">
        <BUTTON type=reset value="reset">Reset</BUTTON>
        <BUTTON type=submit value="save" >Save</BUTTON>
        <TABLE>
            <TR>
                <TD class="form1">Track:<BR> <SELECT name="track">
                    <?php populate_select_from_table("Tracks", $session["track"], "SELECT", FALSE); ?>
                    </SELECT></TD>
                </TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
          <LABEL for="title">Title: </LABEL><BR>
            <?php echo "<INPUT type=text size=\"50\" name=\"title\" value=\"";
            echo htmlspecialchars($session["title"],ENT_COMPAT)."\">"; ?>
                </TD>
             </TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
          <LABEL for="progguiddesc">Description:</LABEL><BR>
            <TEXTAREA cols="70" rows="5" name="progguiddesc" ><?php echo htmlspecialchars($session["progguiddesc"],ENT_NOQUOTES); ?></TEXTAREA>
                </TD>
             </TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
          <LABEL for="notesforprog">Additional info for Programming Committee:</LABEL><BR>
            <TEXTAREA cols="70" rows="7" name="notesforprog" ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES); ?></TEXTAREA>
                </TD>
             </TR>
         </TABLE>
        <BUTTON type=reset value="reset">Reset</BUTTON>
        <BUTTON type=submit value="save" >Save</BUTTON>
      </FORM>
  </DIV>
<?php brainstorm_footer(); } ?>
