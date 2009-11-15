<?php
function check_room_sched_conflicts($deleteScheduleIds,$addToScheduleArray) {
	//
	// $addToScheduleArray is an array of $sessionid => $startmin
	//     sessions to add to schedule with starttime measured in minutes from start of con
	// $deleteScheduleIds is an array of $scheduleid => 1
	// Perform following checks on the participants in the new schedule entries (taking into account deleted entries):
	//    1. Are any participants double booked?
	//    2. Are any participants scheduled outside available times?
	//    3. Are any participants scheduled for more sessions than limits (daily and total)?
	//
	// Process
	//
	// Get the data of entries added -- Get the list of participants affected
	//      If there are no additions, then there are only deletions and these can't cause conflicts.
	// Get their existing schedule
	// Remove the deleted items from the existing schedule data to be compare
	// Check 1
	// Retrieve availability info
	// Check 2
	// Retrieve session limit info
	// Check 3
	global $title, $link, $message;
	if (!$addToScheduleArray) return (true); // If there are no additions, then
	    // there are only deletions and these can't cause conflicts.
	$sessionidlist="";
	foreach ($addToScheduleArray as $sessionid => $starttime) {
		$sessionidlist.=$sessionid.",";
		}
	$sessionidlist=substr($sessionidlist,0,-1); // remove trailing comma
	$query="SELECT sessionid, title from Sessions where sessionid in ($sessionidlist)";
	if (!$result=mysql_query($query,$link)) {
		$message=$query."<BR>\nError querying database.<BR>\n";
        RenderError($title,$message);
        exit();
		}
	while (list($sessionid,$title)=mysql_fetch_array($result,MYSQL_ASSOC)) { 
		$addToSchedTitles[$sessionid]=$title;
		}	
	$query= <<<EOD
SELECT
        S.sessionid, hour(S.duration)*60+minute(S.duration) as durationmin, POS.badgeid
    FROM
        Sessions S JOIN
        ParticipantOnSession POS USING (sessionid)
    WHERE
        S.sessionid in ($sessionidlist)
EOD;
	if (!$result=mysql_query($query,$link)) {
		$message=$query."<BR>\nError querying database.<BR>\n";
        RenderError($title,$message);
        exit();
		}
	while (list($sessionid, $durationmin, $badgeid)=mysql_fetch_array($result,MYSQL_NUM)) {
		$addToScheduleArray2[]=array('sessionid'=>$sessionid, 'startmin'=>$addToScheduleArray[$sessionid],
			'durationmin'=>$durationmin, 'badgeid'=>$badgeid);
		$addToScheduleParticipants[$badgeid]=1;
		}
	if (!$addToScheduleParticipants) return(true); // if none of the sessions added to the schedule
	// had any participants, then there can be no participant conflicts.
	$badgeidlist="";
	foreach ($addToScheduleParticipants as $badgeid => $x) {
		$badgeidlist.="'$badgeid',";
		}
	$badgeidlist=substr($badgeidlist,0,-1); // remove trailing comma
	$query= <<<EOD
SELECT
        badgeid, pubsname
    FROM
        Participants
    WHERE
        badgeid in ($badgeidlist)
EOD;
	if (!$result=mysql_query($query,$link)) {
		$message=$query."<BR>\nError querying database.<BR>\n";
	    RenderError($title,$message);
	    exit();
		}
	while ($x=mysql_fetch_array($result,MYSQL_ASSOC)) {
		$addToScheduleParticipants[$x['badgeid']]=$x['pubsname'];
		}
	// starttime and duration in minutes from start of con -- simpler time comparison
	$query= <<<EOD
SELECT
        SCH.scheduleid, SCH.sessionid, hour(SCH.starttime)*60+minute(SCH.starttime) as startmin,
        hour(S.duration)*60+minute(S.duration) as durationmin, POS.badgeid, S.title, R.roomname
    FROM
        Schedule SCH JOIN
        Sessions S USING (sessionid) JOIN
        Rooms R USING (roomid) JOIN
        ParticipantOnSession POS USING (sessionid)
    WHERE
        POS.badgeid in ($badgeidlist)
EOD;
	if (!$result=mysql_query($query,$link)) {
		$message=$query."<BR>\nError querying database.<BR>\n";
	    RenderError($title,$message);
	    exit();
		}
	while ($x=mysql_fetch_array($result,MYSQL_ASSOC)) {
		if ($deleteScheduleIds[$x['scheduleid']]==1) continue; //skip the scheduleids that will be deleted anyway
		$refScheduleArray[]=$x;
		}
	if (!$refScheduleArray) return (true); //If net of deletes there are no sessions for relevant participants then 
	// there can be no conflicts
	$message="";
	foreach ($addToScheduleArray2 as $addSession) {
		$conflictThisAddition=false;
		foreach ($refScheduleArray as $refSession) {
			if ($addSession['badgeid']!=$refSession['badgeid']) continue;
			if ($addSession['startmin']>=$refSession['startmin']+$refSession['durationmin'] or
				$refSession['startmin']>=$addSession['startmin']+$addSession['durationmin']) continue;
			if (!$conflictThisAddition) { // Need header for this session
				$message.="<P>Session {$addSession['sessionid']}: {$addToSchedTitles[$addSession['sessionid']]}</P>\n<UL>"; 
				}
			$conflictThisAddition=true;
			$message.="<LI>".htmlspecialchars($addToScheduleParticipants[$addSession['badgeid']],ENT_NOQUOTES)." ({$addSession['badgeid']}) ";
			$message.="has conflict with ".htmlspecialchars($refSession['title'],ENT_NOQUOTES)." ({$refSession['sessionid']}) in ";
			$message.="{$refSession['roomname']}.</LI>\n";
			// conflict!

			}
		if ($conflictThisAddition) { 
			$message.="</UL>";
			}
		} 	
	return (($message)?false:true); // empty message == no conflicts.
	}
			
