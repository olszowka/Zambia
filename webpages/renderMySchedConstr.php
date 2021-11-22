<?php
// Copyright (c) 2005-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
// $timesXML array defined on global scope
participant_header($title, false, 'Normal', true);
?>

<?php if (!empty($message_error)) { ?>
    <p class="alert alert-danger mt-2"><?php echo $message_error; ?></p>
<?php } ?>
<?php if (!empty($message)) { ?>
    <p class="alert alert-success mt-2"><?php echo $message; ?></p>
<?php } ?>

<div id="constraint">

    <form name="constrform" method=POST action="SubmitMySchedConstr.php">

        <div class="row mt-2">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header"><h5>Maximum Number of Sessions</h5></div>
                    <div class="card-body">
                        <p> Please indicate the maximum number of sessions you are willing to be on.
                            You may indicate a total for each day as well as an overall maximum for
                            the whole con.</p>
                        <p><small>Please note that Zambia limits you to <?php echo PREF_TTL_SESNS_LMT; ?> or fewer
                            total sessions and <?php echo PREF_DLY_SESNS_LMT; ?> each day. There is no need for the numbers to add up. We'll
                            use this for guidance when assigning and scheduling sessions.</small></p>
                        <div class="form-group row">                            
                            <label class="col-md-7 offset-md-1 col-form-label" for="maxprog">Preferred total number of sessions:</label>
                            <div class="col-md-3">
                                <input class="form-control" type="text" size=3 name="maxprog" value="<?php echo $partAvail["maxprog"]; ?>">
                            </div>
                        </div>
                        <div>
                            <?php
                            // Don't ask about day limits at all if only 1 day con
                            if (CON_NUM_DAYS > 1) {
                                for ($i = 1; $i <= CON_NUM_DAYS; $i++) {
                                    echo "<div class=\"form-group row\">\n";
                                    $D = longDayNameFromInt($i);
                                    echo "<label class=\"col-md-7 offset-md-1 col-form-label\" for=\"maxprogday$i\">$D maximum:</label>\n";
                                    $N = isset($partAvail["maxprogday$i"]) ? $partAvail["maxprogday$i"] : '';
                                    echo "<div class=\"col-md-3\"><input type=\"text\" class=\"form-control\" id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=$N></div>\n";
                                    echo "</div>\n";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>

    <!-- SCHEDULE availability times -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5>Times I Am Available</h5>
            </div>
            <div class="card-body">
                <p> For each day you will be attending <?php echo CON_NAME; ?>, please
                    indicate the times when you will be available as a program panelist.
                    Entering a single time for the whole con is fine. Splitting a day into
                    multiple time slots also is fine. Change all items in a row to blank to delete the row. 
                    Keep in mind we will be using this only as guidance when scheduling your sessions.</p>

    <table class="table table-sm">
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
                echo "    <td><select class=\"form-control\" name=\"availstartday_$i\">\n";
                $sel = isset($partAvail["availstartday_$i"]) ? "" : " selected";
                echo "        <option value=0$sel>&nbsp;</option>\n";
                for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                    $sel = (isset($partAvail["availstartday_$i"]) && $partAvail["availstartday_$i"] == $j) ? " selected" : "";
                    $day = longDayNameFromInt($j);
                    echo "        <option value=$j $sel>$day</option>\n";
                }
                echo "        </select></td>\n";
            }
            echo "    <td><select class=\"form-control\" name=\"availstarttime_$i\">\n";
            $timeindex = (isset($partAvail["availstarttime_$i"])) ? $partAvail["availstarttime_$i"] : 0;
            // use XSLT to render <option> tags
            $timesXML["variablesNode"]->setAttribute("option", "start");
            $timesXML["variablesNode"]->setAttribute("index", $timeindex);
            echo($xslt->transformToXML($timesXML["XML"]));
            // end XSLT
            echo "        </select></td>\n";
            echo "    <td class=\"text-center\"> until </td>\n";
            if (CON_NUM_DAYS > 1) {
                echo "    <td><select class=\"form-control\" name=\"availendday_$i\">\n";
                $sel = isset($partAvail["availendday_$i"]) ? "" : " selected";
                echo "        <option value=0$sel>&nbsp;</option>\n";
                for ($j = 1; $j <= CON_NUM_DAYS; $j++) {
                    $sel = (isset($partAvail["availendday_$i"]) && $partAvail["availendday_$i"] == $j) ? " selected" : "";
                    $day = longDayNameFromInt($j);
                    echo "        <option value=$j $sel>$day</option>\n";
                }
                echo "        </select></td>\n";
            }
            echo "    <td><select class=\"form-control\" name=\"availendtime_$i\">\n";
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
    <?php echo fetchCustomText("note_after_times"); ?>
    </div>
    </div>
    </div>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6 form-group">
                    <label>Please don't schedule me for a session that conflicts with:</label>
                    <textarea class="form-control" name="preventconflict" rows=3><?php
                        echo htmlspecialchars($partAvail["preventconflict"], ENT_NOQUOTES); ?></textarea>
                </div>
                <div class="col-lg-6 form-group">
                    <label>Other constraints or conflicts that we should know about?</label>
                    <textarea class="form-control" name="otherconstraints" rows=3><?php
                        echo htmlspecialchars($partAvail["otherconstraints"], ENT_NOQUOTES); ?></textarea>
                </div>
            </div>

            <?php
            if (MY_AVAIL_KIDS === TRUE) {
                echo("<div class=\"row mt-2\">\n");
                echo "<P>We are looking for a rough count of children attending FastTrack (programming for children";
                echo " ages 6-13).</P>\n";
                $x = $partAvail["numkidsfasttrack"];
                echo("  <div class=\"form-group\">\n");
                echo("    <label for=\"kids\">Please indicate how many children will be attending with you:</label>");
                echo "    <input class=\"form-control\" id=\"kids\" size=2 name=\"numkidsfasttrack\" value=\"$x\">\n";
                echo "  </div>\n";
                echo "</div>\n";
            }
            ?>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 text-center">
            <button class="btn btn-primary" type=submit value="Save">Save</button>
        </div>
    </div>
</form>
</div>
<?php participant_footer(); ?>
