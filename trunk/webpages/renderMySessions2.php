<?php
function renderMySessions2 ($title, $error, $message, $badgeid) {
    global $link;
    $query= <<<EOD
    SELECT S.sessionid, T.trackname, S.title, S.duration, PSI.rank, PSI.willmoderate,
    PSI.comments, S.pocketprogtext, S.persppartinfo from Sessions AS S, Tracks AS T, 
    (Select sessionid, rank, willmoderate, comments from ParticipantSessionInterest
    where badgeid=
EOD;
        $query.="\"".$badgeid."\") ";
        $query.= "AS PSI where S.sessionid = PSI.sessionid and ";
        $query.= "S.trackid = T.trackid order by PSI.rank";
        if (!$result=mysql_query($query,$link)) {
            $message.=$query."<BR>Error querying database.<BR>";
            RenderError($title,$message);
            exit();
            }
        participant_header($title);
        if ($error) {
                echo "<P class=\"errmsg\">Database not updated.<BR>".$message."</P>";
                }
            elseif ($message!="") {
                echo "<P class=\"regmsg\">".$message."</P>";
                }
?>
    <FORM name="addform" method=POST action="my_sessions2.php">
      <table>
        <tr>
          <td>Add Session ID to my List</td>
          <td><Input type="text" name="sessionid" size=10></td>
          <td><BUTTON type="submit" name="add" id="add">Add</BUTTON></td>
        </tr>
      <table>
    </FORM>
    <HR>
<p> Please use the following scale when ranking your interest in the panels you have chosen:  <p>1 - Oooh! Oh! Pick Me!, 2- ..., 3 - I'd like to if I can, 4 - ..., 5 - Meh, I can take it or leave it. </p>

    <H3>List of Sessions in Which I'm Interested in Participating</H3>
    <FORM name="sessionform" method=POST action="SubmitMySessions2.php">
        <TABLE>
           <COL><COL><COL><COL><COL>
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
