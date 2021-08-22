<?php

function renderMyDetails ($title, $error, $message) {
    global $newrow;
    global $dayjob, $accessibilityissues, $ethnicity, $gender, $sexualorientation, $agerangeid, $pronounid, $pronounother;

    participant_header($title, false, 'Normal', true);
    echo("<div class=\"mt-2\">");
    if ($error) {
        echo "<p class=\"alert alert-error\">Database not updated.<BR>" . $message . "</p>";
    } elseif ($message != "") {
        echo "<p class=\"alert alert-success\">" . $message . "</p>";
    }
    if (!may_I('my_gen_int_write')) {
        echo "<p><b>Changes cannot be made at this time.</b></p>\n";
    }

    echo "<div id=constraint>\n";

    echo "<p>";
    echo CON_NAME;
    echo " is committed to diverse panelist representation on our program items. To help us do that, please consider filling in the following OPTIONAL items of demographic information. All answers will be kept strictly confidential.</p>\n";

    echo "<form name=\"addform\" method=\"POST\" action=\"SubmitMyDetails.php\" >\n";
    echo "    <input type=\"hidden\" name=\"newrow\" value=\"" . ($newrow ? 1 : 0) . "\" />\n";


    echo "    <div class=\"row-fluid\">\n";

    echo "        <div class=\"control-group\">\n";
    echo "            <div class=\"controls\">\n";
    echo "                <label for=\"dayjob\" class=\"control-label nowidth\"><p>Day Job: </p></label>\n";
    echo "                <input type=\"text\" size=\"20\" class=\"span2\" name=\"dayjob\" value=\"" . htmlspecialchars($dayjob, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "            </div>\n";
    echo "        </div>\n";

    echo "        <div class=\"control-group\">\n";
    echo "            <label for=\"accessibilityissues\"><p>Do you have any accessibility issues that we should be aware of?</p></label>\n";
    echo "            <textarea class=\"span12\" name=\"accessibilityissues\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($accessibilityissues, ENT_COMPAT) . "</textarea>\n";
    echo "        </div>\n";

    echo "        <div class=\"control-group\">\n";
    echo "            <label for=\"agerangeid\" class=\"control-label\">Age Range: </label>\n";
    echo "            <div class=\"controls\">\n";
    echo "                <select name=\"agerangeid\" class=\"span2\">\n";
    populate_select_from_table("AgeRanges", $agerangeid, "", false);
    echo "                </select>\n";
    echo "            </div>\n";
    echo "        </div>\n";

    echo "        <div class=\"control-group\">\n";
    echo "            <div class=\"controls\">\n";
    echo "                <label for=\"ethnicity\" class=\"control-label nowidth\"><p>Race/Ethnicity: </p></label>\n";
    echo "                <input type=\"text\" size=\"20\" class=\"span2\" name=\"ethnicity\" value=\"" . htmlspecialchars($ethnicity, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "            </div>\n";
    echo "        </div>\n";

    echo "    <div class=\"control-group\">\n";
    echo "        <div class=\"controls\">\n";
    echo "            <label for=\"gender\" class=\"control-label nowidth\"><p>Gender: </p></label>\n";
    echo "            <input type=\"text\" size=\"20\" class=\"span2\" name=\"gender\" value=\"" . htmlspecialchars($gender, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "        </div>\n";
    echo "    </div>\n";

    echo "    <div class=\"control-group\">\n";
    echo "        <div class=\"controls\">\n";
    echo "            <label for=\"sexualorientation\" class=\"control-label nowidth\"><p>Sexual Orientation: </p></label>\n";
    echo "            <input type=\"text\" size=\"20\" class=\"span2\" name=\"sexualorientation\" value=\"" . htmlspecialchars($sexualorientation, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "        </div>\n"; 
    echo "    </div>\n"; 

    echo "    <div class=\"control-group\">\n";
    echo "        <label for=\"pronounid\" class=\"control-label\">My pronouns are: </label>\n";
    echo "        <div class=\"controls\">\n";
    echo "            <select name=\"pronounid\" class=\"span2\">\n";
    populate_select_from_table("Pronouns", $pronounid, "", false);
    echo "            </select>\n";
    echo "        </div>\n";
    echo "    </div>\n";

    echo "    <div class=\"control-group\">\n";
    echo "        <label for=\"pronounother\" class=\"control-label nowidth\">If you selected \"other\" above, provide your pronouns here: </label>\n";
    echo "        <div class=\"controls\">\n";
    echo "            <input type=\"text\" size=\"20\" class=\"span2\" name=\"pronounother\" value=\"" . htmlspecialchars($pronounother, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "        </div>\n";
    echo "    </div>\n";

    echo "</div>\n";


    echo "<div id=\"submit\" class=\"row mt-3\"><div class=\"col-12\">\n";
    if (may_I('my_gen_int_write')) {
        echo "<button class=\"btn btn-primary\" type=\"submit\" name=\"submit\" >Save</button>\n";
    }
    echo "</div></div>\n";
    echo "</form>\n";
    echo "</div></div>\n";
} ?>
