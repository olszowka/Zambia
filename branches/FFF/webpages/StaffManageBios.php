<?php
global $participant,$message_error,$message2,$congoinfo;
require_once('StaffCommonCode.php');
$conid=$_SESSION['conid'];
$ReportDB=REPORTDB; // make it a variable so it can be substituted
$BioDB=BIODB; // make it a variable so it can be substituted
$LanguageList=LANGUAGE_LIST; // make it a variable so it can be substituted

// Tests for the substituted variables
if ($ReportDB=="REPORTDB") {unset($ReportDB);}
if ($BiotDB=="BIODB") {unset($BIODB);}
if ($LanguageList=="LANGUAGE_LIST") {unset($LanguageList);}

$title="Staff - Manage Participant Biographies";
$description="<P>Report of status of Participant Biographies.</P>";
$additionalinfo ="<P>This report is limited to participants who are currently listed as attending and interested in particpating.</P>\n";

if ((isset($_GET['badgeids'])) AND ($_GET['badgeids']!='')) {
  $additionalinfo.="<P>Click on the participant name in the table below to edit their biography or";
  $additionalinfo.=" <A HREF=StaffManageBios.php>return</A> to the editing matrix.</P>\n";
 } else {
  $additionalinfo.="<P>Select with which category you would like to work with and click on the number.</P>\n";
  $additionalinfo.="<P>(We currently do not use the \"good\" category of bio.)</P>\n";
 }

if (isset($_GET['unlock'])) {
  $unlockresult=unlock_participant($_GET['unlock']);
 }

/* Categories
 Rows:
  No raw bio
  No edited bio
  No good bio (* only liaison)
  Raw bio different from edited bio
  Edited bio different from good bio (* only liaison)
  Raw, edited, and good in agreement (* no links)
 Columns:
  each lang/type combination.  Eg:
  en-us web, en-us book, en-us uri, en-us picture, en-uk web, en-uk book
*/

// Participants
$query= <<<EOD
SELECT 
    B.badgeid,
    biostatename,
    concat(biolang, " ", biotypename) AS col,
    LB.pubsname AS lockedby,
    P.pubsname,
    biotext
  FROM
      $ReportDB.Participants P
    JOIN $BioDB.Bios B USING (badgeid)
    JOIN $BioDB.BioTypes USING (biotypeid)
    JOIN $BioDB.BioStates USING (biostateid)
    JOIN $ReportDB.UserHasPermissionRole UHPR USING (badgeid)
    JOIN $ReportDB.PermissionRoles USING (permroleid)
    JOIN $ReportDB.Interested I USING (badgeid)
    JOIN $ReportDB.InterestedTypes USING (interestedtypeid)
    LEFT JOIN $ReportDB.Participants LB on B.biolockedby = LB.badgeid
   
  WHERE
    interestedtypename in ('Yes') AND
    UHPR.conid=$conid AND
    I.conid=$conid AND
    (permrolename in ('Participant') OR
     permrolename like '%Super%')
EOD;

// Specific set of badgeids.
if ((isset($_GET['badgeids'])) AND ($_GET['badgeids']!='')) {
  $query.=" AND B.badgeid in (".$_GET['badgeids'].")";
 }

// Specific languages.
if (isset($LanguageList)) {
  $query.=" AND biolang in $LanguageList";
 }

// Give some semblance of order to the names
$query.=" ORDER BY P.pubsname";

if (($result=mysql_query($query,$link))===false) {
  $message_error.=$query."<BR>\nError retrieving data from database.\n";
  RenderError($title,$message_error);
  exit();
 }

$numrows=mysql_num_rows($result);

while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
  $check_element[$row['badgeid']][$row['col']][$row['biostatename']]=$row['biotext'];
  $count_badgeid[$row['badgeid']]++;
  $pubsname[$row['badgeid']]=$row['pubsname'];
  /*if (((!isset($lockedby[$row['badgeid']])) or ($lockedby[$row['badgeid']] = "")) and
   (isset($row['lockedby']))) { */
  if (isset($row['lockedby'])) {
    $lockedby[$row['badgeid']]=$row['lockedby'];
   }
  $count_col[$row['col']]++;
 }

//Start the page
topofpagereport($title,$description,$additionalinfo);

// Set up the necessary switches so we can know what exactly is being operated on.
$col_keys=array_keys($count_col);
$badgeid_keys=array_keys($count_badgeid);

