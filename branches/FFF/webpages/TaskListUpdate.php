<?php
$title="Task List Update";
require_once('StaffCommonCode.php');

staff_header($title);

// Submit the task, if there was one, when this was called
if (isset($_POST["activitynotes"])) {
  if ($_POST["activityid"] == "-1") {
    $element_array=array('activity','activitynotes','badgeid','targettime','donestate');
    $value_array=array($_POST['activity'],$_POST['activitynotes'],$_POST['assignedid'],$_POST['targettime'],"N");
    //    $value_array=array($_POST['activity'],$_POST['activitynotes'],"123",$_POST['targettime'],"N");
    submit_table_element($link, $title, "TaskList", $element_array, $value_array);
  } else {
    if ($_POST['donestate']=="Y") {
      if (isset($_POST['donetime'])) {
	$donetime=",donetime='".$_POST['donetime']."'";
      } else {
	$donetime=",donetime=CURRENT_DATE";
      }
    } else { $donetime=""; }
    $pairedvalue_array=array("activitynotes='".$_POST['activitynotes']."'","badgeid='".$_POST['assignedid']."'","targettime='".$_POST['targettime']."'","donestate='".$_POST['donestate']."'".$donetime);
    $match_field="activityid";
    $match_value=$_POST['activityid'];
    update_table_element($link, $title, "TaskList", $pairedvalue_array, $match_field, $match_value);
  }
 }

// Carry over the task list element, from the form before, if they exist
if (isset($_POST["activityid"])) {
  $activityid=$_POST["activityid"];
 }
 elseif (isset($_GET["activityid"])) {
  $activityid=$_GET["activityid"];
 }
 else {
   $activityid=0;
 }

$query=<<<EOD
SELECT
    activityid,
    activity
  FROM
      TaskList
  ORDER BY
     activityid

EOD;

if (!$activityresult=mysql_query($query,$link)) {
  $message=$query."<BR>Error querying database. Unable to continue.<BR>";
  echo "<P class\"errmsg\">".$message."\n";
  staff_footer();
  exit();
 }

?>

