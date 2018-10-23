<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function renderMyInterests($title, $error, $message, $rolearray) {
    global $link, $yespanels, $nopanels, $yespeople, $nopeople;
    global $otherroles, $newrow;
    $rolerows = count($rolearray);
    participant_header($title);
    if ($error) {
        echo "<p class=\"alert alert-error\">Database not updated.<br>" . $message . "</p>";
    } elseif ($message != "") {
        echo "<p class=\"alert alert-success\">" . $message . "</p>";
    }
    if (!may_I('my_gen_int_write')) {
        echo "<p>We're sorry, but we are unable to accept your suggestions at this time.\n";
    }
    echo "<form class=\"form-horizontal\" name=\"addform\" method=POST action=\"SubmitMyInterests.php\">\n";
    echo "<input type=\"hidden\" name=\"newrow\" value=\"" . ($newrow ? 1 : 0) . "\">\n";
    echo "<input type=\"hidden\" name=\"rolerows\" value=\"" . $rolerows . "\">\n";
    echo "<div class=\"row-fluid\">\n";
    echo "  <div class=\"span6\">\n";
    echo "    <label for=\"yespanels\"><p>Workshops or presentations I'd like to run: </p></label>\n";
    echo "    <textarea class=\"span12\" name=\"yespanels\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($yespanels, ENT_COMPAT) . "</textarea>\n";

    echo "  </div>\n";
    echo "  <div class=\"span6\">\n";
    echo "    <label for=\"nopanels\"><p>Panel types I am not interested in participating in:</p></label>\n";
    echo "    <textarea class=\"span12\" name=\"nopanels\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($nopanels, ENT_COMPAT) . "</textarea>\n";
    echo "</div>\n";
    echo "<div class=\"row-fluid\">\n";
    echo "  <div class=\"span6\">\n";
    echo "    <label for=\"yespeople\"><p>People with whom I'd like to be on a session: (Leave blank for none)</p></label>\n";
    echo "    <textarea class=\"span12\" name=\"yespeople\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($yespeople, ENT_COMPAT) . "</textarea>\n";
    echo "  </div>\n";
    echo "  <div class=\"span6\">\n";
    echo "    <label for=\"nopeople\"><p>People with whom I'd rather not be on a session: (Leave blank for none)</p></label>\n";
    echo "    <textarea class=\"span12\" name=\"nopeople\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($nopeople, ENT_COMPAT) . "</textarea>\n";
    echo "  </div>\n";
    echo "</div>\n";
    echo "<p>Roles I'm willing to take on:</p>\n";
    echo "<div class=\"row-fluid\">\n";
    echo "    <div class=\"control-group span12\">\n";
    for ($i = 1; $i < $rolerows - 1; $i += 2) {
        echo "                <label class=\"checkbox inline long\" for=\"willdorole" . $i . "\">" . $rolearray[$i]["rolename"] . "\n";
        echo "                <input type=checkbox name=\"willdorole" . $i . "\" id=\"willdorole" . $i . "\"";
        if (isset($rolearray[$i]["badgeid"])) {
            echo "checked";
        }
        if (!may_I('my_gen_int_write')) {
            echo " disabled";
        }
        echo "></label>\n";
        echo "                <input type=hidden name=\"diddorole" . $i . "\" value=\"";
        echo ((isset($rolearray[$i]["badgeid"])) ? 1 : 0) . "\">\n";
        echo "                <input type=hidden name=\"roleid" . $i . "\" value=\"" . $rolearray[$i]["roleid"] . "\">\n";
        echo "                <input type=hidden name=\"rolename" . $i . "\" value=\"" . $rolearray[$i]["rolename"] . "\">\n";
        if ($i+1 >= $rolerows-1) {
            break;
        }
        echo "                <label class=\"checkbox inline long\" for=\"willdorole" . ($i + 1) . "\">" . $rolearray[$i + 1]["rolename"] . "\n";
        echo "                <input class=\"checkbox\" type=checkbox name=\"willdorole" . ($i + 1) . "\" ";
        if (isset($rolearray[$i + 1]["badgeid"])) {
            echo "checked";
        }
        if (!may_I('my_gen_int_write')) {
            echo " disabled";
        }
        echo "></label>\n";
        echo "                <input type=hidden name=\"diddorole" . ($i + 1) . "\" value=\"";
        echo ((isset($rolearray[$i + 1]["badgeid"])) ? 1 : 0) . "\">\n";
        echo "                <input type=hidden name=\"roleid" . ($i + 1) . "\" value=\"" . $rolearray[$i + 1]["roleid"] . "\">\n";
        echo "                <input type=hidden name=\"rolename" . ($i + 1) . "\" value=\"" . $rolearray[$i + 1]["rolename"] . "\">\n";
    }

    echo "                <label class=\"checkbox inline long\" for=\"willdorole0\">" . $rolearray[0]["rolename"] . "  (Please describe below)";
    echo "                <input class=\"checkbox\" type=checkbox name=\"willdorole0\" ";
    if (isset($rolearray[0]["badgeid"])) {
        echo "checked";
    }
    if (!may_I('my_gen_int_write')) {
        echo " disabled";
    }
    echo "> </label>\n";
    echo "                <input type=hidden name=\"roleid0\" value=\"" . $rolearray[0]["roleid"] . "\">\n";
    echo "                <input type=hidden name=\"rolename0\" value=\"" . $rolearray[0]["rolename"] . "\">\n";
    echo "                <input type=hidden name=\"diddorole0\" value=\"";
    echo ((isset($rolearray[0]["badgeid"])) ? 1 : 0) . "\">\n";
    echo "<p>Description for \"Other\":</p>\n";
    echo "<textarea class=\"span12\" name=\"otherroles\" rows=5 cols=72";
    if (!may_I('my_gen_int_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($otherroles, ENT_COMPAT) . "</textarea>\n";
    echo "</div>\n";
    echo "<div class=\"submit\">\n";
    echo "    <div id=\"submit\">";
    if (may_I('my_gen_int_write')) {
        echo "<button class=\"btn btn-primary\" type=\"submit\" name=\"submit\" >Save</button>";
    }
    echo "</div>\n";
    echo "    </div>\n";
    echo "</form>\n";
} ?>
