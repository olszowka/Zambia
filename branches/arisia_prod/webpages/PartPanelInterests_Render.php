<?php
function render_session_interests($badgid,$session_interest_count,$message,$message_error) {
    global $session_interests, $title;
    participant_header($title);
    if ($message_error) {
        echo "<P class=\"alert alert-error\">Database not updated.<BR>".$message_error."</P>";
        }
    if ($message) {
        echo "<P class=\"alert alert-success\">".$message."</P>";
        }
    // "Add" Section
    echo "<FORM class=\"form-inline\" name=\"addform\" method=POST action=\"PartPanelInterests_POST.php\">\n";
    echo "  <div class=\"row-fluid\">";
    echo "    <div class=\"controls padded\">\n";
    echo "        <div class=\"control-group\">";
    echo "            <label class=\"control-label\">Add Session ID to my List: ";
    echo "            <Input type=\"text\" class=\"span4\" name=\"addsessionid\" size=\"10\"></label>\n";
    echo "            <BUTTON class=\"btn btn-primary\" type=\"submit\" name=\"add\" id=\"add\">Add</BUTTON>\n";
    echo "        </div>\n";
    echo "      </div>\n";
    echo "  </div>\n";
    echo "</FORM>\n";
    echo "<HR />\n";
    // "Update Ranks" Section
    echo "<FORM class=\"form-inline\" name=\"sessionform\" method=POST action=\"PartPanelInterests_POST2.php\">\n";
    echo "<DIV class=\"submit\" id=\"submit\"><BUTTON class=\"btn btn-primary pull-right\" type=\"submit\" name=\"submitranks\">Save</BUTTON></DIV>\n";
    echo "<P>Please use the following scale when ranking your interest in the sessions you have chosen:  </P>\n";
    echo "<strong>1 &mdash;<em> Oooh! Oh! Pick Me!</em>&nbsp;&nbsp;&nbsp;2-3 &mdash; <em>I'd like to if I can</em>&nbsp;&nbsp;&nbsp;4-5 &ndash; <em>I am qualified but this is not one of my primary interests</em></strong>\n";
    echo "<P>You are limited to 4 sessions each of preferences 1-4.  There is no limit to the number of sessions for which you can express preference 5.</P>\n";
    echo "<H4>List of Sessions in Which I'm Interested in Participating:</H4>\n";
    echo "<div class=\"row-fluid\">\n";
	$j=1; //use $j so that skipped sessions don't skip numbering
    for ($i=1; $i<=$session_interest_count; $i++) {
        if (!$session_interests[$i]['title']) continue;
        echo "  <div class=\"control-group\">\n";
        echo "    <div class=\"controls\">\n";
        echo "        <span class=\"span1\">{$session_interests[$i]['sessionid']}";
        echo "            <INPUT type=\"hidden\" name=\"sessionid$j\" value=\"{$session_interests[$i]['sessionid']}\"></span>\n";
        echo "        <span class=\"span2\">{$session_interests[$i]['trackname']}</span>\n";
        echo "        <span class=\"span5\">".htmlspecialchars($session_interests[$i]['title'],ENT_NOQUOTES)."</span>\n";
        echo "        <span class=\"span4\">Duration: {$session_interests[$i]['duration']}</span>\n";
        echo "    </div>\n";
        echo "    <div class=\"controls controls-row\">\n";
        echo "        <span class=\"span1\"></span>\n";
        echo "        <label class=\"span2 control-label\">Rank: <INPUT class=\"span5\" type=\"text\" size=3 name=\"rank$j\" value=\"{$session_interests[$i]['rank']}\"></label>\n";
        echo "        <label class=\"span5 checkbox inline\">I'd like to moderate this session&nbsp;<INPUT class=\"checkbox\" type=\"checkbox\" value=1 name=\"mod$j\" ";
        echo "            ".(($session_interests[$i]['willmoderate'])?"checked":"")."></label>\n";
        echo "        <label class=\"span4 checkbox \">Remove this session from my list<INPUT class=\"checkbox\" type=\"checkbox\" value=1 name=\"delete$j\"></label>\n";
        echo "    </div>\n";
        echo "    <div class=\"controls controls-row\">\n";
        echo "        <span class=\"span1\"></span>\n";
        echo "        <label class=\"span11 control-label\">Use this space to convince us why you would be fabulous on this session:";
        echo "            <TEXTAREA class=\"span12\" height=5em cols=80 name=\"comments$j\" id=\"intCmnt\">". htmlspecialchars( $session_interests[$i]['comments'],ENT_COMPAT)."</TEXTAREA></label>\n";
        echo "    </div>\n";
        echo "    <div class=\"controls controls-row padded\">\n";
        echo "        <span class=\"span1\"></span>\n";
        echo "        <span class=\"span11\">".htmlspecialchars($session_interests[$i]['progguiddesc'],ENT_NOQUOTES)."</span>\n";
        echo "    </div>\n";
        if ($session_interests[$i]['persppartinfo']) {
        echo "    <div class=\"controls controls-row\">\n";
        echo "        <span class=\"span1\"></span>\n";
        echo "        <span class=\"span11 alert\" style=\"padding: 0\">".htmlspecialchars($session_interests[$i]['persppartinfo'],ENT_NOQUOTES)."</span>\n";
        echo "    </div>\n";
        }
        echo "        <hr />\n";
        echo "  </div>\n";
		$j++;
        }
    echo "</div>\n";
    echo "<DIV class=\"submit\" id=\"submit2\"><BUTTON class=\"btn btn-primary pull-right\" type=\"submit\" name=\"submitranks\">Save</BUTTON></DIV><br>\n";
    echo "</FORM>\n";
    participant_footer();
    }
?>
