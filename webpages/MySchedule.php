<?php
    $title="My Schedule";
    require ('db_functions.php'); //define database functions
    require_once('ParticipantFooter.php');
    require_once('renderMySessions2.php');
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
    // set $badgeid from session
    $query= <<<EOD
    SELECT POS.sessionid, title, roomname, DATE_FORMAT(ADDTIME('2006-01-13 00:00:00', starttime),'%a %l:%i %p')
    as 'StartTime', left(duration,5) as 'Duration', persppartinfo, notesforpart FROM
    ParticipantOnSession POS, Sessions S, Rooms R, Schedule SCH where badgeid="$badgeid" and
    POS.sessionid = S.sessionid and R.roomid = SCH.roomid and S.sessionid = SCH.sessionid
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $schdrows=mysql_num_rows($result);
    for ($i=0; $i<$schdrows; $i++) {
        list($schdarray[$i]["sessionid"],$schdarray[$i]["title"],$schdarray[$i]["roomname"],\
            $schdarray[$i]["starttime"],$schdarray[$i]["duration"],$schdarray[$i]["persppartinfo"],\
            $schdarray[$i]["notesforpart"])=mysql_fetch_array($result, MYSQL_NUM);
        }
    $query= <<<EOD
    SELECT Sess.sessionid, CD.badgename, POS.moderator, PSI.comments FROM 
    ParticipantOnSession POS, CongoDump CD, ParticipantSessionInterest PSI,
    (SELECT sessionid FROM ParticipantOnSession WHERE badgeid="$badgeid") AS Sess
    WHERE Sess.sessionid = POS.sessionid and Sess.sessionid = PSI.sessionid and POS.badgeid = PSI.badgeid
    and POS.badgeid = CD.badgeid order by sessionid, moderator desc
EOD;
    if (!$result=mysql_query($query,$link)) {
        $message.=$query."<BR>Error querying database.<BR>";
        RenderError($title,$message);
        exit();
        }
    $partrows=mysql_num_rows($result);
    for ($i=0; $i<$partrows; $i++) {
        list($partarray[$i]["sessionid"],$schdarray[$i]["badgename"],$schdarray[$i]["moderator"],\
            $schdarray[$i]["comments"])=mysql_fetch_array($result, MYSQL_NUM);
        }
    participant_header($title);
?>
    <TABLE>
        <COL><COL><COL><COL><COL><COL>
<!--            <TR>
                <TH rowspan=2 class="border2122">Session<BR>ID</TH>
                <TH class="border2111">Title</TH>
                <TH class="border2111">Rank<BR>Preference</TH>
                <TH rowspan=2 class="border2221">Delete<BR>From<BR>List</TH>
                </TR>
            <TR>    
                <TH class="border1121">Notes to Program Committee and Other Participants</TH>
                <TH class="border1121">Would Moderate</TH>
                 </TR>
-->
<?php
    $i=0;
    while (list($sessionid,$trackname,$title,$duration,$rank,$willmoderate,$comments,
        $pocketprogtext, $persppartinfo)= mysql_fetch_array($result, MYSQL_NUM)) {
        echo "        <TR>\n";
        echo "            <TD rowspan=5 class=\"border0000 hilit\" id=\"sessidtcell\">".$sessionid."<INPUT type=\"hidden\" name=\"sessionid".$i."\" value=\"".$sessionid."\"></TD>\n";
        echo "            <TD class=\"border0000 hilit vatop\">".$trackname."</TD>\n";
        echo "            <TD colspan=2 class=\"border0000 hilit vatop\">".htmlspecialchars($title,ENT_NOQUOTES)."</TD>\n";
        echo "            <TD class=\"border0000 hilit vatop\">Duration: ".$duration."</TD>\n";
        echo "        </TR>\n";
        echo "        <TR>\n";
        echo "            <TD class=\"border0000 usrinp\">Rank: <INPUT type=\"text\" size=3 name=\"rank".$i."\" value=\"".$rank."\"></TD>\n";
        echo "            <TD class=\"border0000 usrinp\">I'd like to moderate this panel:<INPUT type=\"checkbox\" value=1 name=\"mod".$i."\" ".(($willmoderate)?"checked":"")."></TD>\n";
        echo "            <TD colspan=2 class=\"border0000 usrinp\">Remove this panel from my list:<INPUT type=\"checkbox\" value=1 name=\"delete".$i."\"></TD>\n";
        echo "        </TR>\n";
        echo "        <TR>\n";
        echo "            <TD  class=\"border0000 usrinp\" colspan=4>My notes regarding this panel for Programming and other panel participants: <TEXTAREA height=5em cols=80 name=\"comments".$i."\" id=\"intCmnt\">".htmlspecialchars($comments,ENT_COMPAT)."</TEXTAREA></TD>\n";
        echo "        </TR>\n";
        echo "        <TR>\n";
        echo "            <TD colspan=4 class=\"border0010\">".htmlspecialchars($pocketprogtext,ENT_NOQUOTES)."</TD>\n";
        echo "        </TR>\n";
        echo "        <TR>\n";
        echo "            <TD colspan=4 class=\"border0000\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</TD>\n";
        echo "        </TR>\n";
        echo "        <TR>\n";
        echo "            <TD colspan=6 class=\"border0020\" id=\"smallspacer\">&nbsp;</TD>\n";
        echo "        </TR>\n";
        echo "        <TR>\n";
        echo "            <TD colspan=6 class=\"border0000\" id=\"smallspacer\">&nbsp;</TD>\n";
        echo "        </TR>\n";
        $i++;
        }
?>

        </TABLE>    
        <DIV class="submit">
            <DIV id="submit"><BUTTON class="SubmitButton" type="submit" name="submit" >Save</BUTTON></DIV>
            </DIV>
      </FORM>
<?php } ?>
