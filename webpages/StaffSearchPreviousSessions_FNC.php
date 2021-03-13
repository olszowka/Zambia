<?php
//	Copyright (c) 2010-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
function SetSessionSearchParameterDefaults() {
    global $SessionSearchParameters;
    $SessionSearchParameters['currenttrack'] = 0;
    $SessionSearchParameters['previouscontrack'] = 0;
    $SessionSearchParameters['previouscon'] = 0;
    $SessionSearchParameters['type'] = 0;
    $SessionSearchParameters['status'] = 0;
    $SessionSearchParameters['title'] = '';
    $SessionSearchParameters['showimported'] = false;
}

function RenderSearchPreviousSessions() {
    global $SessionSearchParameters, $message_error, $message;
    if ($message_error) {
        echo "<p class=\"alert alert-error\">$message_error</p>\n";
    } elseif ($message != "") {
        echo "<p class=\"alert alert-success\">$message</p>\n";
    }
    ?>

    <form method="POST" action="ShowPreviousSessions.php" class="well form-inline">
        <fieldset>
            <p>Use this page to search for session records from previous cons to import to the current list of
                sessions.</p>
            <div class="row-fluid">
                <div class="span2">
                    <label for="currenttrack" class="control-label">Current Track: </label>
                    <select name="currenttrack" class="xspan2">
                        <?php populate_select_from_table("Tracks", $SessionSearchParameters['currenttrack'], "Any", true); //$table_name, $default_value, $option_0_text, $default_flag ?>
                    </select>
                </div>
                <div class="span2">
                    <label for="previoustrack" class="control-label">Obsolete Track: </label>
                    <select name="previoustrack" class="xspan2">
                        <?php $query = <<<EOD
SELECT
        CONCAT(PC.previousconid,"a",PCT.previoustrackid), CONCAT(PC.previousconname,": ",PCT.trackname)
    FROM
        PreviousCons PC JOIN
	PreviousConTracks PCT USING (previousconid) LEFT JOIN
	TrackCompatibility TC USING (previousconid, previoustrackid)
    WHERE
        TC.currenttrackid IS NULL
    ORDER BY
        PC.display_order, PCT.previoustrackid
EOD;
                        populate_select_from_query($query, $SessionSearchParameters['previouscontrack'], "ANY", true); ?>
                    </select>
                </div>
                <div class="span2">
                    <label class="control-label" for="previouscon">Previous Con: </label>
                    <select name="previouscon" class="xspan2">
                        <?php populate_select_from_table("PreviousCons", $SessionSearchParameters['previouscon'], "Any", true); //$table_name, $default_value, $option_0_text, $default_flag ?>
                    </select>
                </div>
                <div class="span2">
                    <label class="control-label" for="type">Type: </label>
                    <select name="type" class="xspan2">
                        <?php populate_select_from_table("Types", $SessionSearchParameters['type'], "Any", true); //$table_name, $default_value, $option_0_text, $default_flag ?>
                    </select>
                </div>
                <div class="span2">
                    <label class="control-label" for="status">Status: </label>
                    <select name="status" class="xspan2">
                        <?php $query = <<<EOD
SELECT
        ST.statusid, ST.statusname
    FROM
        SessionStatuses ST
    WHERE
        ST.statusid IN (SELECT DISTINCT previousstatusid FROM PreviousSessions)
    ORDER BY
        ST.display_order
EOD;
                        populate_select_from_query($query, $SessionSearchParameters['status'], "ANY", true); ?>
                    </select>
                </div>
            </div>
            <br/>
            <div class="row-fluid">
                <label class="control-label" for="title">Title: </label>
                <input type="text" name="title" size="40" value="<?php echo $SessionSearchParameters['title']; ?>">
                <span class="help-inline">Enter a word or phrase for which to search. Leave blank for any.</span>
            </div>
            <br/>
            <div class="row-fluid">
                <label class="checkbox">
                    <input type="checkbox"
                           name="showimported" <?php echo $SessionSearchParameters['showimported'] ? 'checked' : ''; ?>>
                    Include in results sessions which have been imported already.
                </label>
            </div>
            <br/>
            <div class="row-fluid">
                <button type="submit" class="btn btn-primary" value="search">Search</button>
            </div>
        </fieldset>
    </form>
<?php } // End of RenderSearchPreviousSessions()