function SubmitMaintainRoom($ignore_conflicts) {
//
//  This is hardcoded to follow the workflow of editme -> vetted -> scheduled -> assigned
//  We need to find a way to make it more configurable and flexible
//
    global $link,$message;
//    print_r($_POST);
    $numrows=$_POST["numrows"];
    $selroomid=$_POST["selroom"];
    for ($i=1; $i<=$numrows; $i++) { //***** need to update render as well to start at 1********
        if($_POST["del$i"]!=1) continue;
        $deleteScheduleIds[$_POST["row$i"]]=1;
        }
    $incompleteRows=0;
	$completeRows=0;
	for ($i=1;$i<=newroomslots;$i++) {
        if ($_POST["sess$i"]=="unset") continue;
        if (CON_NUM_DAYS==1) {
                $day=1;
                }
            else {
                $day=$_POST["day$i"];
                }
        if ($day==0 or $_POST["hour$i"]=="unset" or  $_POST["min$i"]=="unset") {
            $incompleteRows++;
            continue;
            }
		// starttimes in minutes from start of con
        $addToScheduleArray[$_POST["sess$i"]]=($day-1)*1440+$_POST["ampm$i"]*720+$_POST["hour$i"]*60+$_POST["min$i"];
		$completeRows++;
		}
	if (!$ignore_conflicts) {
		if (!check_room_sched_conflicts($deleteScheduleIds,$addToScheduleArray)) {
			echo "<P>Database not updated.  There were conflicts</P>\n";
			echo $message;
			return false;
			}
		}
	if ($deleteScheduleIds!="") {
		$delSchedIdList="";
		foreach ($deleteScheduleIds as $delid=>$foo) { 
			$delSchedIdList.="$delid,";
			}
		$delSchedIdList=substr($delSchedIdList,0,-1); // remove trailing comma
//  Set status of deleted entries back to vetted.
        $vs=get_idlist_from_db('SessionStatuses','statusid','statusname',"'vetted'");
        $query="UPDATE Sessions AS S, Schedule as SC SET S.statusid=$vs WHERE S.sessionid=SC.sessionid AND ";
        $query.="SC.scheduleid IN ($delSchedIdList)";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        $query="DELETE FROM Schedule WHERE scheduleid in ($delSchedIdList)";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        $rows=mysql_affected_rows($link);
        echo "<P class=\"regmsg\">$rows session".($rows>1?"s":"")." removed from schedule.\n";
        }
    $rows=0;
    $warn=0;
    for ($i=1;$i<=newroomslots;$i++) {
        if ($_POST["sess$i"]=="unset") {
            continue;
            }
        if (CON_NUM_DAYS==1) {
                $day=1;
                }
            else {
                $day=$_POST["day$i"];
                }
        if ($day==0 or $_POST["hour$i"]=="unset" or  $_POST["min$i"]=="unset") {
            $warn++;
            continue;
            }
        $time=(($day-1)*24+$_POST["ampm$i"]*12+$_POST["hour$i"]).":".$_POST["min$i"];
        $sessionid=$_POST["sess$i"];
        $query="INSERT INTO Schedule SET sessionid=$sessionid, roomid=$selroomid, starttime=\"$time\"";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
// Set status of scheduled entries to Scheduled.
        $vs=get_idlist_from_db('SessionStatuses','statusid','statusname',"'scheduled'");
        $query="UPDATE Sessions SET statusid=$vs WHERE sessionid=$sessionid";
        if (!mysql_query($query,$link)) {
            $message=$query."<BR>Error updating database.<BR>";
            echo "<P class=\"errmsg\">".$message."\n";
            staff_footer();
            exit();
            }
        $rows++;    
        }
    if ($rows) {
        echo "<P class=\"regmsg\">$rows new schedule entr".($rows>1?"ies":"y")." written to database.\n";
        }
    if ($warn) {
        echo "<P class=\"errmsg\">$warn row".($warn>1?"s":"")." not entered due to incomplete data.\n";
        }
    return (true);    
    }
?>
