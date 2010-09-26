<?php
    // This function will output the page with the form to add or create a participant
    // Variables
    //     action: "create" or "edit"
    //     participant_arr: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderEditCreateParticipant ($action, $participant_arr, $message1, $message2) {
    if ($action=="create") {
            $title="Add New Participant";
            }
        elseif ($action=="edit") {
            $title="Edit Participant";
            }
        else {
            exit();
            }
    //    staff_header($title);
    // still inside function RenderEditCreateParticipant
    if (strlen($message1)>0) {
      echo "<P id=\"message1\"><font color=red>".$message1."</font></P>\n";
    }
    if (strlen($message2)>0) {
      echo "<P id=\"message2\"><font color=red>".$message2."</font></P>\n";
      exit(); // If there is a message2, then there is a fatal error.
    }
    //error_log("Zambia: ".print_r($participant_arr,TRUE));
  ?>
    <DIV class="formbox">
        <FORM name="partform" class="bb"  method=POST action="SubmitEditCreateParticipant.php">
            <INPUT type="hidden" name="action" value="<?php echo htmlspecialchars($action,ENT_COMPAT);?>">
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
                    <BUTTON class="ib" type=reset value="reset">Reset</BUTTON>
                    <BUTTON class="ib" type=submit value="save">Save</BUTTON>
                    </TD></TR></TABLE>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="firstname">First Name: </LABEL><INPUT type="text" size=7 name="firstname" id="firstname" 
                     value="<?php echo htmlspecialchars($participant_arr["firstname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="lastname">Last Name: </LABEL><INPUT type="text" size=13 name="lastname" id="lastname"
                     value="<?php echo htmlspecialchars($participant_arr["lastname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN>&nbsp;<BUTTON type=button onclick="fpopdefaults()" value="noop">Populate Defaults</BUTTON>&nbsp;</SPAN>
                <SPAN><LABEL for="badgeid">Badge ID: <?php echo $participant_arr["badgeid"];?></LABEL>&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="pubsname">Name for Publications: </LABEL><INPUT type="text" size=20 name="pubsname" id="pubsname"
                     value="<?php echo htmlspecialchars($participant_arr["pubsname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="badgename">Badge Name: </LABEL><INPUT type="text" size=20 name="badgename" id="badgename"
                     value="<?php echo htmlspecialchars($participant_arr["badgename"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="interested">Will participate and attend: </LABEL><SELECT name="interested">
                    <OPTION value="" <?php if ($participant_arr["interested"]=="") echo "selected";?> >Not yet logged in</OPTION>
                    <OPTION value="0" <?php if ($participant_arr["interested"]=="0") echo "selected";?> >Did not answer</OPTION>
                    <OPTION value="1" <?php if ($participant_arr["interested"]=="1") echo "selected";?> >Yes</OPTION>
                    <OPTION value="2" <?php if ($participant_arr["interested"]=="2") echo "selected";?> >No</OPTION>
                    </SELECT>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="email">Email Address: </LABEL><INPUT type="text" size=36 name="email"
                     value="<?php echo htmlspecialchars($participant_arr["email"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="bestway">Preferred way to be contacted: </LABEL><SELECT name="bestway">
                    <OPTION value="" <?php if ($participant_arr["bestway"]=="") echo "selected";?> >Did not answer</OPTION>
                    <OPTION value="Email" <?php if ($participant_arr["bestway"]=="Email") echo "selected";?> >Email</OPTION>
                    <OPTION value="Phone" <?php if ($participant_arr["bestway"]=="Phone") echo "selected";?> >Phone</OPTION>
                    <OPTION value="Postal mail" <?php if ($participant_arr["bestway"]=="Postal mail") echo "selected";?> >Postal mail</OPTION>
                    </SELECT>
                <SPAN><LABEL for="permroleid">Level of participation: </LABEL><SELECT name="permroleid">
                    <OPTION value="" <?php if ($participant_arr["permroleid"]=="") echo "selected";?> >Not determined</OPTION>
                    <OPTION value="3" <?php if ($participant_arr["permroleid"]=="3") echo "selected";?> >Presenter</OPTION>
                    <OPTION value="5" <?php if ($participant_arr["permroleid"]=="5") echo "selected";?> >Volunteer</OPTION>
                    <OPTION value="4" <?php if ($participant_arr["permroleid"]=="4") echo "selected";?> >Brainstorm</OPTION>
                    <OPTION value="2" <?php if ($participant_arr["permroleid"]=="2") echo "selected";?> >Staff</OPTION>
                    </SELECT>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="postaddress1">Postal Address line 1: </LABEL><INPUT type="text" size=80 name="postaddress1"
                     value="<?php echo htmlspecialchars($participant_arr["postaddress1"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="postaddress2">Postal Address line 2: </LABEL><INPUT type="text" size=80 name="postaddress2"
                     value="<?php echo htmlspecialchars($participant_arr["postaddress2"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="postcity">Postal City: </LABEL><INPUT type="text" size=20 name="postcity"
                     value="<?php echo htmlspecialchars($participant_arr["postcity"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="poststate">State: </LABEL><INPUT type="text" size=2 name="poststate"
                     value="<?php echo htmlspecialchars($participant_arr["poststate"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="postzip">Zip: </LABEL><INPUT type="text" size=10 name="postzip"
                     value="<?php echo htmlspecialchars($participant_arr["postzip"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="bio" style="vertical-align: top">Biography: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="bio" ><?php echo htmlspecialchars($participant_arr["bio"],ENT_NOQUOTES);?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="altcontact" style="vertical-align: top">Alternative ways to contact: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="altcontact" ><?php echo htmlspecialchars($participant_arr["altcontact"],ENT_NOQUOTES);?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="prognotes" style="vertical-align: top">Additional Progranning notes: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="prognotes" ><?php echo htmlspecialchars($participant_arr["prognotes"],ENT_NOQUOTES);?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="phone">Phone: </LABEL><INPUT type="text" size=14 name="phone"
                     value="<?php echo htmlspecialchars($participant_arr["phone"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="regtype">Registration Type:: </LABEL><INPUT type="text" size=14 name="regtype"
                     value="<?php echo htmlspecialchars($participant_arr["regtype"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
                    <BUTTON class="ib" type=reset value="reset">Reset</BUTTON>
                    <BUTTON class="ib" type=submit value="save">Save</BUTTON>
                    </TD></TR></TABLE>
                </DIV>
      </FORM>
    </DIV>
<?php staff_footer(); } ?>