if ((isset($_GET['badgeids'])) AND ($_GET['badgeids']!='')) {
  if ($numrows==0) {
    echo "<P>There are no biographies to edit which match your selection.</P>\n";
    echo "<P><A HREF=\"StaffManageBios.php\">Reload the Manage Biographies page.</A></P>\n";
    correct_footer();
    exit();
  }
  echo "<TABLE class=\"grid\" border=1>\n";
  echo "    <TR>\n";
  echo "        <TH>Participant</TH>\n";
  echo "        <TH>Edit Full</TH>\n";
  echo "        <TH>Currently being edited by</TH>\n";
  echo "        </TR>\n";
  for ($i=0; $i<count($count_badgeid); $i++) {
    echo "    <TR>\n";
    $b=$badgeid_keys[$i];
    $p=$pubsname[$badgeid_keys[$i]];
    $bs=$_GET['badgeids'];
    echo "        <TD><A HREF=\"StaffEditBios.php?badgeid=$b&badgeids=$bs\">$p</A></TD>\n";
    echo "        <TD><A HREF=\"StaffEditCreateParticipant.php?action=edit&partid=$b\">$p</A></TD>\n";
    echo "        <TD>".$lockedby[$badgeid_keys[$i]]."</TD>\n";
    echo "        </TR>\n";
  }
  echo "    </TABLE>\n";
 } else {

  // Build the matrix, with approrpriate headers.
  // Not doing "good" state for the time being.
  //$possible_states=array('noraw','noedited','nogood','rawvedited','editedvgood','allmatch');
  $possible_states=array('noraw','noedited','rawvedited','allmatch');
  $possible_statename['noraw']="Missing raw bio";
  $possible_statename['noedited']="Missing edited bio";
  $possible_statename['nogood']="Missing good bio";
  $possible_statename['rawvedited']="Raw bio doesn't match edited bio";
  $possible_statename['editedvgood']="Edited bio does't match good bio";
  $possible_statename['allmatch']="All bios match";

  for ($i=0; $i<count($count_badgeid); $i++ ) {
    $k=$badgeid_keys[$i];
    $all_bios.=",$k";
    for ($j=0; $j<count($count_col); $j++) {
      $l=$col_keys[$j];
      if (!isset($check_element[$k][$l]['raw'])) {
	$matrix_count[$l]['noraw']++;
	$matrix_badgeid[$l]['noraw'].=",$k";
      }
      if (!isset($check_element[$k][$l]['edited'])) {
	$matrix_count[$l]['noedited']++;
	$matrix_badgeid[$l]['noedited'].=",$k";
      }
      if (!isset($check_element[$k][$l]['good'])) {
	$matrix_count[$l]['nogood']++;
	$matrix_badgeid[$l]['nogood'].=",$k";
      }
      if ((isset($check_element[$k][$l]['raw'])) and
	  (isset($check_element[$k][$l]['edited'])) and
	  ($check_element[$k][$l]['raw'] != $check_element[$k][$l]['edited'])) {
	$matrix_count[$l]['rawvedited']++;
	$matrix_badgeid[$l]['rawvedited'].=",$k";
      }
      if ((isset($check_element[$k][$l]['edited'])) and
	  (isset($check_element[$k][$l]['good'])) and
	  ($check_element[$k][$l]['edited'] != $check_element[$k][$l]['good'])) {
	$matrix_count[$l]['editedvgood']++;
	$matrix_badgeid[$l]['editedvgood'].=",$k";
      }
      // Changed to reflect the missing "good" fields.
      if ((isset($check_element[$k][$l]['raw'])) and
	  (isset($check_element[$k][$l]['edited'])) and
	  /* (isset($check_element[$k][$l]['good'])) and */
	  ($check_element[$k][$l]['raw'] == $check_element[$k][$l]['edited']) /*and
          ($check_element[$k][$l]['edited'] == $check_element[$k][$l]['good']) */) {
	$matrix_count[$l]['allmatch']++;
	$matrix_badgeid[$l]['allmatch'].=",$k";
      }
    }
  }

  // Table header
  echo "<TABLE class=\"grid\" border=1>\n";
  echo "  <TR>\n";
  echo "    <TH>Count of the States of the bios</TH>\n";
  for ($i=0; $i<count($count_col); $i++) {
    echo "    <TH>".$col_keys[$i]."</TH>\n";
  }
  echo "  </TR>\n";

  $fixed_all=trim($all_bios,",");
  // Table body, with links to the editing groups, reflecting back here
  for ($i=0; $i<count($possible_states); $i++) {
    echo "  <TR>\n    <TH>".$possible_statename[$possible_states[$i]]."</TH>\n";
    $k=$possible_states[$i];
    for ($j=0; $j<count($count_col); $j++) {
      $l=$col_keys[$j];
      if (isset($matrix_count[$l][$k])) {
	$badgelist=$matrix_badgeid[$l][$k];
	$fixedbadgelist=trim($badgelist,",");
	echo "    <TD align=\"center\">";
	echo "<A HREF=StaffManageBios.php?badgeids=".$fixedbadgelist.">".$matrix_count[$l][$k]."</A></TD>\n";
      } else {
	echo "    <TD></TD>\n";
      }
    }
    echo "  </TR>\n";
  }
  echo "</TABLE><P><A HREF=StaffManageBios.php?badgeids=$fixed_all>All</A></P>";
 }

correct_footer();     
exit();
?>