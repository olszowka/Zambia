<?php
    // This function will build the queries for inserting to or updating the database
    // Returns number of queries to execute
    // 1st Query: Participants
    // 2nd Query: CongoDump
    // 3rd Query: ParticipantAvailabilityTimes
function MakeQueryEditCreateParticipant($action, &$query_arr, $participant_arr) {
    if ($action=='create') {
            $query_arr[1] = 'INSERT INTO ';
            $query_arr[1].= "Participants\n";
            $query_arr[1].= "        (badgeid, password, bestway, interested, bio, biolockedby, pubsname,\n";
            $query_arr[1].= "         willparteng, willpartengtrans, willpartfre, willpartfretrans,\n";
            $query_arr[1].= "         speaksEnglish, speaksFrench, speaksOther, otherLangs, datacleanupid )\n";
            $query_arr[1].= "    VALUES\n";
            $query_arr[1].= "        (";
            $query_arr[1].= "'".mysql_real_escape_string($participant_arr['badgeid'])."',";
            $query_arr[1].= "'".mysql_real_escape_string($participant_arr['password'])."',";
            $query_arr[1].= "'".mysql_real_escape_string($participant_arr['bestway'])."',";
            $query_arr[1].= (($participant_arr['interested']=='')?"NULL":$participant_arr['interested']).",";
            $query_arr[1].= "'".mysql_real_escape_string($participant_arr['bio'])."',";
            $query_arr[1].= "NULL,"; // biolockedby
            $query_arr[1].= "'".mysql_real_escape_string($participant_arr['pubsname'])."',\n";
            $query_arr[1].= "        ".(($participant_arr['willparteng']=='1')?'1':'0').",";    
            $query_arr[1].= (($participant_arr['willpartengtrans']=='1')?'1':'0').",";    
            $query_arr[1].= (($participant_arr['willpartfre']=='1')?'1':'0').",";    
            $query_arr[1].= (($participant_arr['willpartfretrans']=='1')?'1':'0').",";    
            $query_arr[1].= (($participant_arr['speaksenglish']=='1')?'1':'0').",";    
            $query_arr[1].= (($participant_arr['speaksfrench']=='1')?'1':'0').",";    
            $query_arr[1].= (($participant_arr['speaksother']=='1')?'1':'0').",";    
            $query_arr[1].= "'".mysql_real_escape_string($participant_arr['otherlangs'])."',";
            if (!validate_integer($participant_arr['datacleanupid'],1,99)) $participant_arr['datacleanupid']=1;
            $query_arr[1].= $participant_arr['datacleanupid'].")\n"; 
            }
        else { // edit/update
            $query_arr[1] = "UPDATE Participants set\n";
            $query_arr[1].= "    bestway='".mysql_real_escape_string($participant_arr['bestway'])."',\n";
            $query_arr[1].= "    interested=".(($participant_arr['interested']=='')?"NULL":$participant_arr['interested']).",\n";
            $query_arr[1].= "    bio='".mysql_real_escape_string($participant_arr['bio'])."',\n";
            $query_arr[1].= "    pubsname='".mysql_real_escape_string($participant_arr['pubsname'])."',\n";
            $query_arr[1].= "    willparteng=".(($participant_arr['willparteng']=='1')?'1':'0').",";
            $query_arr[1].= "    willpartengtrans=".(($participant_arr['willpartengtrans']=='1')?'1':'0').",";
            $query_arr[1].= "    willpartfre=".(($participant_arr['willpartfre']=='1')?'1':'0').",";
            $query_arr[1].= "    willpartfretrans=".(($participant_arr['willpartfretrans']=='1')?'1':'0').",";
            $query_arr[1].= "    speaksEnglish=".(($participant_arr['speaksenglish']=='1')?'1':'0').",";
            $query_arr[1].= "    speaksFrench=".(($participant_arr['speaksfrench']=='1')?'1':'0').",";
            $query_arr[1].= "    speaksOther=".(($participant_arr['speaksother']=='1')?'1':'0').",";
            $query_arr[1].= "    otherLangs='".mysql_real_escape_string($participant_arr['otherlangs'])."'\n";
            if (validate_integer($participant_arr['datacleanupid'],1,99)) {
                $query_arr[1].= "    ,datacleanupid=".$participant_arr['datacleanupid']."\n";
                }
            $query_arr[1].= "WHERE badgeid='{$participant_arr['badgeid']}'";
            }
    $query_arr[2] = ($action=='create') ? 'INSERT INTO ' : 'REPLACE ';
    $query_arr[2].= "CongoDump (badgeid, firstname, lastname, badgename, phone, email, postaddress, regtype) VALUES (";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['badgeid'])."',";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['firstname'])."',";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['lastname'])."',";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['badgename'])."',";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['phone'])."',";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['email'])."',";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['postaddress'])."',";
    $query_arr[2].= "'".mysql_real_escape_string($participant_arr['regtype'])."');";
    return(2);
    }
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
    staff_header($title);
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
                     value="<?php echo htmlentities($participant_arr["firstname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="lastname">Last Name: </LABEL><INPUT type="text" size=13 name="lastname" id="lastname"
                     value="<?php echo htmlentities($participant_arr["lastname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN>&nbsp;<BUTTON type=button onclick="fpopdefaults()" value="noop">Populate Defaults</BUTTON>&nbsp;</SPAN>
                <SPAN><LABEL for="badgeid">Participant #: </LABEL><INPUT type="text" size=4 name="badgeid" readonly
                     value="<?php echo htmlentities($participant_arr["badgeid"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="datacleanupid">Data Tuning: </LABEL><SELECT name="datacleanupid">
