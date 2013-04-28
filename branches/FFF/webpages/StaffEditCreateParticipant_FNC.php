<?php
    // This function will output the page with the form to add or create a participant
    // Variables
    //     action: "create" or "edit"
    //     participant_arr: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderEditCreateParticipant ($action, $participant_arr, $permrole_arr, $message1, $message2) {
    $ReportDB=REPORTDB; // make it a variable so it can be substituted
    $BioDB=BIODB; // make it a variable so it can be substituted

    // Tests for the substituted variables
    if ($ReportDB=="REPORTDB") {unset($ReportDB);}
    if ($BiotDB=="BIODB") {unset($BIODB);}

    // Get the various length limits
    $limit_array=getLimitArray();

    if (strlen($message1)>0) {
      echo "<P id=\"message1\"><font color=red>Message: ".$message1."</font></P>\n";
    }
    if (strlen($message2)>0) {
      echo "<P id=\"message2\"><font color=red>Message: ".$message2."</font></P>\n";
      exit(); // If there is a message2, then there is a fatal error.
    }
    //error_log("Zambia: ".print_r($participant_arr,TRUE));

    //Get the bioinfo, not for the info, but for the arrays.
    $bioinfo=getBioData($_SESSION['badgeid']);

  ?>
    <DIV class="formbox">
        <FORM name="partform" class="bb"  method=POST action="StaffEditCreateParticipant.php">
            <INPUT type="hidden" name="action" value="<?php echo htmlspecialchars($action,ENT_COMPAT);?>">
            <INPUT type="hidden" name="partid" value="<?php echo $participant_arr["badgeid"]; ?>">
            <INPUT type="hidden" name="password" value="<?php echo $participant_arr["password"]; ?>">
            <INPUT type="hidden" name="update" value="Yes">
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
<?php /*                    <BUTTON class="ib" type=reset value="reset">Reset</BUTTON>  trimmed for sanity purposes */ ?>
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
                    <?php populate_select_from_table("$ReportDB.InterestedTypes", $participant_arr['interested'], " ", FALSE); ?>
                    </SELECT>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="email">Email Address: </LABEL><INPUT type="text" size=36 name="email"
                     value="<?php echo htmlspecialchars($participant_arr["email"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="bestway">Preferred way to be contacted: </LABEL><SELECT name="bestway">
                    <OPTION value="" <?php if ($participant_arr["bestway"]=="") echo "selected";?> >Did not answer</OPTION>
                    <OPTION value="Email" <?php if ($participant_arr["bestway"]=="Email") echo "selected";?> >Email</OPTION>
                    <OPTION value="Phone" <?php if ($participant_arr["bestway"]=="Phone") echo "selected";?> >Phone</OPTION>
                    <OPTION value="SMS" <?php if ($participant_arr["bestway"]=="SMS") echo "selected";?> >SMS</OPTION>
                    <OPTION value="Postal mail" <?php if ($participant_arr["bestway"]=="Postal mail") echo "selected";?> >Postal mail</OPTION>
                    <OPTION value="Twitter DM" <?php if ($participant_arr["bestway"]=="Twitter DM") echo "selected";?> >Twitter DM</OPTION>
                    <OPTION value="Fet Life" <?php if ($participant_arr["bestway"]=="Fet Life") echo "selected";?> >Fet Life</OPTION>
                    <OPTION value="Facebook" <?php if ($participant_arr["bestway"]=="Facebook") echo "selected";?> >Facebook</OPTION>
                    <OPTION value="G+" <?php if ($participant_arr["bestway"]=="G+") echo "selected";?> >G+</OPTION>
                    <OPTION value="Instant Messenger" <?php if ($participant_arr["bestway"]=="Instant Messenger") echo "selected";?> >Instant Messenger</OPTION>
                    </SELECT>
                    </SPAN>
                </DIV>
	    <DIV class="denseform">
		<SPAN><LABEL for="permroleid">Level of participation (At least one must be selected):</LABEL>
                    <?php 
			for ($i=2; $i<=count($permrole_arr); $i++) {
			  $pos = strpos($participant_arr['permroleid_list'],"$i");
			  if (may_I($permrole_arr[$i]["permrolename"])) {
			    if ((($permrole_arr[$i]["permrolename"]=="Participant") and ((may_I("Liaison")) or (may_I("SuperLiaison")))) or ($permrole_arr[$i]["permrolename"]!="Participant")) {
			      if($pos === false) {
			        echo "<INPUT type=\"hidden\" name=\"waspermroleid".$i."\" value=\"not\">\n";
                                echo "<INPUT type=\"checkbox\" name=\"permroleid".$i."\" value=\"checked\">".$permrole_arr[$i]["permrolenotes"]."\n";
			      } else {
			        echo "<INPUT type=\"hidden\" name=\"waspermroleid".$i."\" value=\"indeed\">\n";
                                echo "<INPUT type=\"checkbox\" name=\"permroleid".$i."\" value=\"checked\" checked>".$permrole_arr[$i]["permrolenotes"]."\n";
			      }
			    }
			  }
			} 
                    ?>
		    <INPUT type="hidden" name="permroleid_list" value="<?php echo $participant_arr['permroleid_list']; ?>">
		    </SPAN>
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
<?php
              if ((may_I("Programming")) or
		  (may_I("SuperProgramming")) or
		  (may_I("Liaison")) or
		  (may_I("SuperVendor")) or
		  (may_I("SuperLiaison"))) {
		/* We are only updating the raw bios here, so only a 2-depth
		   search happens on biolang and biotypename. */
		$biostate='raw'; // for ($k=0; $k<count($bioinfo['biostate_array']); $k++) {
		for ($i=0; $i<count($bioinfo['biotype_array']); $i++) {
		  for ($j=0; $j<count($bioinfo['biolang_array']); $j++) {

		    // Setup for keyname, to collapse all three variables into one passed name.
		    $biotype=$bioinfo['biotype_array'][$i];
		    $biolang=$bioinfo['biolang_array'][$j];
		    // $biostate=$bioinfo['biostate_array'][$k];
		    $keyname=$biotype."_".$biolang."_".$biostate."_bio";

		    echo "            <DIV class=\"denseform\">\n";
		    echo "                <SPAN><LABEL for=\"$keyname\" style=\"vertical-align: top\">";
		    echo ucfirst($biotype)." ($biolang) Biography";
		    $limit_string="";
		    if (isset($limit_array['max'][$biotype]['bio'])) {
		      $limit_string.=" maximum ".$limit_array['max'][$biotype]['bio'];
		    }
		    if (isset($limit_array['min'][$biotype]['bio'])) {
		      $limit_string.=" minimum ".$limit_array['min'][$biotype]['bio'];
		    }
		    if ($limit_string !="") {
		      echo "<BR>(Limit".$limit_string." characters)";
		    }
		    echo ": </LABEL>\n";
		    echo "                    <TEXTAREA class=\"textlabelarea\" cols=70 name=\"$keyname\" >";
		    echo htmlspecialchars($participant_arr[$keyname],ENT_NOQUOTES)."</TEXTAREA>\n";
		    echo "                    </SPAN>\n";
		    echo "                </DIV>\n";
		  }
		}
	      }?>
            <DIV class="denseform">
                <SPAN><LABEL for="altcontact" style="vertical-align: top">Alternative ways to contact: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="altcontact" ><?php echo htmlspecialchars($participant_arr["altcontact"],ENT_NOQUOTES);?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
            <SPAN><LABEL for="note" style="vertical-align: top">Log Note:</LABEL>
		<TEXTAREA class="textlabelarea" name="note" rows=2 cols=72>Participant entry <?php echo htmlspecialchars($action);?></TEXTAREA>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="prognotes" style="vertical-align: top">Additional Participant notes: </LABEL>
                    <TEXTAREA class="textlabelarea" cols=70 name="prognotes" ><?php echo htmlspecialchars($participant_arr["prognotes"],ENT_NOQUOTES);?></TEXTAREA>
                    </SPAN>
                </DIV>
            <DIV class="denseform">
                <SPAN><LABEL for="phone">Phone: </LABEL><INPUT type="text" size=14 name="phone"
                     value="<?php echo htmlspecialchars($participant_arr["phone"],ENT_COMPAT);?>">&nbsp;&nbsp;</SPAN>
                <SPAN><LABEL for="regtype">Registration Type: </LABEL><SELECT name="regtype">
                    <?php populate_select_from_query("SELECT regtype, regtype FROM $ReportDB.RegTypes", $participant_arr['regtype'], "SELECT", FALSE); ?>
                    </SELECT>
                </DIV>
            <DIV style="margin: 0.5em; padding: 0em"><TABLE style="margin: 0em; padding: 0em" ><COL width=600><COL>
              <TR style="margin: 0em; padding: 0em">
                <TD style="margin: 0em; padding: 0em">&nbsp;</TD>
                <TD style="margin: 0em; padding: 0em">
								<?php /*                    <BUTTON class="ib" type=reset value="reset">Reset</BUTTON> Trimmed for sanity purposes */ ?>
                    <BUTTON class="ib" type=submit value="save">Save</BUTTON>
                    </TD></TR></TABLE>
                </DIV>
      </FORM>
    </DIV>
<?php } ?>
