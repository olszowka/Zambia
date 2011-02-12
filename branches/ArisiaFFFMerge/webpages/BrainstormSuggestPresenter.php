<?php
    require_once('BrainstormCommonCode.php');
    require_once('SubmitCommentOn.php');
    $_SESSION['return_to_page']='BrainstormSuggestPresenter.php';
    $title="Brainstorm Suggested Presenter";
    brainstorm_header($title);
    global $youremail, $yourname, $badgeid, $session;
    get_name_and_email($yourname, $youremail);
    // error_log("badgeid: $badgeid; name: $yourname; email: $youremail"); // for debugging only
    $message_error="";
    $message_warn="";
    set_session_defaults();
    if (!(may_I('Participant')||may_I('Staff'))) { // must be brainstorm user
        $session["status"]=1; // brainstorm
        }
    if (strlen($message1)>0) {
      echo "<P id=\"message1\"><font color=red>".$message1."</font></P>\n";
    }
    if (strlen($message2)>0) {
      echo "<P id=\"message2\"><font color=red>".$message2."</font></P>\n";
      exit(); // If there is a message2, then there is a fatal error.
    }
    //error_log("Zambia: ".print_r($session,TRUE));

   // check to see if anything needs submitting
   if (isset($_POST["theiremail"])) {
      SubmitPresenterSuggestion();
      }

  ?>
<script language="javascript" type="text/javascript">
var phase1required=new Array("yourname", "youremail", "theirname", "theiremail", "theirwebsite", "whysuggested");
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
        <FORM name="presenterform" class="bb"  method=POST action="BrainstormSuggestPresenter.php">
        <INPUT type=reset value="Reset">&nbsp;
        <INPUT type=submit ID="sButtonTop" value="Save">
	<P>Note: items in red must be completed before you can save.</P>
        <P>Please make sure your name and email address are valid as well as the presenter's.
           If they aren't going to resolve properly the chance that we might invite the
           presenter you are suggesting, decreases exponentially.</P>
        <TABLE>
            <TR>
                <TD class="form1">
                   <LABEL for="yourname" ID="yourname">Your name:</LABEL><BR>
                   <INPUT TYPE="TEXT" NAME="yourname" onKeyPress="return checkSubmitButton();"
                   <?php if ($yourname!="")
                            echo "value=\"$yourname\" "; ?>
                       ></TD></TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
                   <LABEL for="youremail" ID="youremail">Your email address:</LABEL><BR>
                   <INPUT TYPE="TEXT" NAME="youremail" size="50" onKeyPress="return checkSubmitButton();"
                   <?php if ($youremail!="")
                            echo "value=\"$youremail\" "; ?>
                       ></TD></TR> 
            <TR>
                <TD class="form1">&nbsp;<BR>
                   <LABEL for="theirname" ID="theirname">Suggested Presenter's name:</LABEL><BR>
                   <INPUT TYPE="TEXT" NAME="theirname" onKeyPress="return checkSubmitButton();"></TD></TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
                   <LABEL for="theiremail" ID="theiremail">Suggested Presenter's email address:</LABEL><BR>
                   <INPUT TYPE="TEXT" NAME="theiremail" size="50" onKeyPress="return checkSubmitButton();"></TD></TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
                   <LABEL for="theirwebsite" ID="theirwebsite">Suggested Presenter's website:</LABEL><BR>
                   <INPUT TYPE="TEXT" NAME="theirwebsite" size="50" onKeyPress="return checkSubmitButton();"></TD></TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
                   <LABEL for="whysuggested" ID="whysuggested">Why you are suggesting they present for us:</LABEL><BR>
                   <TEXTAREA cols="70" rows="5" name="whysuggested" onKeyPress="return checkSubmitButton();"></TEXTAREA></TD></TR>
            <TR>
                <TD class="form1">&nbsp;<BR>
                   <LABEL for="notesforprog">Additional info for Programming Committee:</LABEL><BR>
                   <TEXTAREA cols="70" rows="7" name="notesforprog" ></TEXTAREA></TD></TR>
         </TABLE>
        <BR>
        <INPUT type=reset value="Reset">&nbsp;
        <INPUT type=submit ID="sButtonBottom" value="Save">
      </FORM>
  </DIV>
  <script language="javascript" type="text/javascript">
  colourCodeElements("phase1", "red", "green");
  checkSubmitButton();
  </script>
<?php brainstorm_footer(); ?>