<?php
populate_select_from_table('DataCleanupRef',$participant_arr['datacleanupid'],'',FALSE);
?>
                    </SELECT></SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="pubsname">Name for Publications: </LABEL><INPUT type="text" size=20 name="pubsname" id="pubsname"
                     value="<?php echo htmlentities($participant_arr["pubsname"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="badgename">Badge Name: </LABEL><INPUT type="text" size=20 name="badgename" id="badgename"
                     value="<?php echo htmlentities($participant_arr["badgename"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="interested">Will participate and attend: </LABEL><SELECT name="interested">
                    <OPTION value="" <?php if ($participant_arr["interested"]=="") echo "selected";?> >Not yet logged in</OPTION>
                    <OPTION value="0" <?php if ($participant_arr["interested"]=="0") echo "selected";?> >Did not answer</OPTION>
                    <OPTION value="1" <?php if ($participant_arr["interested"]=="1") echo "selected";?> >Yes</OPTION>
                    <OPTION value="2" <?php if ($participant_arr["interested"]=="2") echo "selected";?> >No</OPTION>
                    <OPTION value="3" <?php if ($participant_arr["interested"]=="3") echo "selected";?> >Hide Duplicate</OPTION>
                    </SELECT>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="willmoderate">Will moderate: </LABEL>
                    <INPUT type="checkbox" value=1 name="willmoderate"
<?php if ($participant_arr['willmoderate']==1) echo " checked "; ?>
                    ></SPAN>
                <SPAN><LABEL for="speaksenglish">&nbsp;&nbsp;Speaks English: </LABEL>
                    <INPUT type="checkbox" value=1 name="speaksenglish"
<?php if ($participant_arr['speaksenglish']==1) echo " checked "; ?>
                    ></SPAN>
                <SPAN><LABEL for="speaksfrench">&nbsp;&nbsp;Speaks French: </LABEL>
                    <INPUT type="checkbox" value=1 name="speaksfrench"
<?php if ($participant_arr['speaksfrench']==1) echo " checked "; ?>
                    ></SPAN>
                <SPAN><LABEL for="speaksother">&nbsp;&nbsp;Speaks other languages: </LABEL>
                    <INPUT type="checkbox" value=1 name="speaksother"
<?php if ($participant_arr['speaksother']==1) echo " checked "; ?>
                    ></SPAN>
                <SPAN><LABEL for="otherlangs">&nbsp;&nbsp;Details: </LABEL>
                    <INPUT type="text" name="otherlangs"
<?php echo " value=\"".htmlentities($participant_arr['otherlangs'])."\">\n"; ?>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
                Will participate on sessions ...
                <SPAN><LABEL for="willparteng">-in English: </LABEL>
                    <INPUT type="checkbox" value=1 name="willparteng"
<?php if ($participant_arr['willparteng']==1) echo " checked "; ?>
                    ></SPAN>
                <SPAN><LABEL for="willpartengtrans">&nbsp;&nbsp;-in English with translation: </LABEL>
                    <INPUT type="checkbox" value=1 name="willpartengtrans"
<?php if ($participant_arr['willpartengtrans']==1) echo " checked "; ?>
                    ></SPAN>
                <SPAN><LABEL for="willpartfre">&nbsp;&nbsp;-in French: </LABEL>
                    <INPUT type="checkbox" value=1 name="willpartfre"
<?php if ($participant_arr['willpartfre']==1) echo " checked "; ?>
                    ></SPAN>
                <SPAN><LABEL for="willpartfretrans">&nbsp;&nbsp;-in French with translation: </LABEL>
                    <INPUT type="checkbox" value=1 name="willpartfretrans"
<?php if ($participant_arr['willpartfretrans']==1) echo " checked "; ?>
                    ></SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="email">Email Address: </LABEL><INPUT type="text" size=36 name="email"
                     value="<?php echo htmlentities($participant_arr["email"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="bestway">Preferred way to be contacted: </LABEL><SELECT name="bestway">
                    <OPTION value="" <?php if ($participant_arr["bestway"]=="") echo "selected";?> >Did not answer</OPTION>
                    <OPTION value="" <?php if ($participant_arr["bestway"]=="Email") echo "selected";?> >Email</OPTION>
                    <OPTION value="" <?php if ($participant_arr["bestway"]=="Phone") echo "selected";?> >Phone</OPTION>
                    <OPTION value="" <?php if ($participant_arr["bestway"]=="Postal mail") echo "selected";?> >Postal mail</OPTION>
                    </SELECT>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="postaddress">Postal Address: </LABEL><INPUT type="text" size=80 name="postaddress"
                     value="<?php echo htmlentities($participant_arr["postaddress"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="bio" style="vertical-align: top">Biography: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="bio" ><?php 
echo htmlentities($participant_arr["bio"],ENT_NOQUOTES);
?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="phone">Phone: </LABEL><INPUT type="text" size=14 name="phone"
                     value="<?php echo htmlentities($participant_arr["phone"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="regtype">Registration Type: </LABEL><INPUT type="text" size=14 name="regtype"
                     value="<?php echo htmlentities($participant_arr["regtype"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
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