function HandleSearchParameters() {
    // parse parameters for Search of previous sessions and validate them
    // return true if successful, false otherwise
    global $SessionSearchParameters, $message_error, $message;
    $message_error = "This page is intended to be reached from a form.  One or more required Post parameters were not provided. No further processing is possible.";
    $SessionSearchParameters = array();
    if (isset($_POST['currenttrack'])) {
        $SessionSearchParameters['currenttrack'] = $_POST['currenttrack'];
    } else {
        return (false);
    }
    if (isset($_POST['previoustrack'])) {
        $SessionSearchParameters['previouscontrack'] = $_POST['previoustrack'];
    } else {
        return (false);
    }
    if (isset($_POST['previouscon'])) {
        $SessionSearchParameters['previouscon'] = $_POST['previouscon'];
    } else {
        return (false);
    }
    if (isset($_POST['type'])) {
        $SessionSearchParameters['type'] = $_POST['type'];
    } else {
        return (false);
    }
    if (isset($_POST['status'])) {
        $SessionSearchParameters['status'] = $_POST['status'];
    } else {
        return (false);
    }
    if (isset($_POST['title'])) {
        $SessionSearchParameters['title'] = $_POST['title'];
    } else {
        return (false);
    }
    $SessionSearchParameters['showimported'] = (isset($_POST['showimported'])) ? true : false;
    if ($SessionSearchParameters['previouscontrack'] != 0) {
        sscanf($SessionSearchParameters['previouscontrack'], "%da%d", $SessionSearchParameters['previouscon2'],
            $SessionSearchParameters['previoustrack']);
        if ($SessionSearchParameters['previouscon'] != 0 &&
            $SessionSearchParameters['previouscon'] != $SessionSearchParameters['previouscon2']) {
            $message_error = "<i>Previous Track</i> is not from the con indicated by <i>Previous Con</i> so no results can be returned.";
            return (false);
        }
    }
    if (isset($SessionSearchParameters['previoustrack']) && $SessionSearchParameters['previoustrack'] != 0 &&
        $SessionSearchParameters['currenttrack'] != 0) {
        $message_error = "<i>Previous Track</i> and <i>Current Track</i> are both specified so no results can be returned.";
        Return (FALSE);
    }
    $message_error = '';
    return (true);
} // End of HandleSearchParameters()

function PerformPrevSessionSearch () {
    global $SessionSearchParameters, $message_error,$message,$result,$linki;
    $query= <<<EOD
SELECT
        PS.title, PS.progguiddesc, PS.previousconid, PS.previoussessionid, PS.importedsessionid, TY.typename,
        PC.previousconname, SS.statusname, PCT.trackname
    FROM
                  PreviousSessions PS
             JOIN PreviousCons PC USING (previousconid)
             JOIN PreviousConTracks PCT USING (previousconid, previoustrackid)
             JOIN Types TY USING (typeid)
             JOIN SessionStatuses SS ON PS.previousstatusid = SS.statusid
        LEFT JOIN TrackCompatibility TC USING (previousconid, previoustrackid)
EOD;
    if ($SessionSearchParameters['currenttrack'] != 0 || $SessionSearchParameters['previouscontrack'] != 0 ||
        $SessionSearchParameters['previouscon'] != 0 || $SessionSearchParameters['type'] != 0 ||
        $SessionSearchParameters['status'] != 0 || $SessionSearchParameters['title'] != '' ||
        !$SessionSearchParameters['showimported']) {
        $query .= " WHERE";
    }
    if ($SessionSearchParameters['currenttrack'] != 0) {
        $query .= " TC.currenttrackid={$SessionSearchParameters['currenttrack']} AND";
    }
    if ($SessionSearchParameters['previouscontrack'] != 0) {
        $query .= " PS.previoustrackid={$SessionSearchParameters['previoustrack']} AND";
        $query .= " PS.previousconid={$SessionSearchParameters['previouscon2']} AND";
    } elseif ($SessionSearchParameters['previouscon'] != 0) {
        $query .= " PS.previousconid={$SessionSearchParameters['previouscon']} AND";
    }
    if ($SessionSearchParameters['type'] != 0) {
        $query .= " PS.typeid={$SessionSearchParameters['type']} AND";
    }
    if ($SessionSearchParameters['status'] != 0) {
        $query .= " PS.previousstatusid={$SessionSearchParameters['status']} AND";
    }
    if ($SessionSearchParameters['title'] != '') {
        $query .= " PS.title LIKE \"%" . mysqli_real_escape_string($linki, $SessionSearchParameters['title']) . "%\" AND";
    }
    if (!$SessionSearchParameters['showimported']) {
        $query .= " PS.importedsessionid IS NULL AND";
    }
    if (substr($query, -4) == ' AND') {     //take last 4 characters
        $query = substr($query, 0, -4);     //drop last 4 characters
    }
    $query .= " ORDER BY PC.display_order, PS.previoustrackid";
    $result = mysqli_query($linki, $query);
    if (!$result) {
        $message_error = $query . " Error querying database.";
        return (false);
    }
    if (mysqli_num_rows($result) == 0) {
        $message_error = "No matching sessions found.";
        return (false);
    }
    return (true);
} // End of PerformPrevSessionSearch()

