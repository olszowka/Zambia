<?php
// Copyright (c) 2009-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
function render_session_interests($session_interest_count,$message,$message_error, $pageIsDirty, $showNotAttendingWarning) {
    global $session_interests, $title;
    participant_header($title, false, 'Normal', 'bs5');
    echo "<div class=\"container-xl\">\n";
    if ($showNotAttendingWarning) {
        echo "<div class=\"alert alert-warning mt-3\" role=\"alert\">\n";
        echo "    <h4>Warning!</h4>\n";
        echo "    <span>\n";
        echo "        You have not indicated in your profile that you will be attending " . CON_NAME . ".\n";
        echo "        You will not be able to save your panel choices until you so do.\n";
        echo "    </span>\n";
        echo "</div>\n";
        $disabled = "disabled=\"disabled\" ";
    } else {
        $disabled = "";
    }
    if ($message_error) {
        echo "<p class=\"alert alert-danger mt-3\">Database not updated.<br />" . $message_error . "</p>\n";
    }
    if ($message) {
        echo "<p class=\"alert alert-success mt-3\">" . $message . "</p>\n";
    }
    if ($pageIsDirty) {
        echo "<input type=\"hidden\" id=\"pageIsDirty\" value=\"true\" />\n";
    }
    // "Add" Section
    echo "<form id=\"addFRM\" name=\"addform\" method=\"POST\" action=\"PartPanelInterests_POST.php\" class=\"row g-2 align-items-end mt-3\">\n";
    echo "  <div class=\"col-auto\">\n";
    echo "    <label class=\"form-label\" for=\"addsessionid\">Add Session ID to my List:</label>\n";
    echo "    <input type=\"text\" class=\"form-control\" id=\"addsessionid\" name=\"addsessionid\" size=\"10\" style=\"width: 8em\" $disabled/>\n";
    echo "  </div>\n";
    echo "  <input type=\"hidden\" name=\"add\" />\n";
    echo "  <div class=\"col-auto\">\n";
    echo "    <button class=\"btn btn-primary\" type=\"button\" onclick=\"panelInterests.onClickAdd();\" $disabled>Add</button>\n";
    echo "  </div>\n";
    echo "</form>\n";
    echo "<hr />\n";
    // "Update Ranks" Section
    echo "<form id=\"sessionFRM\" name=\"sessionform\" method=\"POST\" action=\"PartPanelInterests_POST2.php\">\n";
    echo "<input type=\"hidden\" name=\"submitranks\" value=\"1\" />\n";
    echo "<div class=\"d-flex justify-content-end mb-2\"><button class=\"btn btn-primary\" type=\"submit\" $disabled>Save</button></div>\n";
    echo "<p>Please use the following scale when ranking your interest in the sessions you have chosen:</p>\n";
    echo "<p><strong>1 &mdash;<em> Oooh! Oh! Pick Me!</em>&nbsp;&nbsp;&nbsp;2-3 &mdash; <em>I'd like to if I can</em>&nbsp;&nbsp;&nbsp;4-5 &ndash; <em>I am qualified but this is not one of my primary interests</em></strong></p>\n";
    echo "<p>You are limited to 4 sessions each of preferences 1-4.  There is no limit to the number of sessions for which you can express preference 5.</p>\n";
    echo "<h4>List of Sessions in Which I'm Interested in Participating:</h4>\n";
    $j = 1; //use $j so that skipped sessions don't skip numbering
    for ($i = 1; $i <= $session_interest_count; $i++) {
        if (!$session_interests[$i]['title']) continue;
        echo "  <div class=\"card mb-3 bg-transparent\">\n";
        echo "    <div class=\"card-body\">\n";
        echo "      <div class=\"row align-items-center interest-edit-row\">\n";
        echo "        <div class=\"col-md-6 col-lg-5\">\n";
        echo "            Session {$session_interests[$i]['sessionid']}\n";
        echo "            <input type=\"hidden\" name=\"sessionid$j\" value=\"{$session_interests[$i]['sessionid']}\" />\n";
        echo "        </div>\n";
        echo "        <div class=\"col-md-8 col-lg-7\">{$session_interests[$i]['trackname']}</div>\n";
        echo "        <div class=\"col-md-14 col-lg-18\">" . htmlspecialchars($session_interests[$i]['title'], ENT_NOQUOTES) . "</div>\n";
        echo "        <div class=\"col-md-8 col-lg-6\">Duration: {$session_interests[$i]['duration']}</div>\n";
        echo "      </div>\n";
        echo "      <div class=\"row align-items-center mt-2 interest-edit-row\">\n";
        echo "        <div class=\"col-md-9\">\n";
        echo "            <label class=\"form-label\" for=\"rankINP_$j\">Rank:</label>\n";
        echo "            <input type=\"text\" id=\"rankINP_$j\" size=\"2\" class=\"form-control rankINP\" style=\"width: 4em\" name=\"rank$j\" value=\"{$session_interests[$i]['rank']}\" $disabled/>\n";
        echo "        </div>\n";
        echo "        <div class=\"col-md-13\">\n";
        echo "            <div class=\"form-check\">\n";
        echo "                <input type=\"checkbox\" class=\"form-check-input\" id=\"modCHK_$j\" value=\"1\" name=\"mod$j\" ".(($session_interests[$i]['willmoderate'])?"checked":"")." $disabled/>\n";
        echo "                <label class=\"form-check-label\" for=\"modCHK_$j\">I'd like to moderate this session</label>\n";
        echo "            </div>\n";
        echo "        </div>\n";
        echo "        <div class=\"col-md-14\">\n";
        echo "            <div class=\"form-check\">\n";
        echo "                <input type=\"checkbox\" class=\"form-check-input\" id=\"deleteCHK_$j\" value=\"1\" name=\"delete$j\" $disabled/>\n";
        echo "                <label class=\"form-check-label\" for=\"deleteCHK_$j\">Remove this session from my list</label>\n";
        echo "            </div>\n";
        echo "        </div>\n";
        echo "      </div>\n";
        echo "      <div class=\"row mt-2 interest-edit-row\">\n";
        echo "        <div class=\"col-36\">\n";
        echo "            <label class=\"form-label\" for=\"commentsTXTA_$j\">Use this space to convince us why you would be fabulous on this session:</label>\n";
        $s_i_c = is_null($session_interests[$i]['comments']) ? "" : htmlspecialchars( $session_interests[$i]['comments'],ENT_COMPAT);
        echo "            <textarea id=\"commentsTXTA_$j\" class=\"form-control\" rows=\"3\" cols=\"80\" name=\"comments$j\" $disabled>$s_i_c</textarea>\n";
        echo "        </div>\n";
        echo "      </div>\n";
        echo "      <div class=\"row mt-2\">\n";
        echo "        <div class=\"col-36\">" . htmlspecialchars($session_interests[$i]['progguiddesc'], ENT_NOQUOTES) . "</div>\n";
        echo "      </div>\n";
        if ($session_interests[$i]['persppartinfo']) {
            echo "      <div class=\"row mt-2\">\n";
            echo "        <div class=\"col-36\">\n";
            echo "            <div class=\"alert alert-info mb-0\">" . htmlspecialchars($session_interests[$i]['persppartinfo'], ENT_NOQUOTES) . "</div>\n";
            echo "        </div>\n";
            echo "      </div>\n";
        }
        echo "    </div>\n"; // card-body
        echo "  </div>\n"; // card
        $j++;
    }
    echo "<div class=\"d-flex justify-content-end mb-3\"><button class=\"btn btn-primary\" type=\"submit\" $disabled>Save</button></div>\n";
    echo "<input type=\"hidden\" id=\"autosaveHID\" name=\"autosave\" value=\"0\" />\n";
    echo "</form>\n";
    echo "<div class=\"modal\" id=\"addButDirtyMOD\" data-bs-backdrop=\"static\" tabindex=\"-1\" role=\"dialog\">\n";
    echo "  <div class=\"modal-dialog\" role=\"document\">\n";
    echo "    <div class=\"modal-content\">\n";
    echo "      <div class=\"modal-header\">\n";
    echo "        <h5 class=\"modal-title\">Unsaved edits</h5>\n";
    echo "        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" onclick=\"panelInterests.dismissAutosaveWarn();\" aria-label=\"Close\"></button>\n";
    echo "      </div>\n";
    echo "      <div class=\"modal-body\">\n";
    echo "        <p>You have unsaved edits which will be lost by adding a new session to your list.  Please save your edits first.</p>\n";
    echo "      </div>\n";
    echo "      <div class=\"modal-footer\">\n";
    echo "        <button type=\"button\" class=\"btn btn-primary\" onclick=\"panelInterests.doAutosave();\">Save changes</button>\n";
    echo "        <button type=\"button\" class=\"btn btn-secondary\" onclick=\"$('#addFRM').get(0).submit();\">Continue without saving</button>\n";
    echo "        <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Cancel</button>\n";
    echo "      </div>\n";
    echo "    </div>\n";
    echo "  </div>\n";
    echo "</div>\n";
    echo "<div class=\"modal\" id=\"autosaveMOD\" data-bs-backdrop=\"static\" tabindex=\"-1\" role=\"dialog\">\n";
    echo "  <div class=\"modal-dialog\" role=\"document\">\n";
    echo "    <div class=\"modal-content\">\n";
    echo "      <div class=\"modal-header\">\n";
    echo "        <h5 class=\"modal-title\">Please save</h5>\n";
    echo "        <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>\n";
    echo "      </div>\n";
    echo "      <div class=\"modal-body\">\n";
    echo "        <p>You have been editing your responses for 10 minutes or more without saving your work.  Please save now.</p>\n";
    echo "      </div>\n";
    echo "      <div class=\"modal-footer\">\n";
    echo "        <button type=\"button\" class=\"btn btn-primary\" onclick=\"panelInterests.doAutosave();\">Save changes</button>\n";
    echo "        <button type=\"button\" class=\"btn btn-secondary\" onclick=\"panelInterests.dismissAutosaveWarn();\">Dismiss</button>\n";
    echo "      </div>\n";
    echo "    </div>\n";
    echo "  </div>\n";
    echo "</div>\n";
    echo "</div>\n"; // container-xl
    participant_footer();
}
?>
