<?php
// $Header$
function render_session_interests($badgid,$session_interest_count,$message,$message_error, $pageIsDirty) {
    global $session_interests, $title;
    participant_header($title);
    if ($message_error) {
        echo "<p class=\"alert alert-error\">Database not updated.<br />".$message_error."</p>";
        }
    if ($message) {
        echo "<p class=\"alert alert-success\">".$message."</p>";
        }
	if ($pageIsDirty) {
		echo "<input type=\"hidden\" id=\"pageIsDirty\" value=\"true\" />\n";
		}
    // "Add" Section
    echo "<form class=\"form-inline\" id=\"addFRM\" name=\"addform\" method=\"POST\" action=\"PartPanelInterests_POST.php\">\n";
    echo "  <div class=\"row-fluid\">";
    echo "    <div class=\"controls padded\">\n";
    echo "        <div class=\"control-group\">\n";
    echo "            <label class=\"control-label\">Add Session ID to my List: \n";
    echo "                <input type=\"text\" class=\"span4\" name=\"addsessionid\" size=\"10\" />\n";
    echo "            </label>\n";
    echo "            <input type=\"hidden\" name=\"add\" />\n";
    echo "            <button class=\"btn btn-primary\" type=\"button\" onclick=\"panelInterests.onClickAdd();\">Add</button>\n";
    echo "        </div>\n";
    echo "      </div>\n";
    echo "  </div>\n";
    echo "</form>\n";
    echo "<hr />\n";
    // "Update Ranks" Section
    echo "<form id=\"sessionFRM\" class=\"form-inline\" name=\"sessionform\" method=\"POST\" action=\"PartPanelInterests_POST2.php\">\n";
	echo "<input type=\"hidden\" name=\"submitranks\" value=\"1\" />\n";
    echo "<div class=\"submit\"><button class=\"btn btn-primary pull-right\" type=\"submit\">Save</button></div>\n";
    echo "<p>Please use the following scale when ranking your interest in the sessions you have chosen:  </p>\n";
    echo "<strong>1 &mdash;<em> Oooh! Oh! Pick Me!</em>&nbsp;&nbsp;&nbsp;2-3 &mdash; <em>I'd like to if I can</em>&nbsp;&nbsp;&nbsp;4-5 &ndash; <em>I am qualified but this is not one of my primary interests</em></strong>\n";
    echo "<p>You are limited to 4 sessions each of preferences 1-4.  There is no limit to the number of sessions for which you can express preference 5.</p>\n";
    echo "<h4>List of Sessions in Which I'm Interested in Participating:</h4>\n";
    echo "<div class=\"row-fluid\">\n";
	$j=1; //use $j so that skipped sessions don't skip numbering
    for ($i=1; $i<=$session_interest_count; $i++) {
        if (!$session_interests[$i]['title']) continue;
        echo "  <div class=\"control-group\">\n";
        echo "    <div class=\"controls\">\n";
        echo "        <span class=\"span1\">{$session_interests[$i]['sessionid']}";
        echo "            <input type=\"hidden\" name=\"sessionid$j\" value=\"{$session_interests[$i]['sessionid']}\" /></span>\n";
        echo "        <span class=\"span2\">{$session_interests[$i]['trackname']}</span>\n";
        echo "        <span class=\"span5\">".htmlspecialchars($session_interests[$i]['title'],ENT_NOQUOTES)."</span>\n";
        echo "        <span class=\"span4\">Duration: {$session_interests[$i]['duration']}</span>\n";
        echo "    </div>\n";
        echo "    <div class=\"controls controls-row\">\n";
        echo "        <span class=\"span1\"></span>\n";
        echo "        <label class=\"control-label span2\">Rank: \n";
        echo "            <input type=\"text\" id=\"rankINP_$j\" size=\"2\" class=\"rankINP\" name=\"rank$j\" value=\"{$session_interests[$i]['rank']}\" />\n";
        echo "        </label>\n";
        echo "        <span class=\"span5\">\n";
        echo "            <input type=\"checkbox\" id=\"modCHK_$j\" class=\"checkbox\" value=\"1\" name=\"mod$j\" ".(($session_interests[$i]['willmoderate'])?"checked":"")." />\n";
        echo "            <label class=\"inline\">I'd like to moderate this session </label>\n";
        echo "        </span>\n";
        echo "        <span class=\"span4\">\n";
        echo "            <input type=\"checkbox\" id=\"deleteCHK_$j\" class=\"checkbox\" value=\"1\" name=\"delete$j\" />\n";		
        echo "            <label class=\"inline \">Remove this session from my list </label>\n";
        echo "        </span>\n";
        echo "    </div>\n";
        echo "    <div class=\"controls controls-row\">\n";
        echo "        <span class=\"span1\"></span>\n";
        echo "        <label class=\"span11 control-label\">Use this space to convince us why you would be fabulous on this session: </label>";
        echo "    </div>\n";
        echo "    <div class=\"controls controls-row padded\">\n";
        echo "        <textarea id=\"commentsTXTA_$j\" class=\"span12 sessionWhyMe\" cols=\"80\" name=\"comments$j\" >". htmlspecialchars( $session_interests[$i]['comments'],ENT_COMPAT)."</textarea>\n";
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
    echo "<div class=\"submit\"><button class=\"btn btn-primary pull-right\" type=\"submit\">Save</button></div><br />\n";
	echo "<input type=\"hidden\" id=\"autosaveHID\" name=\"autosave\" value=\"0\" />\n";
    echo "</form>\n";
	echo "<div id=\"addButDirtyMOD\" class=\"modal hide\" data-backdrop=\"static\">\n";
	echo "  <div class=\"modal-header\">\n";
	echo "    <button type=\"button\" class=\"close\" onclick=\"panelInterests.dismissAutosaveWarn();\" aria-hidden=\"true\">&times;</button>\n";
	echo "    <h3>Unsaved edits</h3>\n";
	echo "  </div>\n";
	echo "  <div class=\"modal-body\">\n";
	echo "    <p>You have unsaved edits which will be lost by adding a new session to your list.  Please save your edits first.</p>\n";
	echo "  </div>\n";
	echo "  <div class=\"modal-footer\">\n";
	echo "    <a href=\"#\" class=\"btn btn-primary\" onclick=\"panelInterests.doAutosave();\">Save changes</a>\n";
	echo "    <a href=\"#\" class=\"btn\" onclick=\"$('#addFRM').get(0).submit();\">Continue without saving</a>\n";
	echo "    <a href=\"#\" class=\"btn\" data-dismiss=\"modal\">Cancel</a>\n";
	echo "  </div>\n";
	echo "</div>\n";
	echo "<div id=\"autosaveMOD\" class=\"modal hide\" data-backdrop=\"static\">\n";
	echo "  <div class=\"modal-header\">\n";
	echo "    <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>\n";
	echo "    <h3>Please save</h3>\n";
	echo "  </div>\n";
	echo "  <div class=\"modal-body\">\n";
	echo "    <p>You have been editing your responses for 10 minutes or more without saving your work.  Please save now.</p>\n";
	echo "  </div>\n";
	echo "  <div class=\"modal-footer\">\n";
	echo "    <a href=\"#\" class=\"btn btn-primary\" onclick=\"panelInterests.doAutosave();\">Save changes</a>\n";
	echo "    <a href=\"#\" class=\"btn\" onclick=\"panelInterests.dismissAutosaveWarn();\" >Dismiss</a>\n";
	echo "  </div>\n";
	echo "</div>\n";
    participant_footer();
    }
?>