<FORM name="tasklistselect" method=POST action="TaskListUpdate.php">
<DIV><LABEL for="activityid">Select Task</LABEL>
<SELECT name="activityid">
<?php 
  echo "     <OPTION value=0 ".(($assignedid==0)?"selected":"").">Select task</OPTION>\n";
  while (list($taskid,$task)= mysql_fetch_array($activityresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"$taskid\"".(($activityid==$taskid)?"selected":"");
    echo ">".htmlspecialchars($task)."</OPTION>\n";
  }
?>
</SELECT></DIV>
<BUTTON class="SubmitButton" type="submit" name="submit" >Submit</BUTTON>
</FORM>
<HR>
<?php
// Stop page here if and individual has not yet been selected
if ($activityid==0) {
  staff_footer();
  exit();
 }

if ($activityid==-1) {
// Update note through form below

  $query0=<<<EOD
SELECT
    P.badgeid,
    CD.lastname,
    CD.firstname,
    CD.badgename,
    P.pubsname
  FROM
      Participants P
    JOIN CongoDump CD USING (badgeid)
    JOIN UserHasPermissionRole UP USING (badgeid)
  WHERE
     UP.permroleid=5
  ORDER BY
     P.pubsname

EOD;

  if (!$nameresult=mysql_query($query0,$link)) {
    $message=$query0."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
  }

?>

<FORM name="tasklistform" method=POST action="TaskListUpdate.php">
<DIV class="titledtextarea">
<INPUT type="hidden" name="activityid" value="<?php echo $activityid; ?>">
<LABEL for="assignedid">Person assigned:</LABEL>
<SELECT name="assignedid">
<?php 
  echo "     <OPTION value=0 ".(($assignedid==0)?"selected":"").">Select Participant (Pubsname)</OPTION>\n";
  while (list($partid,$lastname,$firstname,$badgename,$pubsname)= mysql_fetch_array($nameresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$partid."\" ".(($assignedid==$partid)?"selected":"");
    echo ">".htmlspecialchars($pubsname)."/".htmlspecialchars($badgename);
    echo " (".htmlspecialchars($lastname).", ".htmlspecialchars($firstname).") - ".$partid."</OPTION>\n";
  }
?>
</SELECT>
<LABEL for"activity">Task:</LABEL>
  <INPUT type="text" size="25" name="activity" id="activity">
  <LABEL for="activitynotes">Note:</LABEL>
  <TEXTAREA name="activitynotes" rows=6 cols=72><?php echo $activitynotes ?></TEXTAREA>
  <LABEL for="targettime">Targeted Completion Time: (eg: 2038-12-12)</LABEL>
  <INPUT type="text" size=10 name="targettime" id="tartettime" value="<?php echo htmlspecialchars($targettime) ?>">
</DIV>

<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php } else {

// Get the selected note information

  $query= <<<EOD
SELECT
    activity,
    activitynotes,
    badgeid,
    targettime,
    donestate,
    donetime
  FROM
      TaskList
  WHERE
    activityid='$activityid'

EOD;

  list($rows,$header_array,$task_array)=queryreport($query,$link,$title,$description,0);

  $activity=$task_array[1]['activity'];
  $activitynotes=$task_array[1]['activitynotes'];
  $assignedid=$task_array[1]['badgeid'];
  $targettime=$task_array[1]['targettime'];
  $donestate=$task_array[1]['donestate'];
  $donetime=$task_array[1]['donetime'];

  $query0=<<<EOD
SELECT
    P.badgeid,
    CD.lastname,
    CD.firstname,
    CD.badgename,
    P.pubsname
  FROM
      Participants P
    JOIN CongoDump CD USING (badgeid)
    JOIN UserHasPermissionRole UP USING (badgeid)
  WHERE
     UP.permroleid=5
  ORDER BY
     P.pubsname

EOD;

  if (!$nameresult=mysql_query($query0,$link)) {
    $message=$query0."<BR>Error querying database. Unable to continue.<BR>";
    echo "<P class\"errmsg\">".$message."\n";
    staff_footer();
    exit();
  }

  // Update note through form below
  ?>

<FORM name="tasklistform" method=POST action="TaskListUpdate.php">
<INPUT type="hidden" name="activityid" value="<?php echo $activityid; ?>">
<DIV class="titledtextarea">
<LABEL for="assigned">Person assigned:</LABEL>
<SELECT name="assignedid">
<?php 
  echo "     <OPTION value=0 ".(($assignedid==0)?"selected":"").">Select Participant (Pubsname)</OPTION>\n";
  while (list($partid,$lastname,$firstname,$badgename,$pubsname)= mysql_fetch_array($nameresult, MYSQL_NUM)) {
    echo "     <OPTION value=\"".$partid."\" ".(($assignedid==$partid)?"selected":"");
    echo ">".htmlspecialchars($pubsname)."/".htmlspecialchars($badgename);
    echo " (".htmlspecialchars($lastname).", ".htmlspecialchars($firstname).") - ".$partid."</OPTION>\n";
  }
?>
</SELECT>
<LABEL for"activity">Task:</LABEL><?php echo $activity ?>
  <LABEL for="activitynotes">Note:</LABEL>
  <TEXTAREA name="activitynotes" rows=6 cols=72><?php echo $activitynotes ?></TEXTAREA>
  <LABEL for="targettime">Targeted Completion Time: (eg: 2038-12-12)</LABEL>
  <INPUT type="text" size=10 name="targettime" id="targettime" value="<?php echo htmlspecialchars($targettime) ?>">
  <?php if ($donestate=="Y") { ?>
  <LABEL for="finished">Finished at:</LABEL><?php echo $donetime; ?>
  <INPUT type="hidden" name="donetime" value="<?php echo $donetime ?>">
  <INPUT type="hidden" name="donestate" value="<?php echo $donestate ?>">
  <?php } else { ?>
  <LABEL for="finished">Is it done?</LABEL>
  <INPUT type="radio" name="donestate" id="donestate" value="Y" <?php if ($donestate=="Y") {echo "checked";} ?>> Yes, it is finished.<br>
  <INPUT type="radio" name="donestate" id="donestate" value="P" <?php if ($donestate=="P") {echo "checked";} ?>> It is partially done.<br>
  <INPUT type="radio" name="donestate" id="donestate" value="N" <?php if ($donestate=="N") {echo "checked";} ?>> It has not yet been begun.<br>
  <?php } ?>
       
</DIV>

<BUTTON class="SubmitButton" type="submit" name="submit" >Update</BUTTON>
</FORM>

<?php
 }
staff_footer();
?>
