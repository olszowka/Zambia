<?php
    // This function will output the page with the form to add or create a session
    // Variables
    //     action: "brainstorm"
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function BrainstormRenderCreateSession ($action, $session, $message1, $message2) {
    global $name, $email;
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
<script language="javascript" type="text/javascript">
var phase1required=new Array("name", "email", "track", "title", "progguiddesc");
var currentPhase, unhappyColour, happyColor;

function colourCodeElements(phaseName, unhappyC, happyC) {
var i, o;
  	currentPhase = phaseName;
  	unhappyColor = unhappyC;
  	happyColor = happyC;
  	eval('var requiredElements = ' + phaseName + 'required');
  	if (requiredElements == null) return;
  	for (i = 0; i < requiredElements.length; i++) {
  		o = document.getElementById(requiredElements[i]);
  		if (o != null) {
			o.style.color = "red";
  		}
  	}
}

function checkSubmitButton() {
var i, j, o, relatedO, controls;
var enable = true;
  
  	eval('var requiredElements = ' + currentPhase + 'required');
  	if (requiredElements == null) return;
  	for (i = 0; i < requiredElements.length; i++) {
  		controls = document.getElementsByName(requiredElements[i]);
  		if (controls != null) {
  			for (j = 0; j < controls.length; j++) {
  				
				o = controls[j];
				relatedO = document.getElementById(requiredElements[i]);
				switch (o.tagName) {
				case "LABEL":
					break;
				case "SELECT":
					if (o.options[o.selectedIndex].value == 0) {
						enable = false;
						relatedO.style.color = unhappyColor;
					}
					else {
						relatedO.style.color = happyColor;
					}
					break;
				case "TEXTAREA":
					if (o.value == "") {
						enable = false;
						relatedO.style.color = unhappyColor;
					}
					else {
						relatedO.style.color = happyColor;
					}
					break;
				case "INPUT":
					if (o.value == "") {
						enable = false;
						relatedO.style.color = unhappyColor;
					}
					else {
						relatedO.style.color = happyColor;
					}
					break;
				}
			}
  		}
  	}
	var saveButton = document.getElementById("sButtonTop");
	if (saveButton != null) {
		saveButton.disabled = !enable;
	}	
	var saveButton = document.getElementById("sButtonBottom");
	if (saveButton != null) {
		saveButton.disabled = !enable;
	}	
}
</script>
  
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
        <INPUT type=reset value="Reset">&nbsp;
        <INPUT type=submit ID="sButtonTop" value="Save">
	<p>Note: items in red must be completed before you can save.</p>
        <TABLE>
            <TR>
                <TD class="form1">
                   <LABEL for="name" ID="name">Your Name:</LABEL><BR>
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
                <TD class="form1">&nbsp;<BR>
                    <LABEL for="track" ID="track">Track:</LABEL><BR><SELECT name="track" onChange="return checkSubmitButton();">
                    <?php populate_select_from_table("Tracks", $session["track"], "SELECT", FALSE); ?>
                    </SELECT></TD>
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
          <LABEL for="notesforprog">Additional info for Programming Committee:</LABEL><BR>
            <TEXTAREA cols="70" rows="7" name="notesforprog" ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES); ?></TEXTAREA>
                </TD>
             </TR>
         </TABLE>
        <INPUT type=reset value="Reset">&nbsp;
        <INPUT type=submit ID="sButtonBottom" value="Save">
      </FORM>
  </DIV>
  <script language="javascript" type="text/javascript">
  colourCodeElements("phase1", "red", "green");
  checkSubmitButton();
  </script>
<?php brainstorm_footer(); } ?>