function RenderSearchPrevSessionResults() {
    global $result;
    $result_array = array();
    while ($result_array[] = mysqli_fetch_array($result, MYSQLI_ASSOC)) ;
    array_pop($result_array);
    echo "<div class=\"row-fluid\"><form method=POST action=\"SubmitImportSessions.php\" class=\"form-horizontal\">\n";
    echo "<div class=\"clearfix\"><button type=submit class=\"btn btn-primary pull-right\" value=\"submitimport\">Import</button></div>\n";
    echo "<table class=\"table-condensed\">\n";
    foreach ($result_array as $resultrowindex => $resultrow) {
        echo "<tr><td colspan=6><hr style='margin: 0;'/></td></tr>\n";
        echo "<tr><td rowspan=3>&nbsp;</td>";
        echo "<td colspan=5><strong>" . htmlspecialchars($resultrow['title'], ENT_NOQUOTES) . "<strong></td></tr>\n";
        echo "<tr><td><label class=\"checkbox\"><input type=\"checkbox\" name=\"import$resultrowindex\"";
        if ($resultrow['importedsessionid'] != '') {
            echo " disabled checked";
        }
        echo ">Import</label>";
        echo "<input type=\"hidden\" name=\"previousconid$resultrowindex\" value=\"{$resultrow['previousconid']}\">";
        echo "<input type=\"hidden\" name=\"previoussessionid$resultrowindex\" value=\"{$resultrow['previoussessionid']}\"></td>";
        echo "<td><span class=\"label\">{$resultrow['trackname']}</span></td>";
        echo "<td><span class=\"label\">{$resultrow['typename']}</span></td>";
        echo "<td><span class=\"label\">{$resultrow['statusname']}</span></td>";
        echo "<td><span class=\"label label-info\">{$resultrow['previousconname']}</span></td></tr>\n";
        echo "<td colspan=5 class=\"padding2000\">" . htmlspecialchars($resultrow['progguiddesc'], ENT_NOQUOTES) . "</td></tr>\n";
    }
    echo "<input type=\"hidden\" name=\"lastrownum\" value=\"$resultrowindex\">\n";
    echo "</table><hr /><div class=\"clearfix\"><button type=submit class=\"btn btn-primary pull-right \" value=\"submitimport\">Import</button></div></form>\n";
}  // End of RenderSearchPrevSessionResults()

