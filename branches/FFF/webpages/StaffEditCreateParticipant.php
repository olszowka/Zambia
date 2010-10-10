<?php
    require_once ('StaffCommonCode.php');
    require ('StaffEditCreateParticipant_FNC.php');
    if (isset($_GET['action'])) {
        $action=$_GET['action'];
        }
      elseif (isset($_POST['action'])) {
        $action=$_POST['action'];
        }
      else {
        $title="Edit or Add Participant";
        $message_error="Required parameter 'action' not found.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    if (!($action=="edit"||$action=="create")) {
        $title="Edit or Add Participant";
        $message_error="Parameter 'action' contains invalid value.  Can't continue.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    if ($action=="create") { //initialize participant array
            $title="Add Participant";
            staff_header($title);
            $participant_arr['password']="changeme";
	    $participant_arr['badgeid']="auto-assigned";
            $participant_arr['bestway']=""; //null means hasn't logged in yet.
            $participant_arr['interested']=""; //null means hasn't logged in yet.
            $participant_arr['permroleid']=""; //null means hasn't logged in yet.
            $participant_arr['bio']="";
            $participant_arr['altcontact']="";
            $participant_arr['prognotes']="";
            $participant_arr['bioeditstatusid']=1; //not edited -- whatever is first step
            $participant_arr['pubsname']="";
            $participant_arr['firstname']="";
            $participant_arr['lastname']="";
            $participant_arr['badgename']="";
            $participant_arr['phone']="";
            $participant_arr['email']="";
            $participant_arr['postaddress1']="";
	    $participant_arr['postaddress2']="";
	    $participant_arr['postcity']="";
	    $participant_arr['poststate']="";
	    $participant_arr['postzip']="";
            }
        else { // get participant array from database
            $title="Edit Participant";
            staff_header($title);
            if (isset($_POST["partid"])) {
                    $selpartid=$_POST["partid"];
                    }
                elseif (isset($_GET["partid"])) {
                    $selpartid=$_GET["partid"];
                    }
                else {
                    $selpartid=0;
                    }

	    //Choose the individual from the database
            select_participant($selpartid, "StaffEditCreateParticipant.php?action=edit");

	    //Stop page here if and individual has not yet been selected
            if ($selpartid==0) {
                staff_footer();
                exit();
                }

	    //Get Participant information for updating
	    $participant_arr['badgeid']=$selpartid;
            $partid=mysql_real_escape_string($selpartid,$link);
            $query= <<<EOD
SELECT
    CD.firstname,
    CD.lastname,
    CD.badgename,
    CD.phone,
    CD.email,
    CD.postaddress1,
    CD.postaddress2,
    CD.postcity,
    CD.poststate,
    CD.postzip,
    CD.regtype,
    P.bestway,
    P.interested,
    P.bio,
    P.pubsname,
    P.altcontact,
    P.prognotes,
    group_concat(U.permroleid) as 'permroleid_list'
  FROM 
      CongoDump CD
    JOIN Participants P USING (badgeid)
    JOIN UserHasPermissionRole U USING (badgeid)
  WHERE
    badgeid='$selpartid'
EOD;
            if (($result=mysql_query($query,$link))===false) {
                $message_error="Error retrieving data from database<BR>\n";
                $message_error.=$query;
                RenderError($title,$message_error);
                exit();
                }
            if (mysql_num_rows($result)!=1) {
                $message_error="Database query did not return expected number of rows (1).<BR>\n";
                $message_error.=$query;
                RenderError($title,$message_error);
                exit();
                }
            $participant_arr=mysql_fetch_array($result,MYSQL_ASSOC);
	    $permroleid_arr=explode(",", $participant_arr['permroleid_list']);
            }
    RenderEditCreateParticipant($action,$participant_arr,$message_warn,$message_error);
    
?>
<FORM name="partnoteform" method=POST action="NoteOnParticipant.php">
<INPUT type="hidden" name="partid" value="<?php echo $selpartid; ?>">
<DIV class="titledtextarea">
  <LABEL for="note">Note:</LABEL>
  <TEXTAREA name="note" rows=6 cols=72></TEXTAREA>
</DIV>
<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
// Show previous notes added, for references, and end page
ShowNotesOnParticipant($selpartid);
?>