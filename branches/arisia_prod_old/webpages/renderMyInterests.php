<?php
function renderMyInterests ($title, $error, $message) {
    global $link, $yespanels, $nopanels, $yespeople, $nopeople;
    global $otherroles, $newrow, $rolerows, $rolearray;
    participant_header($title);
    if ($error) {
            echo "<P class=\"alert alert-error\">Database not updated.<BR>".$message."</P>";
            }
        elseif ($message!="") {
            echo "<P class=\"alert alert-success\">".$message."</P>";
            }
    if (!may_I('my_gen_int_write')) {
        echo "<P>We're sorry, but we are unable to accept your suggestions at this time.\n";
        }
    echo "<FORM class=\"form-horizontal\" name=\"addform\" method=POST action=\"SubmitMyInterests.php\">\n";
    echo "<INPUT type=\"hidden\" name=\"newrow\" value=\"".($newrow?1:0)."\">\n";
    echo "<INPUT type=\"hidden\" name=\"rolerows\" value=\"".$rolerows."\">\n";
    echo "<DIV class=\"row-fluid\">\n";
    echo "  <DIV class=\"span6\">\n";
    echo "    <LABEL for=\"yespanels\"><p>Workshops or presentations I'd like to run: </p></LABEL>\n";
    echo "    <TEXTAREA class=\"span12\" name=\"yespanels\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($yespanels,ENT_COMPAT)."</TEXTAREA>\n";

    echo "  </DIV>\n"; 
    echo "  <DIV class=\"span6\">\n";
    echo "    <LABEL for=\"nopanels\"><p>Panel types I am not interested in participating in:</p></LABEL>\n";
    echo "    <TEXTAREA class=\"span12\" name=\"nopanels\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($nopanels,ENT_COMPAT)."</TEXTAREA>\n";
    echo "</DIV>\n";
    echo "<DIV class=\"row-fluid\">\n";
    echo "  <DIV class=\"span6\">\n";
    echo "    <LABEL for=\"yespeople\"><p>People with whom I'd like to be on a session: (Leave blank for none)</p></LABEL>\n";
    echo "    <TEXTAREA class=\"span12\" name=\"yespeople\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($yespeople,ENT_COMPAT)."</TEXTAREA>\n";
    echo "  </DIV>\n";
    echo "  <DIV class=\"span6\">\n";
    echo "    <LABEL for=\"nopeople\"><p>People with whom I'd rather not be on a session: (Leave blank for none)</p></LABEL>\n";
    echo "    <TEXTAREA class=\"span12\" name=\"nopeople\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($nopeople,ENT_COMPAT)."</TEXTAREA>\n";
    echo "  </DIV>\n";
    echo "</DIV>\n";
    echo "<P>Roles I'm willing to take on:</P>\n";
    echo "<DIV class=\"row-fluid\">\n";
    echo "    <DIV class=\"control-group span12\">\n";
    for ($i=1; $i<$rolerows; $i+=2) {
        echo "                <LABEL class=\"checkbox inline long\" for=\"willdorole".$i."\">".$rolearray[$i]["rolename"]."\n";
        echo "                <INPUT type=checkbox name=\"willdorole".$i."\" id=\"willdorole".$i."\"";
        if (isset($rolearray[$i]["badgeid"])) {
            echo "checked";
            }
        if (!may_I('my_gen_int_write')) {
            echo " disabled";
            }
        echo "></LABEL>\n";
        echo "                <INPUT type=hidden name=\"diddorole".$i."\" value=\"";
        echo ((isset($rolearray[$i]["badgeid"]))?1:0)."\">\n";
        echo "                <INPUT type=hidden name=\"roleid".$i."\" value=\"".$rolearray[$i]["roleid"]."\">\n";
        echo "                <INPUT type=hidden name=\"rolename".$i."\" value=\"".$rolearray[$i]["rolename"]."\">\n";
        if (($i+1)>=$rolerows) {
            break;
            }
        echo "                <LABEL class=\"checkbox inline long\" for=\"willdorole".($i+1)."\">".$rolearray[$i+1]["rolename"]."\n";
        echo "                <INPUT class=\"checkbox\" type=checkbox name=\"willdorole".($i+1)."\" ";
        if (isset($rolearray[$i+1]["badgeid"])) {
            echo "checked";
            }
        if (!may_I('my_gen_int_write')) {
            echo " disabled";
            }
        echo "></LABEL>\n";
        echo "                <INPUT type=hidden name=\"diddorole".($i+1)."\" value=\"";
        echo ((isset($rolearray[$i+1]["badgeid"]))?1:0)."\">\n";
        echo "                <INPUT type=hidden name=\"roleid".($i+1)."\" value=\"".$rolearray[$i+1]["roleid"]."\">\n";
        echo "                <INPUT type=hidden name=\"rolename".($i+1)."\" value=\"".$rolearray[$i+1]["rolename"]."\">\n";
    }

    echo "                <LABEL class=\"checkbox inline long\" for=\"willdorole0\">".$rolearray[0]["rolename"]."  (Please describe below)";
    echo "                <INPUT class=\"checkbox\" type=checkbox name=\"willdorole0\" ";
    if (isset($rolearray[0]["badgeid"])) {
        echo "checked";
        }
    if (!may_I('my_gen_int_write')) {
        echo " disabled";
        }
    echo "> </LABEL>\n";
    echo "                <INPUT type=hidden name=\"roleid0\" value=\"".$rolearray[0]["roleid"]."\">\n";
    echo "                <INPUT type=hidden name=\"rolename0\" value=\"".$rolearray[0]["rolename"]."\">\n";
    echo "                <INPUT type=hidden name=\"diddorole0\" value=\"";
    echo ((isset($rolearray[0]["badgeid"]))?1:0)."\">\n";
    echo "<P>Description for \"Other\":</P>\n";
    echo "<TEXTAREA class=\"span12\" name=\"otherroles\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
        }
    echo ">".htmlspecialchars($otherroles,ENT_COMPAT)."</TEXTAREA>\n";
    echo "</DIV>\n";
    echo "<DIV class=\"submit\">\n";
    echo "    <DIV id=\"submit\">";
    if (may_I('my_gen_int_write')) {
        echo "<BUTTON class=\"btn btn-primary\" type=\"submit\" name=\"submit\" >Save</BUTTON>";
        }
    echo "</DIV>\n";
    echo "    </DIV>\n";
    echo "</FORM>\n";
    } ?>
