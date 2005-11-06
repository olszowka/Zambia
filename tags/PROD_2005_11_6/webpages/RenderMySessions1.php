<?php
    function RenderMySessions1($result) {
?>
  <FORM name="resform" method=POST action="SubmitMySessions1.php">
<p> You will find below the results of your search.  We have included the session id, track, title, duration, a check box for you to indicate interest, followed by the description as it will appear in the pocket program and some additional information for you as a prospective panelist.  <hr>
    <TABLE>
        <COL><COL><COL><COL><COL><COL>
<!--        <TR>
            <TH rowspan=3 class="border2122">Sess.<BR>ID</TH>
            <TH class="border2111">Track</TH>
            <TH class="border2111">Title</TH>
            <TH class="border2111">Duration</TH>
            <TH class="border2211">I'm Interested</TH>
            </TR>
        <TR><TH colspan=4 class="border1211">Pocket Program Notes</TH></TR>
        <TR><TH colspan=4 class="border1221">Prospective Participant Info</TH></TR>
        <TR><TD>&nbsp;</TD></TR>
-->
<?php
    $i=0;
    while (list($sessionid,$trackname,$title,$duration,$pocketprogtext, $persppartinfo, $rbadgeid)= mysql_fetch_array($result, MYSQL_NUM)) {
        echo "        <TR>\n";
        echo "            <TD rowspan=4 class=\"border0000\" id=\"sessidtcell\"><b>".$sessionid."&nbsp;&nbsp;";
             echo "<INPUT type=hidden name=\"sessionid".$i."\" value=\"".$sessionid."\"></TD>\n";

        echo "            <TD class=\"border0000\"><b>".$trackname."</TD>\n";

        echo "            <TD class=\"border0000\"><b>".htmlspecialchars($title,ENT_NOQUOTES)."</TD>\n";

        echo "            <TD class=\"border0000\"><b>".$duration." hr</TD>\n";
        echo "        </TR>\n";
        echo "        <TR>\n";
        echo "            <TD colspan=2 class=\"addbox\">Add this panel to my list:<INPUT type=\"checkbox\" name=\"int".$i."\" ";
             echo ((strlen($rbadgeid)>1)?"checked":"")."><INPUT type=hidden name=\"checked".$i."\" value=\"";
             echo ((strlen($rbadgeid)>1)?"1":"0")."\"></TD>\n";
        echo "        </TR>\n";
        echo "        <TR><TD colspan=3 class=\"border0010\">".htmlspecialchars($pocketprogtext,ENT_NOQUOTES)."</TD></TR>\n";
        echo "        <TR><TD colspan=4 class=\"border0000\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</TD></TR>\n";
        echo "        <TR><TD colspan=6 class=\"border0020\">&nbsp;</TD></TR>\n";
        echo "        <TR><TD colspan=6 class=\"border0000\" id=\"smallspacer\">&nbsp;</TD></TR>\n";
        $i++;
        }
    echo "        <INPUT type=\"hidden\" name=\"maxrow\" value=\"".($i-1)."\">\n";
?>
        </TABLE>
        <DIV class="submit">
            <DIV id="submit"><BUTTON type="submit" name="save" >Save</BUTTON></DIV>
            </DIV>
      </FORM>
<?php
    }
?>