function ProcessImportSessions() {
    global $linki, $message, $message_error;
    if (!isset($_POST['lastrownum'])) {
        $message_error = "This page is intended to be reached from a form.  One or more required ";
        $message_error .= "Post parameters were not provided. No further processing is possible.";
        return (false);
    }
    get_name_and_email($name, $email); // populates them from session data or db as necessary
    $name = mysqli_real_escape_string($linki, $name);
    $email = mysqli_real_escape_string($linki, $email);
    $badgeid = mysqli_real_escape_string($linki, $_SESSION['badgeid']);
    $query1 = "START TRANSACTION";
    $success_rows = 0;
    for ($i = 0; $i <= $_POST['lastrownum']; $i++) {
        if (isset($_POST["import$i"])) {
            $previousconid = mysqli_real_escape_string($linki, $_POST["previousconid$i"]);
            $previoussessionid = mysqli_real_escape_string($linki, $_POST["previoussessionid$i"]);
            $query2 = <<<EOD
INSERT INTO Sessions
    (sessionid, trackid, typeid, divisionid, pubstatusid,
    languagestatusid, pubsno, title, secondtitle, pocketprogtext,
    progguiddesc, progguidhtml, persppartinfo, duration, estatten, kidscatid,
    signupreq, roomsetid, notesforpart, servicenotes, statusid,
    notesforprog, warnings, invitedguest, ts)
SELECT
    NULL sessionid, COALESCE(TC.currenttrackid, 99), PS.typeid, PS.divisionid, 2 pubstatusid,
    PS.languagestatusid, NULL pubsno, PS.title, PS.secondtitle, PS.pocketprogtext,
    PS.progguiddesc, IFNULL(PS.progguidhtml, PS.progguiddesc), PS.persppartinfo, PS.duration, PS.estatten, PS.kidscatid,
    PS.signupreq, 99 roomsetid, NULL notesforpart, NULL servicenotes, 6 statusid,
    PS.notesforprog, NULL warnings, PS.invitedguest, CURRENT_TIMESTAMP ts
FROM
    PreviousSessions PS LEFT JOIN
    TrackCompatibility TC USING (previousconid, previoustrackid)
WHERE
    previousconid=$previousconid AND
    previoussessionid=$previoussessionid
EOD;
            //echo $query2;
            $result = mysqli_query_with_error_handling($query1);
            if (!$result) {
                rollback_mysqli();
                return (false);
            }
            $result = mysqli_query_with_error_handling($query2);
            if (!$result) {
                rollback_mysqli();
                return (false);
            }
            if (($x = mysqli_affected_rows($linki)) != 1) {
                $message_error = $query2 . "There was a problem because 1 row was expected to ";
                $message_error .= "be inserted, but $x rows were actually inserted. ";
                rollback_mysqli();
                return (false);
            }
            $sessionid = mysqli_insert_id($linki);
            if ($sessionid == 0 || !$sessionid) {
                $message_error = $query2 . "Insert id not returned as expected from previous query. ";
                rollback_mysqli();
                return (false);
            }
            $query3 = "UPDATE PreviousSessions\n";
            $query3 .= "    SET importedsessionid=$sessionid WHERE\n";
            $query3 .= "        previousconid=$previousconid AND\n";
            $query3 .= "        previoussessionid=$previoussessionid\n";
            $result = mysqli_query($linki, $query3);
            if (!$result) {
                $message_error = $query3 . "Error querying database.";
                rollback_mysqli();
                return (false);
            }
            if (($x = mysqli_affected_rows($linki)) != 1) {
                $message_error = $query3 . "There was a problem because 1 row was expected to ";
                $message_error .= "be inserted, but $x rows were actually inserted. ";
                rollback_mysqli();
                return (false);
            }
            $query4 = "INSERT INTO SessionEditHistory\n";
            $query4 .= "    (sessionid, badgeid, name, email_address, timestamp, sessioneditcode, statusid, editdescription)\n";
            $query4 .= "    Values($sessionid, \"$badgeid\", \"$name\", \"$email\", CURRENT_TIMESTAMP, 6, 6, NULL)\n";
            $result = mysqli_query($linki, $query4);
            if (!$result) {
                $message_error = $query4 . "Error querying database. " . mysqli_error($linki);
                rollback_mysqli();
                return (false);
            }
            if (($x = mysqli_affected_rows($linki)) != 1) {
                $message_error = $query4 . "There was a problem because 1 row was expected to ";
                $message_error .= "be inserted, but $x rows were actually inserted. ";
                rollback_mysqli();
                return (false);
            }
            $result = mysqli_query($linki, "COMMIT");
            if (!$result) {
                $message_error = "COMMIT: Error querying database.";
                return (false);
            }
            $success_rows++;
        }
    }
    $message = "$success_rows sessions(s) imported.";
    $message_error = "";
    return (true);
} // End of ProcessImportSessions()
?>
