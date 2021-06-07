<?php
// Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
function renderMyInterests($title, $error, $message, $rolearray) {
    global $link, $yespanels, $nopanels, $yespeople, $nopeople;
    global $otherroles, $newrow;
    $rolerows = $rolearray['count'];
    participant_header($title, false, 'Normal', true);
    echo("<div class=\"mt-2\">");
    if ($error) {
        echo "<p class=\"alert alert-error\">Database not updated.<br>" . $message . "</p>";
    } elseif ($message != "") {
        echo "<p class=\"alert alert-success\">" . $message . "</p>";
    }
    if (!may_I('my_gen_int_write')) {
        echo "<p>We're sorry, but we are unable to accept your suggestions at this time.\n";
    }
    echo "<form name=\"addform\" method=POST action=\"SubmitMyInterests.php\">\n";
    echo "<input type=\"hidden\" name=\"newrow\" value=\"" . ($newrow ? 1 : 0) . "\">\n";
    echo "<input type=\"hidden\" name=\"rolerows\" value=\"" . $rolerows . "\">\n";
    echo "<div class=\"row mt-3\">\n";
    echo "  <div class=\"col-lg-6\">\n";
    echo "    <label for=\"yespanels\">Workshops or presentations I'd like to run:</label>\n";
    echo "    <textarea class=\"form-control\" id=\"yespanels\" name=\"yespanels\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($yespanels, ENT_COMPAT) . "</textarea>\n";

    echo "  </div>\n";
    echo "  <div class=\"col-lg-6\">\n";
    echo "    <label for=\"nopanels\">Panel types I am not interested in participating in:</label>\n";
    echo "    <textarea class=\"form-control\" id=\"nopanels\" name=\"nopanels\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($nopanels, ENT_COMPAT) . "</textarea>\n";
    echo "    </div>\n";
    echo "</div>\n";
    echo "<div class=\"row mt-3\">\n";
    echo "  <div class=\"col-lg-6\">\n";
    echo "    <label for=\"yespeople\">People with whom I'd like to be on a session: (Leave blank for none)</label>\n";
    echo "    <textarea class=\"form-control\" id=\"yespeople\" name=\"yespeople\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($yespeople, ENT_COMPAT) . "</textarea>\n";
    echo "  </div>\n";
    echo "  <div class=\"col-lg-6\">\n";
    echo "    <label for=\"nopeople\">People with whom I'd rather not be on a session: (Leave blank for none)</label>\n";
    echo "    <textarea class=\"form-control\" id=\"nopeople\" name=\"nopeople\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($nopeople, ENT_COMPAT) . "</textarea>\n";
    echo "  </div>\n";
    echo "</div>\n";
    echo "<p class=\"mt-3\">Roles I'm willing to take on:</p>\n";
    echo "<div class=\"row mt-3\">\n";
    for ($i = 1; $i < $rolerows; $i++) {
        echo "        <div class=\"col-md-4\">";
        echo "                <input type=checkbox name=\"willdorole" . $i . "\" id=\"willdorole" . $i . "\"";
        if (isset($rolearray[$i]["badgeid"])) {
            echo "checked";
        }
        if (!may_I('my_gen_int_write')) {
            echo " disabled";
        }
        echo ">";
        echo "    <label class=\"checkbox\" for=\"willdorole" . $i . "\">" . $rolearray[$i]["rolename"] . "\n";
        echo "                <input type=hidden name=\"diddorole" . $i . "\" value=\"";
        echo ((isset($rolearray[$i]["badgeid"])) ? 1 : 0) . "\">\n";
        echo "                <input type=hidden name=\"roleid" . $i . "\" value=\"" . $rolearray[$i]["roleid"] . "\">\n";
        echo "                <input type=hidden name=\"rolename" . $i . "\" value=\"" . $rolearray[$i]["rolename"] . "\">\n";
        echo "</div>\n";
    }

    echo "        <div class=\"col-md-4\">";
    echo "                <input class=\"checkbox\" type=checkbox name=\"willdorole0\" id=\"willdorole0\" ";
    if (isset($rolearray[0]["badgeid"])) {
        echo "checked";
    }
    if (!may_I('my_gen_int_write')) {
        echo " disabled";
    }
    echo ">\n";
    echo "<label class=\"checkbox\" for=\"willdorole0\">" . $rolearray[0]["rolename"] . "  (Please describe below)</label>";
    echo "                <input type=hidden name=\"roleid0\" value=\"" . $rolearray[0]["roleid"] . "\">\n";
    echo "                <input type=hidden name=\"rolename0\" value=\"" . $rolearray[0]["rolename"] . "\">\n";
    echo "                <input type=hidden name=\"diddorole0\" value=\"";
    echo ((isset($rolearray[0]["badgeid"])) ? 1 : 0) . "\">\n";
    echo "</div></div>";
    echo "<div class=\"row\"><div class=\"col-12\">";
    echo "<label class=\"mt-3\" for=\"otherroles\">Description for \"Other\" Roles:</label>\n";
    echo "<textarea class=\"form-control\" id=\"otherroles\" name=\"otherroles\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($otherroles, ENT_COMPAT) . "</textarea>\n";
    echo "</div></div>\n";
    echo "<div id=\"submit\" class=\"row mt-3\"><div class=\"col-12\">\n";
    if (may_I('my_gen_int_write')) {
        echo "<button class=\"btn btn-primary\" type=\"submit\" name=\"submit\" >Save</button>\n";
    }
    echo "</div></div>\n";
    echo "</form>\n";
    echo "</div>\n";
} ?>
