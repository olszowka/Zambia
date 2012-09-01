<?php
    // This function will output the page with the form to add or create a session
    // Variables
    //     action: "create" or "edit"
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderEditCreateSession ($action, $session, $message1, $message2) {
    global $name, $email, $debug;
    require_once("StaffHeader.php");
    require_once("StaffFooter.php");
    if ($action=="create") {
            $title="Create New Session";
            }
        elseif ($action=="edit") {
            $title="Edit Session";
            }
        else {
            exit();
            }
    staff_header($title);
    
    // still inside function RenderAddCreateSession
    if (strlen($message1)>0) {
      echo "<p id=\"message1\" class=\"alert\">".$message1."</p>\n";
    }
    if (strlen($message2)>0) {
      echo "<p id=\"message2\" class=\"alert alert-error\">".$message2."</p>\n";
      exit(); // If there is a message2, then there is a fatal error.
    }
    //error_log("Zambia: ".print_r($session,TRUE));
    if (isset($debug)) {
        echo $debug."<BR>\n";
        }  
  ?>
    <DIV class="row-fluid">
        <FORM name="sessform" class="form-inline"  method=POST action="SubmitEditCreateSession.php">
          <INPUT type="hidden" name="name" value="<?php echo htmlspecialchars($name,ENT_COMPAT);?>">
          <INPUT type="hidden" name="email" value="<?php echo htmlspecialchars($email,ENT_COMPAT);?>">
          <!-- The pubno field is no longer used on the form, but the code expects it.-->
          <INPUT type="hidden" name="pubno" value="<?php echo htmlspecialchars($session["pubno"],ENT_COMPAT)."\">";?>
          <div id="buttonBox" class="clearfix">
            <div class="pull-right">
              <BUTTON class="btn" type=reset value="reset">Reset</BUTTON>
              <BUTTON class="btn btn-primary" type=submit value="save" onclick="mysubmit()">Save</BUTTON>
            </div>
          </div>
          <div class="row-fluid">
            <div class="control-group">
              <label class="control-label" for="sessionid">Session #: </label>
                <INPUT id="sessionid" type="text" class="span1" size=4 name="sessionid" disabled readonly value="<?php echo htmlspecialchars($session["sessionid"],ENT_COMPAT);?>">
              <label class="control-label" for="divisionid">Division: </label>
                <SELECT name="divisionid" class="span2">
                   <?php populate_select_from_table("Divisions", $session["divisionid"], "SELECT", FALSE); ?>
                </SELECT>
              <label class="control-label" for="track">Track: </label>
                <SELECT name="track" class="span2">
                  <?php populate_select_from_table("Tracks", $session["track"], "SELECT", FALSE); ?>
                </SELECT>
              <label class="control-label" for="type">Type: </label>
                <SELECT name="type" class="span2">
                  <?php populate_select_from_table("Types", $session["type"], "SELECT", FALSE); ?>
                </SELECT>
              <label class="control-label" for="pubstatusid">Pub. Status: </label>
                <SELECT name="pubstatusid" class="span2">
                  <?php populate_select_from_table("PubStatuses", $session["pubstatusid"], "SELECT", FALSE); ?>
                </SELECT>
            </div>
          </div>
            <div class="control-group">
              <label class="control-label" for="title">Title:</LABEL>
              <INPUT type=text class="span4" size="50" name="title" value="<?php echo htmlspecialchars($session["title"],ENT_COMPAT)."\">"; ?>
              <INPUT class="checkbox adjust" type="checkbox" value="invguest" id="invguest" <?php if ($session["invguest"]) {echo " checked ";} ?> name="invguest">
              <label class="checkbox inline" for="invguest"> Invited Guests Only</LABEL>
              <INPUT class="checkbox adjust" type="checkbox" value="signup" id="signup" <?php if ($session["signup"]) {echo " checked ";} ?> name="signup">
              <label class="checkbox inline" for="signup"> Signup Required</LABEL>
              <label class="control-label" for="kids">&nbsp;&nbsp;Kids:</LABEL>
              <SELECT name="kids" class="span2"><?php populate_select_from_table("KidsCategories", $session["kids"], "SELECT", FALSE); ?></SELECT>
          </DIV>
<?php
      if (strtoupper(BILINGUAL)=="TRUE") {
              echo "            <DIV class=\"span12\">\n";
              echo "                 <LABEL for=\"secondtitle\">".SECOND_TITLE_CAPTION.": </LABEL>";
              echo "<INPUT type=text size=\"50\" class=\"span4\" name=\"secondtitle\" value=\"".htmlspecialchars($session["secondtitle"],ENT_COMPAT)."\">\n";
              echo "                 <LABEL for=\"languagestatusid\">Session Language: </LABEL>";
              echo "<SELECT class=\"span2\" name=\"languagestatusid\">";
              populate_select_from_table("LanguageStatuses", $session["languagestatusid"], "SELECT", FALSE);
              echo "</SELECT>\n";
              echo "            </DIV>\n";
              }
          else {
              echo "            <INPUT type=\"hidden\" name=\"secondtitle\" value=\"";
              echo htmlspecialchars($session["secondtitle"],ENT_COMPAT)."\">";
              echo "            <INPUT type=\"hidden\" name=\"languagestatusid\" value=\"";
              echo htmlspecialchars($session["languagestatusid"],ENT_COMPAT)."\">";
              }
?>
          <!-- The pocketprogtext field is no longer used on the form, but the code expects it.-->
          <INPUT type="hidden" name="pocketprogtext" value="<?php echo htmlspecialchars($session["pocketprogtext"],ENT_COMPAT)."\">";?>
          <DIV class="row-fluid clearfix">
            <div class="control-group">
              <label class="control-label"  for="atten">Est. Atten.:</LABEL>
              <INPUT type="number" class="span2" size="3" name="atten" value="<?php echo htmlspecialchars($session["atten"],ENT_COMPAT)."\">"; ?>
              <label class="control-label"  for="duration">Duration:</LABEL>
              <INPUT type="text" class="span1" size="5" name="duration" value="<?php echo htmlspecialchars($session["duration"],ENT_COMPAT)."\">"; ?>
              <label class="control-label"  for="roomset">Room Set: </LABEL>
              <SELECT name="roomset" class="span2"><?php populate_select_from_table("RoomSets", $session["roomset"], "SELECT", FALSE); ?></SELECT>
              <label class="control-label"  for="status">Status:</LABEL>
              <SELECT name="status" class="span2"><?php populate_select_from_table("SessionStatuses", $session["status"], "", FALSE); ?></SELECT>
            </div>
          </DIV>
          <DIV class="row-fluid">
            <div class="span6">
              <LABEL class="control-label dense" for="progguiddesc">Description:</LABEL>
              <TEXTAREA class="span12 textlabelarea" cols=70 name="progguiddesc"><?php echo htmlspecialchars($session["progguiddesc"],ENT_NOQUOTES);?></TEXTAREA>
<?php
        if (strtoupper(BILINGUAL)=="TRUE") {
                echo "<LABEL class=\"control-label dense\" for=\"pocketprogtext\">";
                echo SECOND_DESCRIPTION_CAPTION.": </LABEL>\n";
                echo "<TEXTAREA class=\"textlabelarea\" cols=70 name=\"pocketprogtext\">";
                echo htmlspecialchars($session["pocketprogtext"],ENT_NOQUOTES)."</TEXTAREA>\n";
                }
            else {
                echo "                <!-- The pocketprogtext field is no longer used on the form, but the code expects it.-->\n";
                echo "                <INPUT type=\"hidden\" name=\"pocketprogtext\" value=\"";
                echo htmlspecialchars($session["pocketprogtext"],ENT_COMPAT)."\">\n";
                }
?>
            </div>
            <div class="span6">
              <LABEL class="dense" for="persppartinfo">Prospective Participant Info:</LABEL>
              <TEXTAREA class="span12 textlabelarea" cols=70 name="persppartinfo"><?php echo htmlspecialchars($session["persppartinfo"],ENT_NOQUOTES);?></TEXTAREA>
            </div>
          </div>
        </DIV>
        <DIV class="row-fluid">
          <DIV class="span5"> <!-- Features Box; -->
            <LABEL>Required Room Features:</LABEL>
            <DIV class="borderBox">
              <div class="clearfix">
                <LABEL for="featsrc" class="pull-left">Possible Features:</LABEL>
                <LABEL for="featdest[]" class="pull-right">Selected Features:</LABEL>
              </div>
              <div class="clearfix">
                <SELECT class="span5" style="float: left;" id="featsrc" name="featsrc" size=6 multiple>
                      <?php populate_multisource_from_table("Features", $session["featdest"]); ?>
                </SELECT>
                <div class="span2">
                  <BUTTON class="btn" onclick="fadditems(document.sessform.featsrc,document.sessform.featdest)"
                      name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                  <BUTTON class="btn" onclick="fdropitems(document.sessform.featsrc,document.sessform.featdest)"
                      name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                </div>
                <SELECT class="span5" style="float: left;" id="featdest" name="featdest[]" size=6 multiple >
                  <?php populate_multidest_from_table("Features", $session["featdest"]); ?>
                </SELECT>
              </div>
            </div>
          </DIV> <!-- Features -->
          <DIV class="span5" style="float: left;"> <!-- Services Box; -->
            <LABEL>Required Room Services:</LABEL>
            <DIV class="borderBox">
              <div class="clearfix">
                <LABEL for="servsrc" class="pull-left">Possible Services:</LABEL>
                <LABEL for="servdest[]" class="pull-right">Selected Services:</LABEL>
              </div>
              <div class="clearfix">
                <SELECT class="span5" style="float: left;" id="servsrc" name="servsrc" size=6 multiple>
                  <?php populate_multisource_from_table("Services", $session["servdest"]); ?>
                </SELECT>
                <div class="span2">
                  <BUTTON class="btn" onclick="fadditems(document.sessform.servsrc,document.sessform.servdest)"
                      name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                  <BUTTON  class="btn"onclick="fdropitems(document.sessform.servsrc,document.sessform.servdest)"
                      name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                </div>
                <SELECT class="span5" style="float: left;" id="servdest" name="servdest[]" size=6 multiple >
                    <?php populate_multidest_from_table("Services", $session["servdest"]); ?>
                </SELECT>
              </div>
            </div>
          </DIV> <!-- Services -->
          <DIV class="span2" style="float: left;"> 
            <LABEL class="control-label" for="pubchardest">Characteristics:
              <SELECT class="span12" id="pubchardest" name="pubchardest[]" multiple><?php populate_multiselect_from_table("PubCharacteristics",
                  $session["pubchardest"]); ?>
              </SELECT>
            </LABEL>
          </DIV>
        </DIV>
        <HR class="nospace">
        <div class="row-fluid form-vertical">
          <div class="span4">
            <LABEL class="control-label" for="notesforpart">Notes for Participants:</LABEL>
            <TEXTAREA class="textlabelarea span12" cols=70 name="notesforpart"
                    ><?php echo htmlspecialchars($session["notesforpart"],ENT_NOQUOTES);?></TEXTAREA>
          </div>
          <div class="span4">
            <LABEL class="control-label" for="servnotes">Notes for Tech and Hotel:</LABEL>
            <TEXTAREA class="textlabelarea span12" cols=70 name="servnotes"
                    ><?php echo htmlspecialchars($session["servnotes"],ENT_NOQUOTES);?></TEXTAREA>
          </div>
          <div class="span4">
            <LABEL class="control-label" for="notesforprog">Notes for Programming Committee:</LABEL>
            <TEXTAREA class="textlabelarea span12" cols=70 name="notesforprog"
                    ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES);?></TEXTAREA>
          </div>
        </div>
        <div id="buttonBox" class="clearfix">
          <div class="pull-right">
            <BUTTON class="btn" type=reset value="reset">Reset</BUTTON>
            <BUTTON class="btn btn-primary" type=submit value="save" onclick="mysubmit()">Save</BUTTON>
          </div>
        </div>
      <?php
        if ($action=="create") {
          echo "<INPUT type=\"hidden\" name=\"action\" value=\"create\">";
        } else {
          echo "<INPUT type=\"hidden\" name=\"action\" value=\"edit\">";
        }
      ?>
      </FORM>
    </DIV>
<?php staff_footer(); } ?>
