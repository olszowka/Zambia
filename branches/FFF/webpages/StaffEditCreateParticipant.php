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

$query= <<<EOD
SELECT
    permroleid,
    permrolename,
    notes AS permrolenotes
  FROM
      PermissionRoles
EOD;
if (($result=mysql_query($query,$link))===false) {
  $message_error="Error retrieving data from database<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
if (0==($rows=mysql_num_rows($result))) {
  $message_error="Database query did not return any rows.<BR>\n";
  $message_error.=$query;
  RenderError($title,$message_error);
  exit();
}
for ($i=1; $i<=$rows; $i++) {
  $permrole_arr[$i]=mysql_fetch_array($result,MYSQL_ASSOC);
}

if ($action=="create") { //initialize participant array
  $title="Add Participant";
  staff_header($title);

  // If the information has already been added, and we are
  // on the return loop, add the Participant to the database.
  if ((isset ($_POST['update'])) and ($_POST['update']=="Yes")) {
    create_participant ($_POST,$permrole_arr);
  }

  // Get a set of bioinfo, not for the info, but for the arrays.
  $bioinfo=getBioData($_SESSION['badgeid']);

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

      // Clear the values.
      $participant_arr[$keyname]="";
    }
  }

  // Clear the values.
  $participant_arr['password']=md5("changeme");
  $participant_arr['badgeid']="auto-assigned";
  $participant_arr['bestway']=""; //null means hasn't logged in yet.
  $participant_arr['interested']=""; //null means hasn't logged in yet.
  $participant_arr['permroleid']=""; //null means hasn't logged in yet.
  $participant_arr['altcontact']="";
  $participant_arr['prognotes']="";
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
  RenderEditCreateParticipant($action,$participant_arr,$permrole_arr,$message_warn,$message_error);
  correct_footer();
 }

 else { // get participant array from database
   $title="Edit Participant";
   staff_header($title);

   // Collaps the three choices into one
   if ($_POST["partidl"]!=0) {$_POST["partid"]=$_POST["partidl"];}
   if ($_POST["partidf"]!=0) {$_POST["partid"]=$_POST["partidf"];}
   if ($_POST["partidp"]!=0) {$_POST["partid"]=$_POST["partidp"];}

   if (isset($_POST["partid"])) {
     $selpartid=$_POST["partid"];
   } elseif (isset($_GET["partid"])) {
     $selpartid=$_GET["partid"];
   } else {
     $selpartid=0;
   }
   
   //Choose the individual from the database
   select_participant($selpartid, "StaffEditCreateParticipant.php?action=edit");
   
   //Stop page here if and individual has not yet been selected
   if ($selpartid==0) {
     correct_footer();
     exit();
   }
   
   //If we are on the loop with an update, update the database
   // with the current version of the information
   if ((isset ($_POST['update'])) and ($_POST['update'] == "Yes")) {
     edit_participant ($_POST,$permrole_arr);
   }

   //Get Participant information for updating
   $participant_arr['badgeid']=$selpartid;
   $partid=mysql_real_escape_string($selpartid,$link);
   $query= <<<EOD
SELECT
    CD.badgeid,
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
    P.pubsname,
    P.altcontact,
    P.prognotes,
    group_concat(U.permroleid) as 'permroleid_list'
  FROM 
      CongoDump CD
    JOIN Participants P USING (badgeid)
    JOIN UserHasPermissionRole U USING (badgeid)
  WHERE
    CD.badgeid='$selpartid'
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

   // Get a set of bioinfo, and map it to the appropriate $participant_arr.
   $bioinfo=getBioData($selpartid);

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

       // Clear the values.
       $participant_arr[$keyname]=$bioinfo[$keyname];
     }
   }
   RenderEditCreateParticipant($action,$participant_arr,$permrole_arr,$message_warn,$message_error);
   // Show previous notes added, for references, and end page
   show_participant_notes ($selpartid);
 }
?>
