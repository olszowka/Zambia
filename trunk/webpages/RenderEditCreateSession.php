<?php
    // This function will output the page with the form to add or create a session
    // Variables
    //     action: "create" or "edit"
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderEditCreateSession ($action, $session, $message1, $message2) {
    require_once("StaffHeader.php");
    require_once("StaffFooter.php");
    if ($action=="create") {
            $title="Add New Session";
            }
        elseif ($action=="edit") {
            $title="Edit Session";
            }
        else {
            exit();
            }
    staff_header($title);
    
    // still inside function RenderAddCreateSession
    if ($action=="create") {
      echo "<H1>Create New Session</H1>\n";
    } else {
      echo "<H1>Edit Session</H1>\n";
    }
    if (strlen($message1)>0) {
      echo "<P id=\"message1\">".$message1."</P>\n";
    }
    if (strlen($message2)>0) {
      echo "<P id=\"message2\">".$message2."</P>\n";
    }
  ?>
   <DIV class="formbox">
    <FORM name="sessform" class="bb"  method=POST action="SubmitEditCreateSession.php">
      <DIV class="tab-row"> <!-- Block 1 is Row 1 -->

        <DIV class="ib">
          <DIV class="ib" for="sessionid">Session #:</DIV>
          <DIV class="ib">
            <?php // still inside function RenderAddCreateSession
              echo "<INPUT type=\"text\" size=3 name=\"sessionid\" readonly value=\"";
              echo htmlspecialchars($session["sessionid"],ENT_COMPAT)."\">\n";
            ?>
          </DIV>
        </DIV>

        <DIV class="ib">
          <DIV class="ib" for="track">Track: </DIV>
          <DIV class="ib">
            <SELECT name="track"><?php populate_select_from_table("Tracks", $session["track"], "SELECT"); ?></SELECT>
          </DIV>
        </DIV>

        <DIV class="ib">
          <DIV class="ib" for="type">Type: </DIV>
          <DIV class="ib">
            <SELECT name="type"><?php populate_select_from_table("Types", $session["type"], "SELECT"); ?></SELECT>
          </DIV>
        </DIV>
  
        <DIV class="ib">
          <DIV class="ib" for="pubno">Pub. No.:</DIV>
          <DIV class="ib"><?php
            echo "<INPUT type=text size=\"6\" name=\"pubno\" value=\"";
            echo htmlspecialchars($session["pubno"],ENT_COMPAT)."\">";
          ?></DIV>
        </DIV>
      </DIV> <!-- Block 1 is Row 1 -->

      <HR> <!-- the horizontal rule give the browser a clue -->

      <DIV class="bigbox"> <!-- try me -->

        <DIV class="textwithlabel"> <!-- Block 2 is Title -->
          <LABEL class="textlabel" for="title">Title: </LABEL>
          <DIV class="titlelabelarea"><?php
            echo "<INPUT type=text size=\"50\" name=\"title\" value=\"";
            echo htmlspecialchars($session["title"],ENT_COMPAT)."\">";
          ?></DIV>
        </DIV> <!-- Block 2 is Title -->

        <DIV class="textwithlabel"> <!-- Block 3 is Pocket Program Text -->
          <LABEL class="textlabel" for="pocketprogtext">Pocket Program Text:</LABEL>
          <DIV class="ib">
            <TEXTAREA class="textlabelarea" cols=70 name="pocketprogtext" ><?php echo htmlspecialchars($session["pocketprogtext"],ENT_NOQUOTES); ?></TEXTAREA>
          </DIV>
        </DIV> <!-- Block 3 is Pocket Program Text -->

        <DIV class="textwithlabel"> <!-- Block 4 is Program Guide Description -->
          <LABEL class="textlabel" for="progguiddesc">Program Guide Description:</LABEL>
          <DIV class="ib">
            <TEXTAREA class="textlabelarea" cols=70 name="progguiddesc" ><?php echo htmlspecialchars($session["progguiddesc"],ENT_NOQUOTES); ?></TEXTAREA>
          </DIV>
        </DIV> <!-- Block 4 is Pocket Program Text -->

        <DIV class="textwithlabel"> <!-- Block 5 is Prospective Participant Info -->
          <LABEL class="textlabel" for="persppartinfo">Prospective Participant Info:</LABEL>
          <DIV class="ib">
            <TEXTAREA class="textlabelarea" cols=70 name="persppartinfo"><?php echo htmlspecialchars($session["persppartinfo"],ENT_NOQUOTES); ?></TEXTAREA>
          </DIV>
        </DIV> <!-- Block 5 is Prospective Participant Info -->
      </DIV> 

      <hr> <!-- the horizontal rule give the browser a clue -->

      <DIV class="b5"> <!-- b5 --> 

        <DIV class="b5_a"> <!-- b5_a     -->
          <DIV class="bb">
            <LABEL class="ib" for="duration">Duration:</LABEL>
            <DIV class="ib"><?php echo "<INPUT type=text size=\"4\" name=\"duration\" value=\""; echo htmlspecialchars($session["duration"],ENT_COMPAT)."\">"; ?></DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="atten">Est. Atten.:</LABEL>
            <DIV class="ib"><?php echo "<INPUT type=text size=\"4\" name=\"atten\" value=\""; echo htmlspecialchars($session["atten"],ENT_COMPAT)."\">"; ?></DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="kids">Kid Category:</LABEL>
            <DIV class="ib">
              <SELECT name="kids"><?php populate_select_from_table("KidsCategories", $session["kids"], "SELECT"); ?></SELECT>
            </DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="invguest">Invited Guests Only? </LABEL>
            <DIV class="ib"> <input type="checkbox" value="invguest" <?php if ($session["invguest"]) {echo " checked ";} ?> name="invguest"> </DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="signup">Sign up Req.?</LABEL>
            <DIV class="ib"><input type="checkbox" value="signup" <?php if ($session["signup"]) {echo " checked ";} ?> name="signup"></DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="roomset">Room Set:</LABEL>
            <DIV class="ib"> <SELECT name="roomset"> <?php populate_select_from_table("RoomSets", $session["roomset"], "SELECT"); ?> </SELECT> </DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="status">Status:</LABEL>
            <DIV class="ib">
              <SELECT name="status"><?php populate_select_from_table("SessionStatuses", $session["status"], ""); ?></SELECT>
            </DIV>
          </DIV>
        </DIV> <!-- b5_a -->

        <DIV class="b5_b"> <!-- b5_b -->
          <DIV class="bigbox"> <!-- Both -->
            <DIV class="features"> <!-- Features -->
              <LABEL class="bb" style="text-align: center">Required Features of Room</LABEL>
              <DIV class="select_set">  <!-- lower box -->

                <DIV class="ib">
                  <LABEL class="bb" for="featsrc">Possible Features</LABEL>
                  <SELECT class="select_l" id="featsrc" name="featsrc" size=6 multiple><?php populate_multisource_from_table("Features", $session["featdest"]); ?></SELECT>
                </DIV>

                <DIV class="ib" style="vertical-align: top">
                  <LABEL class="bb">&nbsp;</LABEL>
                  <BUTTON onclick="fadditems(document.sessform.featsrc,document.sessform.featdest)" 
                          name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                  <BUTTON onclick="fdropitems(document.sessform.featsrc,document.sessform.featdest)" 
                          name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                </DIV>

                <DIV class="ib" >
                  <LABEL class="bb" style="text-align: center" for="featdest[]">Selected Features</LABEL>
                  <SELECT class="select_r" id="featdest" name="featdest[]" size=6 multiple >
                    <?php populate_multidest_from_table("Features", $session["featdest"]); ?>
                  </SELECT>
                </DIV>
              </DIV> <!-- lower box -->
            </DIV> <!-- Features -->

            <DIV class="services"> <!-- Services -->
              <LABEL class="bb" style="text-align: center">Services Required</LABEL>
              <DIV class="select_set">  <!-- lower box -->
                <DIV class="ib">
                  <LABEL class="bb" style="text-align: center" for="servsrc">Possible Services</LABEL>
                  <SELECT class="select_l" name="servsrc" id="servsrc" size=6 multiple >
                    <?php populate_multisource_from_table("Services", $session["servdest"]); ?>
                  </SELECT>
                </DIV>
                <DIV class="ib" style="vertical-align: top">
                  <LABEL class="bb">&nbsp;</LABEL>
                  <BUTTON onclick="fadditems(document.sessform.servsrc,document.sessform.servdest)" name="additems2" value="additems2" type="button">&nbsp;&nbsp;&rarr;&nbsp;&nbsp;</BUTTON>
                  <BUTTON onclick="fdropitems(document.sessform.servsrc,document.sessform.servdest)" name="dropitems2" value="dropitems2" type="button">&nbsp;&nbsp;&larr;&nbsp;&nbsp;</BUTTON>
                </DIV>
                <DIV class="ib">
                  <LABEL class="bb" style="text-align: center" for="servdest[]">Selected Services</LABEL>
                  <SELECT class="select_r" name="servdest[]" id="servdest" size=6 multiple >
                    <?php populate_multidest_from_table("Services", $session["servdest"]); ?>
                  </SELECT>
                </DIV>
              </DIV> <!-- lower box -->
            </DIV> <!-- Services -->
          </DIV> <!-- both -->
        </DIV> <!-- b5_b -->
      </DIV> <!-- b5 -->

      <HR> <!-- the horizontal rule give the browser a clue -->

      <DIV class="bigbox"> <!-- Block 6s Notes and stuff -->
        <DIV class="textwithlabel">
          <LABEL class="textlabel" for="notesforpart">Notes for Participants:</LABEL>
          <DIV class="ib">
            <TEXTAREA class="textlabelarea" cols=70 name="notesforpart" ><?php echo htmlspecialchars($session["notesforpart"],ENT_NOQUOTES); ?></TEXTAREA>
          </DIV>
        </DIV>

        <DIV class="textwithlabel">
          <LABEL class="textlabel" for="servnotes">Notes for Tech and Hotel:</LABEL>
          <DIV class="ib">
            <TEXTAREA class="textlabelarea" cols=70 name="servnotes" ><?php echo htmlspecialchars($session["servnotes"],ENT_NOQUOTES); ?></TEXTAREA>
          </DIV>
        </DIV>

        <DIV class="textwithlabel">
          <LABEL class="textlabel" for="notesforprog">Notes for Programming Committee:</LABEL>
          <DIV class="ib">
            <TEXTAREA class="textlabelarea" cols=70 name="notesforprog" ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES); ?></TEXTAREA>
          </DIV>
        </DIV>
      </DIV> <!-- Block 6s Notes and stuff -->


      <DIV class="buttons">
        <BUTTON class="ib" type=reset value="reset">Reset</BUTTON>
        <BUTTON class="ib" type=submit value="save" onclick="mysubmit()">Save</BUTTON>
      </DIV> &nbsp; <!-- space needed to keep the buttons in -->

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
