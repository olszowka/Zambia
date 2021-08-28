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

    echo "<div id=\"container-fluid\" class=\"container mt-2 mb-4\">\n";

    echo "<h5 class=\"center alert-info\">";
    echo CON_NAME;
    echo " is committed to diverse panelist representation on our program items. To help us do that, please consider filling in the following OPTIONAL items of demographic information. All answers will be kept strictly confidential.";
    echo "</h5>\n";
    echo "<hr>\n";

    echo "<form name=\"addform\" method=\"POST\" action=\"SubmitMyDetails.php\">\n";
    echo "    <input type=\"hidden\" name=\"newrow\" value=\"" . ($newrow ? 1 : 0) . "\" />\n";



    echo "        <div class=\"row\">\n";  //first row

    echo "            <div class=\"col-auto\">\n";
    echo "                <label for=\"dayjob\">Day Job: </label>\n";
    echo "            </div>\n";
    echo "            <div class=\"col-auto\">\n";
    echo "                <input type=\"text\" size=\"20\" class=\"mycontrol\" name=\"dayjob\" value=\"" . htmlspecialchars($dayjob, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "            </div>\n";

    echo "            <div class=\"col-auto\">\n";
    echo "                <label for=\"agerangeid\">Age Range: </label>\n";
    echo "            </div>\n";
    echo "            <div class=\"col-auto\">\n";
    echo "                <select name=\"agerangeid\" class=\"mycontrol\">\n";
    populate_select_from_table("AgeRanges", $agerangeid, "", false);
    echo "                </select>\n";
    echo "            </div>\n";

    echo "            <div class=\"col-auto\">\n";
    echo "                <label for=\"ethnicity\">Race/Ethnicity: </label>\n";
    echo "            </div>\n";
    echo "            <div class=\"col-auto\">\n";
    echo "                <input type=\"text\" size=\"20\" class=\"mycontrol\" name=\"ethnicity\" value=\"" . htmlspecialchars($ethnicity, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "            </div>\n";

    echo "        </div>\n";   //end of top row



    echo "        <div class=\"row mt-3\">\n";    //second row

    echo "            <div class=\"col-12\">\n";
    echo "                <label for=\"accessibilityissues\">Do you have any accessibility issues that we should be aware of?</label>\n";
    echo "                <textarea class=\"form-control\" name=\"accessibilityissues\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($accessibilityissues, ENT_COMPAT) . "</textarea>\n";
    echo "            </div>\n";

    echo "        </div>\n";    //end of second row



    echo "        <div class=\"row mt-3\">\n";    //third row

    echo "            <div class=\"col-auto\">\n";
    echo "                <label for=\"gender\">Gender: </label>\n";
    echo "            </div>\n";
    echo "            <div class=\"col-auto\">\n";
    echo "                <input type=\"text\" size=\"20\" class=\"mycontrol\" name=\"gender\" value=\"" . htmlspecialchars($gender, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "            </div>\n";

    echo "            <div class=\"col-auto\">\n";
    echo "                <label for=\"sexualorientation\">Sexual Orientation: </label>\n";
    echo "            </div>\n";
    echo "            <div class=\"col-auto\">\n";
    echo "                <input type=\"text\" size=\"20\" class=\"mycontrol\" name=\"sexualorientation\" value=\"" . htmlspecialchars($sexualorientation, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "            </div>\n"; 

    echo "        </div>\n";   //end of third row



    echo "        <div class=\"row mt-3\">\n";    //fourth row

    echo "            <div class=\"col-auto\">\n";
    echo "                <label for=\"pronounid\">My pronouns are: </label>\n";
    echo "            </div>\n";
    echo "            <div class=\"col-auto\">\n";
    echo "                <select name=\"pronounid\" class=\"mycontrol\">\n";
    populate_select_from_table("Pronouns", $pronounid, "", false);
    echo "                </select>\n";
    echo "            </div>\n";

    echo "        </div>\n";    //end of fourth row


    echo "        <div class=\"row mt-3\">\n";    //fifth row

    echo "            <div class=\"col-auto\">\n";
    echo "                <label for=\"pronounother\">If you selected \"other\" for your pronouns, provide your pronouns here: </label>\n";
    echo "            </div>\n";
    echo "            <div class=\"col-auto\">\n";
    echo "                <input type=\"text\" size=\"20\" class=\"mycontrol\" name=\"pronounother\" value=\"" . htmlspecialchars($pronounother, ENT_COMPAT) . "\"";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">\n";
    echo "            </div>\n";

    echo "        </div>\n";    //end of fifth row




//    echo "</div>\n";


    echo "    <div id=\"submit\" class=\"row mt-3\">\n";
    echo "        <div class=\"col-12\">\n";
    if (may_I('my_gen_int_write')) {
        echo "        <button class=\"btn btn-primary\" type=\"submit\" name=\"submit\" >Save</button>\n";
    }
    echo "        </div>\n";
    echo "    </div>\n";

    //echo "</div>\n";
    echo "</form>\n";

    echo "</div>\n";
    echo "</div>\n";
} ?>
