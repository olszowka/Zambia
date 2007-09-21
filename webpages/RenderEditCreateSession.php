<?php
    // This function will output the page with the form to add or create a session
    // Variables
    //     action: "create" or "edit"
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderEditCreateSession ($action, $session, $message1, $message2) {
    global $name, $email;
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
            <INPUT type="hidden" name="name" value="<?php echo htmlspecialchars($name,ENT_COMPAT);?>">
            <INPUT type="hidden" name="email" value="<?php echo htmlspecialchars($email,ENT_COMPAT);?>">
        <TABLE>
            <TR>
                <TD class="form1">Session #: <INPUT type="text" size=3 name="sessionid" readonly
                    value="<?php echo htmlspecialchars($session["sessionid"],ENT_COMPAT);?>"</TD>  
                <TD class="form1">Track: <SELECT name="track">
                    <?php populate_select_from_table("Tracks", $session["track"], "SELECT", FALSE); ?>
                    </SELECT></TD>
                <TD class="form1">Type: <SELECT name="type">
                    <?php populate_select_from_table("Types", $session["type"], "SELECT", FALSE); ?>
                    </SELECT></TD>
                <TD class="form1">Division: <SELECT name="divisionid">
                    <?php populate_select_from_table("Divisions", $session["divisionid"], "SELECT", FALSE); ?>
                    </SELECT></TD>
                </TR>
            <TR>
                <TD colspan=2 class="form1">Room Set: <SELECT name="roomset">
                    <?php populate_select_from_table("RoomSets", $session["roomset"], "SELECT", FALSE); ?>
                    </SELECT></TD>
                <TD class="form1">Pub. Status: <SELECT name="pubstatusid">
                    <?php populate_select_from_table("PubStatuses", $session["pubstatusid"], "SELECT", FALSE); ?>
                    </SELECT></TD>
                <TD class="form1">Pub. No.:<INPUT type="text" size=10 name="pubno" 
                    value="<?php echo htmlspecialchars($session["pubno"],ENT_COMPAT);?>"</TD>  
                </TR>
            </TABLE>

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
        
        <DIV id="pubchar"> <!-- Publication Characteristics -->
              <LABEL class="bb" style="text-align: center">Publication Characteristics</LABEL>
              <DIV class="select_set">  <!-- lower box -->

                <DIV class="ib">
                  <LABEL class="bb" for="pubcharsrc">Possible Characteristics</LABEL>
                  <SELECT class="select_l" id="pubcharsrc" name="pubcharsrc" size=6 multiple><?php populate_multisource_from_table("PubCharacteristics", $session["pubchardest"]); ?></SELECT>
                </DIV>

                <DIV class="ib" style="vertical-align: top">
                  <LABEL class="bb">&nbsp;</LABEL>
                  <BUTTON onclick="fadditems(document.sessform.pubcharsrc,document.sessform.pubchardest)" 
                          name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</BUTTON>
                  <BUTTON onclick="fdropitems(document.sessform.pubcharsrc,document.sessform.pubchardest)" 
                          name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</BUTTON>
                </DIV>

                <DIV class="ib" >
                  <LABEL class="bb" style="text-align: center" for="pubchardest[]">Selected Characteristics</LABEL>
                  <SELECT class="select_r" id="pubchardest" name="pubchardest[]" size=6 multiple >
                    <?php populate_multidest_from_table("PubCharacteristics", $session["pubchardest"]); ?>
                  </SELECT>
                </DIV>
              </DIV> <!-- lower box -->
            </DIV> <!-- Features -->        
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
              <SELECT name="kids"><?php populate_select_from_table("KidsCategories", $session["kids"], "SELECT", FALSE); ?></SELECT>
            </DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="invguest">Invited Guests Only? </LABEL>
            <DIV class="ib"> <input type="checkbox" value="invguest" id="invguest" <?php if ($session["invguest"]) {echo " checked ";} ?> name="invguest"> </DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="signup">Sign up Req.?</LABEL>
            <DIV class="ib"><input type="checkbox" value="signup" id="signup" <?php if ($session["signup"]) {echo " checked ";} ?> name="signup"></DIV>
          </DIV>

          <DIV class="bb">
            <LABEL class="ib" for="status">Status:</LABEL>
            <DIV class="ib">
              <SELECT name="status"><?php populate_select_from_table("SessionStatuses", $session["status"], "", FALSE); ?></SELECT>
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
