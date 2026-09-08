<?php
// Copyright (c) 2005-2026 Peter Olszowka. All rights reserved. See copyright document for more details.
// $timesXML array defined on global scope
participant_header($title, false, 'Normal', 'bs5');
?>

<div class="container-xl">
<?php if (!empty($message_error)) { ?>
    <p class="alert alert-danger"><?php echo $message_error; ?></p>
<?php } ?>
<?php if (!empty($message)) { ?>
    <p class="alert alert-success"><?php echo $message; ?></p>
<?php } ?>
<div id="constraint">
<form name="constrform" method="POST" action="SubmitMySchedConstr.php">

    <h4 class="alert-info text-center mt-3">Number of Program Items I'm Willing to Participate In</h4>
    <p> Please indicate the maximum number of sessions you are willing to be on.
        You may indicate a total for each day as well as an overall maximum for
        the whole con. Please note that Zambia limits you to <?php echo PREF_TTL_SESNS_LMT; ?> or fewer
        total sessions and <?php echo PREF_DLY_SESNS_LMT; ?> each day. There is no need for the numbers to add up. We'll
        use this
        for guidance when assigning and scheduling sessions. </p>
    <div class="row mt-3">
        <div class="col-md-8 col-lg-7 col-xl-6">
            <label class="form-label" for="maxprog">Preferred total number of sessions:</label>
            <input id="maxprog" class="form-control" style="width: 5em" type="text" size=3 name="maxprog" value="<?php echo $partAvail["maxprog"]; ?>">
        </div>
        <?php
        // Don't ask about day limits at all if only 1 day con
        if (CON_NUM_DAYS > 1) {
            for ($i = 1; $i <= CON_NUM_DAYS; $i++) {
                $D = longDayNameFromInt($i);
                $N = isset($partAvail["maxprogday$i"]) ? $partAvail["maxprogday$i"] : '';
                echo "<div class=\"col-md-8 col-lg-7 col-xl-6\">\n";
                echo "<label class=\"form-label\" for=\"maxprogday$i\">$D maximum:</label>\n";
                echo "<input class=\"form-control\" style=\"width: 5em\" id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=\"$N\">\n";
                echo "</div>\n";
            }
        }
        ?>
    </div>
    <hr>

    <!-- SCHEDULE availability times -->
    <div class="row mt-3">
        <div class="col-md-12 col-lg-10 offset-md-11 offset-lg-12 offset-xl-10">
            <h4>Times I Am Available</h4>
        </div>
    </div>
    <?php echo fetchCustomText("note_before_time_slots"); ?>
    <div class="row d-none d-md-flex fw-bold mb-1"> <!-- header row, hidden on narrow screens where fields stack -->
        <?php if (CON_NUM_DAYS > 1) { ?>
        <div class="col-md-7 col-xl-6 ps-4">Start Day</div>
        <?php } ?>
        <div class="col-md-7 col-xl-6 ps-4">Start Time</div>
        <div class="col-md-3 col-lg-2">&nbsp;</div>
        <?php if (CON_NUM_DAYS > 1) { ?>
        <div class="col-md-7 col-xl-6 ps-4">End Day</div>
        <?php } ?>
        <div class="col-md-7 col-xl-6 ps-4">End Time</div>
    </div>
    <?php
    $xsl = new DomDocument;
    $xsl->load('xsl/ScheduleConstrSelect.xsl');
    $xslt = new XsltProcessor();
    $xslt->importStylesheet($xsl);
    //  Notes on variables:
    //  $partAvail["availstarttime_$i"], $partAvail["availendtime_$i"] are indexes into table Times
    //     0 is unset
    for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
        echo "  <div class=\"row gy-2 align-items-center mb-2\"> <!-- Row $i -->\n";
        if (CON_NUM_DAYS > 1) {
            echo "    <div class=\"col-md col-md-7 col-xl-6\"><select class=\"form-select\" aria-label=\"Start day\" name=\"availstartday_$i\">\n";
            $sel = isset($partAvail["availstartday_$i"]) ? "" : " selected";
            echo "        <option value=0$sel>&nbsp;</option>\n";
            for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                $sel = (isset($partAvail["availstartday_$i"]) && $partAvail["availstartday_$i"] == $j) ? " selected" : "";
                $day = longDayNameFromInt($j);
                echo "        <option value=$j $sel>$day</option>\n";
            }
            echo "        </select></div>\n";
        }
        echo "    <div class=\"col-md-7 col-xl-6\"><select class=\"form-select\" aria-label=\"Start time\" name=\"availstarttime_$i\">\n";
        $timeindex = (isset($partAvail["availstarttime_$i"])) ? $partAvail["availstarttime_$i"] : 0;
        // use XSLT to render <option> tags
        $timesXML["variablesNode"]->setAttribute("option", "start");
        $timesXML["variablesNode"]->setAttribute("index", $timeindex);
        echo($xslt->transformToXML($timesXML["XML"]));
        // end XSLT
        echo "        </select></div>\n";
        echo "    <div class=\"col-md-3 col-lg-2\">Until</div>\n";
        if (CON_NUM_DAYS > 1) {
            echo "    <div class=\"col-md-7 col-xl-6\"><select class=\"form-select\" aria-label=\"End day\" name=\"availendday_$i\">\n";
            $sel = isset($partAvail["availendday_$i"]) ? "" : " selected";
            echo "        <option value=0$sel>&nbsp;</option>\n";
            for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                $sel = (isset($partAvail["availendday_$i"]) && $partAvail["availendday_$i"] == $j) ? " selected" : "";
                $day = longDayNameFromInt($j);
                echo "        <option value=$j $sel>$day</option>\n";
            }
            echo "        </select></div>\n";
        }
        echo "    <div class=\"col-md-7 col-xl-6\"><select class=\"form-select\" aria-label=\"End time\" name=\"availendtime_$i\">\n";
        $timeindex = (isset($partAvail["availendtime_$i"])) ? $partAvail["availendtime_$i"] : 0;
        // use XSLT to render <option> tags
        $timesXML["variablesNode"]->setAttribute("option", "end");
        $timesXML["variablesNode"]->setAttribute("index", $timeindex);
        echo($xslt->transformToXML($timesXML["XML"]));
        // end XSLT
        echo "        </select></div>\n";
        echo "  </div>\n";
    }
    ?>
    <?php echo fetchCustomText("note_after_times"); ?>
    <hr class="mt-4">

    <div class="row mt-3">
        <div class="col-md-18 input-container">
            <label class="form-label" for="preventconflict">Please don't schedule me for a session that conflicts with:</label>
            <textarea class="form-control" name="preventconflict" id="preventconflict" rows=3 cols=72 maxlength=255><?php
                echo htmlspecialchars($partAvail["preventconflict"], ENT_NOQUOTES); ?></textarea>
            <div class="form-text">Please limit to 255 characters.</div>
        </div>

        <div class="col-md-18 input-container">
            <label class="form-label" for="otherconstraints">Other constraints or conflicts that we should know about?</label>
            <textarea class="form-control" name="otherconstraints" id="otherconstraints" rows=3 cols=72 maxlength=255><?php
                echo htmlspecialchars($partAvail["otherconstraints"], ENT_NOQUOTES); ?></textarea>
            <div class="form-text">Please limit to 255 characters.</div>
        </div>
    </div>

    <?php
    if (MY_AVAIL_KIDS === TRUE) {
        $x = $partAvail["numkidsfasttrack"];
        echo "<div class=\"row mt-3\">\n";
        echo "  <div class=\"col-36\"><p>We are looking for a rough count of children attending FastTrack (programming for children";
        echo " ages 6-13).</p></div>\n";
        echo "  <div class=\"col-auto\">\n";
        echo "    <label class=\"form-label\" for=\"kids\">Please indicate how many children will be attending with you:</label>\n";
        echo "    <input class=\"form-control\" style=\"width: 5em\" id=\"kids\" size=2 name=\"numkidsfasttrack\" value=\"$x\">\n";
        echo "  </div>\n";
        echo "</div>\n";
    }
    ?>

    <div class="mt-3 mb-3">
        <button class="btn btn-primary" type="submit" value="Save">Save</button>
    </div>
</form>
</div>
</div>
<?php participant_footer(); ?>
