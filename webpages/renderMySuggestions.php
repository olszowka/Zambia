<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function renderMySuggestions($title, $error, $message, $paneltopics, $otherideas, $suggestedguests) {
    participant_header($title);
    if ($error) {
        echo "<p class=\"alert alert-error\">" . $message . "</p>";
    } elseif ($message != "") {
        echo "<p class=\"alert alert-success\">" . $message . "</p>";
    }
    if (!may_I('my_suggestions_write')) {
        echo "<p>We're sorry, but we are unable to accept your suggestions at this time.\n";
    }
    echo "<form name=\"addform\" method=\"post\" action=\"SubmitMySuggestions.php\">\n";
    echo "<div class=\"titledtextarea\">\n";
    echo "    <label for=\"paneltopics\">Program Topic Ideas:</label>\n";
    echo "    <textarea name=\"paneltopics\" rows=\"6\" cols=\"72\"";
    if (!may_I('my_suggestions_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($paneltopics, ENT_COMPAT) . "</textarea>\n";
    echo "    </div>\n";
    echo "<div class=\"titledtextarea\">\n";
    echo "    <label for=\"otherideas\">Other Programming Ideas:</label>\n";
    echo "    <textarea name=\"otherideas\" rows=\"6\" cols=\"72\"";
    if (!may_I('my_suggestions_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($otherideas, ENT_COMPAT) . "</textarea>\n";
    echo "    </div>\n";
    echo "<div class=\"titledtextarea\">\n";
    echo "    <label for=\"suggestedguests\">Suggested Guests (please provide addresses and other contact information if possible):</label>\n";
    echo "    <textarea name=\"suggestedguests\" rows=\"8\" cols=\"72\"";
    if (!may_I('my_suggestions_write')) {
        echo " readonly class=\"readonly\"";
    }
    echo ">" . htmlspecialchars($suggestedguests, ENT_COMPAT) . "</textarea>\n";
    echo "    </div>\n";
    echo "<div class=\"submit\">\n";
    if (may_I('my_suggestions_write')) {
        echo "<div id=\"submit\"><button class=\"SubmitButton\" type=\"submit\" name=\"submit\">Save</button></div>\n";
    }
    echo "</div>\n";
    echo "</form>\n";
    participant_footer();
} ?>
