<?php
// $Header$
// Function get_session_interests_from_db($badgeid)
// Returns count; Will render its own errors
// Populates global $session_interest with
// ['sessionid'] ['rank'] ['willmoderate'] ['comments']
// and populates $session_interest_index
//
function get_session_interests_from_db($badgeid) {
    global $session_interests, $session_interest_index, $title, $link;
    $query= <<<EOD
SELECT sessionid, rank, willmoderate, comments FROM ParticipantSessionInterest
    WHERE badgeid='$badgeid' ORDER BY IFNULL(rank,9999), sessionid
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<br />Error querying database.<br />";
        RenderError($title,$message);
        exit();
        }
    $session_interest_count=mysql_num_rows($result);
    for ($i=1; $i<=$session_interest_count; $i++ ) {
        $session_interests[$i]=mysql_fetch_array($result, MYSQL_ASSOC);
        $session_interest_index[$session_interests[$i]['sessionid']]=$i;
        }
    return ($session_interest_count);
    }
    
// Function get_si_session_info_from_db($session_interest_count)
// Will render its own errors
// Reads global $session_interest to get sessionid's to retrieve
// Reads global $session_interest_index
// Populates global $session_interest with
// ['trackname'] ['title'] ['duration'] ['progguiddesc'] ['persppartinfo']
//
function get_si_session_info_from_db($session_interest_count) {
    global $session_interests, $session_interest_index, $title, $link;
	//print_r($session_interest_index);
    if ($session_interest_count==0) return;
    for ($i=1; $i<=$session_interest_count; $i++ ) {
        $sessionidlist.=$session_interests[$i]['sessionid'].", ";
        }
    $sessionidlist=substr($sessionidlist,0,-2); // drop extra trailing ", "
// If session for which participant is interested no longer has status valid for signup, then don't retrieve
    $query= <<<EOD
SELECT
        S.sessionid,
        T.trackname,
        S.title,
        S.duration,
        S.progguiddesc,
        S.persppartinfo
    FROM
        Sessions S JOIN
        Tracks T using (trackid) JOIN
        SessionStatuses SS using (statusid)
    WHERE
        S.sessionid in ($sessionidlist) and
        SS.may_be_scheduled=1
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $num_rows=mysql_num_rows($result);
    for ($i=1; $i<=$num_rows; $i++ ) {
        $this_row=mysql_fetch_array($result, MYSQL_ASSOC);
        $j=$session_interest_index[$this_row['sessionid']];
        $session_interests[$j]['trackname']=$this_row['trackname'];
        $session_interests[$j]['title']=$this_row['title'];
        $session_interests[$j]['duration']=$this_row['duration'];
        $session_interests[$j]['progguiddesc']=$this_row['progguiddesc'];
        $session_interests[$j]['persppartinfo']=$this_row['persppartinfo'];
        }
    //echo "<P>message: $message</P>";
    return (true);
    }
// Function get_session_interests_from_post()
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.  Returns
// the maximum index value.
//
function get_session_interests_from_post() {
    global $session_interests,$session_interest_index;
    $i=1;
    while (isset($_POST["sessionid$i"])) {
        $session_interests[$i]['sessionid']=$_POST["sessionid$i"];
        $session_interest_index[$_POST["sessionid$i"]]=$i;
        $session_interests[$i]['rank']=$_POST["rank$i"];
        $session_interests[$i]['delete']=(isset($_POST["delete$i"]))?true:false;
        $session_interests[$i]['comments']=stripslashes($_POST["comments$i"]);
        $session_interests[$i]['willmoderate']=(isset($_POST["mod$i"]))?true:false;
        $i++;
        }
    $i--;
    //echo "<P>I: $i</P>";
    //print_r($session_interest_index);
    return($i);
    }
// Function update_session_interests_in_db($session_interest_count)
// Reads the data posted by the browser form and populates
// the $partavail global variable with it.  Returns
// the maximum index value.
//
function update_session_interests_in_db($badgeid,$session_interest_count) {
	global $session_interests,$link,$title,$message;
	//print_r($session_interests);
	$deleteSessionIds="";
	$noDeleteCount=0;
	for ($i=1; $i<=$session_interest_count; $i++) {
		if ($session_interests[$i]['delete']) {
				$deleteSessionIds.=$session_interests[$i]['sessionid'].", ";
				}
			else {
				$noDeleteCount++;
				}
		}
	if ($deleteSessionIds) {
		$deleteSessionIds=substr($deleteSessionIds,0,-2); //drop trailing ", "
		$query="DELETE FROM ParticipantSessionInterest WHERE badgeid=\"$badgeid\" and sessionid in ($deleteSessionIds)";
		if (!mysql_query($query,$link)) {
	        $message=$query."<br />Error updating database.  Database not updated.";
	        RenderError($title,$message);
	        exit();
			}
		$deleteCount=mysql_affected_rows($link);
    	$message="$deleteCount record(s) deleted.<br />\n";
        }
	if ($noDeleteCount) {
		$noDeleteCount = 0;
		$query = "REPLACE INTO ParticipantSessionInterest (badgeid, sessionid, rank, willmoderate, comments) values ";
    	for ($i=1;$i<=$session_interest_count;$i++) {
			if ($session_interests[$i]['delete'])
				continue;
			$noDeleteCount++;
			$query.="(\"$badgeid\",{$session_interests[$i]['sessionid']},";
			$rank=$session_interests[$i]['rank'];
			$query.=($rank==""?"null":$rank).",";
			$query.=($session_interests[$i]['willmoderate']?1:0).",";
			$query.="\"".mysql_real_escape_string($session_interests[$i]['comments'],$link)."\"),";
			}
		$query=substr($query,0,-1); // drop trailing ","
        if (!mysql_query($query,$link)) {
            $message=$query."<br />Error updating database.  Database not updated.";
            RenderError($title,$message);
            exit();
            }
		$message.="$noDeleteCount sessions recorded.<br />\n";
        }
	return (true);
}
?>
