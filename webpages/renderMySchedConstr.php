<?php
// Copyright (c) 2005-2023 Peter Olszowka. All rights reserved. See copyright document for more details.
// $timesXML array defined on global scope
participant_header($title);
?>

<?php if (!empty($message_error)) { ?>
    <p class="alert alert-error"><?php echo $message_error; ?></p>
<?php } ?>
<?php if (!empty($message)) { ?>
    <p class="alert alert-success"><?php echo $message; ?></p>
<?php } ?>
<div id="constraint">
<form class="form-inline" name="constrform" method=POST action="SubmitMySchedConstr.php">

    <h4 class="alert-info center">Number of Program Items I'm Willing to Participate In</h4>
    <p> Please indicate the maximum number of sessions you are willing to be on.
        You may indicate a total for each day as well as an overall maximum for
        the whole con. Please note that Zambia limits you to <?php echo PREF_TTL_SESNS_LMT; ?> or fewer
        total sessions and <?php echo PREF_DLY_SESNS_LMT; ?> each day. There is no need for the numbers to add up. We'll
        use this
        for guidance when assigning and scheduling sessions. </p>
    <div class="row-fluid">
        <div class="control-group">
            <div class="controls">
                <label class="control-label" for="maxprog">preferred total number of sessions:</label>
                <input id="maxprog" class="span1" type="text" size=3 name="maxprog" value="<?php echo $partAvail["maxprog"]; ?>">
            </div>
            <?php
            // Don't ask about day limits at all if only 1 day con
            if (CON_NUM_DAYS > 1) {
// 1st row on page contains up to 4 days of inputs
                echo "<div class=\"control-group padded\">\n";
                echo "<div class=\"controls span12 padded\">\n";
                for ($i = 1; $i <= min(4, CON_NUM_DAYS); $i++) {
                    $D = longDayNameFromInt($i);
                    echo "<label class=\"control-label\" for=\"maxprogday$i\">$D maximum:</label>\n";
                    $N = isset($partAvail["maxprogday$i"]) ? $partAvail["maxprogday$i"] : '';
                    echo "<input class=\"span1\" id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=$N>\n";
                }
            }
            // 2nd row on page contains up to 4 more if needed
            if (CON_NUM_DAYS > 4) {
                for ($i = 5; $i <= CON_NUM_DAYS; $i++) {
                    $D = longDayNameFromInt($i);
                    echo "<label class=\"control-label\" for=\"maxprogday$i\">$D maximum:</label>\n";
                    $N = isset($partAvail["maxprogday$i"]) ? $partAvail["maxprogday$i"] : '';
                    echo "    <input class=\"span1\" id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=\"$N\">\n";
                }
            }
            echo "</div>\n";
            echo "</div>\n";
            ?>
        </div>
    </div>
    <hr>

    <!-- SCHEDULE availability times -->
    <h4 class="alert-info center">Times I Am Available</H4>
    <?php echo fetchCustomText("note_before_time_slots"); ?>
    <table class="table table-condensed">
        <tr> <!-- row one -->
            <?php if (CON_NUM_DAYS > 1) {
                echo "<td>Start Day</td>\n";
            } ?>
            <td>Start Time</td>
            <td> &nbsp;</td>
            <?php if (CON_NUM_DAYS > 1) {
                echo "<td>End Day</td>\n";
            } ?>
            <td>End Time</td>
        </tr> <!-- header row  -->
        <?php
        $xsl = new DomDocument;
        $xsl->load('xsl/ScheduleConstrSelect.xsl');
        $xslt = new XsltProcessor();
        $xslt->importStylesheet($xsl);
        //  Notes on variables:
        //  $partAvail["availstarttime_$i"], $partAvail["availendtime_$i"] are indexes into table Times
        //     0 is unset
        for ($i = 1; $i <= AVAILABILITY_ROWS; $i++) {
            echo "  <tr> <!-- Row $i -->\n";
            if (CON_NUM_DAYS > 1) {
                echo "    <td><select name=\"availstartday_$i\">\n";
                $sel = isset($partAvail["availstartday_$i"]) ? "" : " selected";
                echo "        <option value=0$sel>&nbsp;</option>\n";
                for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                    $sel = (isset($partAvail["availstartday_$i"]) && $partAvail["availstartday_$i"] == $j) ? " selected" : "";
                    $day = longDayNameFromInt($j);
                    echo "        <option value=$j $sel>$day</option>\n";
                }
                echo "        </select></td>\n";
            }
            echo "    <td><select name=\"availstarttime_$i\">\n";
            $timeindex = (isset($partAvail["availstarttime_$i"])) ? $partAvail["availstarttime_$i"] : 0;
            // use XSLT to render <option> tags
            $timesXML["variablesNode"]->setAttribute("option", "start");
            $timesXML["variablesNode"]->setAttribute("index", $timeindex);
            echo($xslt->transformToXML($timesXML["XML"]));
            // end XSLT
            echo "        </select></td>\n";
            echo "    <td> Until </td>\n";
            if (CON_NUM_DAYS > 1) {
                echo "    <td><select name=\"availendday_$i\">\n";
                $sel = isset($partAvail["availendday_$i"]) ? "" : " selected";
                echo "        <option value=0$sel>&nbsp;</option>\n";
                for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                    $sel = (isset($partAvail["availendday_$i"]) && $partAvail["availendday_$i"] == $j) ? " selected" : "";
                    $day = longDayNameFromInt($j);
                    echo "        <option value=$j $sel>$day</option>\n";
                }
                echo "        </select></td>\n";
            }
            echo "    <td><select name=\"availendtime_$i\">\n";
            $timeindex = (isset($partAvail["availendtime_$i"])) ? $partAvail["availendtime_$i"] : 0;
            // use XSLT to render <option> tags
            $timesXML["variablesNode"]->setAttribute("option", "end");
            $timesXML["variablesNode"]->setAttribute("index", $timeindex);
            echo($xslt->transformToXML($timesXML["XML"]));
            // end XSLT
            echo "        </select></td>\n";
            echo "    </tr>\n";
        }
        ?>
    </table>
    <?php echo fetchCustomText("note_after_time_slots"); ?>
    <hr style="margin-top:5px">

    <div class="row-fluid">
        <div class="span6 input-container">
            <label for="preventconflict">Please don't schedule me for a session that conflicts with:</label>
            <textarea class="span12" name="preventconflict" id="preventconflict" rows=3 cols=72><?php
                echo htmlspecialchars($partAvail["preventconflict"], ENT_NOQUOTES); ?></textarea>
            <div class="input-error-message">Please limit to 255 characters.</div>
        </div>

        <div class="span6 input-container">
            <label for="otherconstraints">Other constraints or conflicts that we should know about?</label>
            <textarea class="span12" name="otherconstraints" id="otherconstraints" rows=3 cols=72><?php
                echo htmlspecialchars($partAvail["otherconstraints"], ENT_NOQUOTES); ?></textarea>
            <div class="input-error-message">Please limit to 255 characters.</div>
        </div>
    </div>

    <?php
    if (MY_AVAIL_KIDS === TRUE) {
        echo("<div class=\"row-fluid padded\">\n");
        echo "<P>We are looking for a rough count of children attending FastTrack (programming for children";
        echo " ages 6-13).</P>\n";
        $x = $partAvail["numkidsfasttrack"];
        echo("  <div class=\"span12\">\n");
        echo("    <label class=\"control-label\">Please indicate how many children will be attending with you:</label>");
        echo "    <input class=\"span1\" id=\"kids\" size=2 name=\"numkidsfasttrack\" value=\"$x\"></label>\n";
        echo "  </div>\n";
        echo "</div>\n";
    }
    ?>

    <div class="padded">
        <button class="btn btn-primary" type=submit value="Save">Save</button>
    </div>
</form>
</div>
<?php participant_footer(); ?>
