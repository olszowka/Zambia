<?php
participant_header($title);
if (!isset($daymap)) {
    error_log("zambia-render_my_avail: \$daymap is not set.");
    }
?>

<?php if ($message_error!="") { ?>
    <p class="alert alert-error"><?php echo $message_error; ?></p>
    <?php } ?>
<?php if ($message!="") { ?>
    <p class="alert alert-success"><?php echo $message; ?></p>
    <?php } ?>
<div id=constraint>
<//?php print_r($partAvail);?>
<form class="form-inline" name="constrform" method=POST action="SubmitMySchedConstr.php">

<h4 class="alert-info center">Number of Program Items I'm Willing to Participate In</h4>
<p> Please indicate the maximum number of sessions you are willing to be on.  
You may indicate a total for each day as well as an overall maximum for 
the whole con.  Please note that Zambia limits you to <?php echo PREF_TTL_SESNS_LMT;?> or fewer 
total sessions and <?php echo PREF_DLY_SESNS_LMT;?> each day.  There is no need for the numbers to add up.  We'll use this 
for guidance when assigning and scheduling sessions. </p>
<div class="row-fluid">
  <div class="control-group">
    <div class="controls">
      <label class="control-label" for="maxprog">preferred total number of sessions:</label>
      <input class="span1" type="text" size=3 name="maxprog" value="<?php echo $partAvail["maxprog"];?>">
    </div>
<?php
// Don't ask about day limits at all if only 1 day con
          if (CON_NUM_DAYS>1) {
// 1st row on page contains up to 4 days of inputs
              echo "<div class=\"control-group padded\">\n";
              echo "<div class=\"controls span12 padded\">\n";
              for ($i=1; $i<=min(4,CON_NUM_DAYS); $i++) {
                  $D=$daymap["long"][$i];
                  echo "<label class=\"control-label\" for=\"maxprogday$i\">$D maximum:</label>\n";
                  $N=$partAvail["maxprogday$i"];
                  echo "<input class=\"span1\" id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=$N>\n";
                  }
              }
// 2nd row on page contains up to 4 more if needed
          if (CON_NUM_DAYS>4) {
              for ($i=5; $i<=CON_NUM_DAYS; $i++) {
                  $D=$daymap["long"][$i];
                  echo "<label class=\"control-label\" for=\"maxprogday$i\">$D maximum:</label>\n";
                  $N=$partAvail["maxprogday$i"];
                  echo "    <input class=\"span1\" id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=$N>\n";
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
<p> For each day you will be attending <?php echo CON_NAME; ?>, please 
indicate the times when you will be available as a program panelist.  
Entering a single time for the whole con is fine.  Splitting a day into 
multiple time slots also is fine.  Keep in mind we will be using this as 
guidance when scheduling your sessions.</p>

<table class="table table-condensed">
  <tr> <!-- row one -->
<?php if (CON_NUM_DAYS>1) { echo "<td>Start Day</td>\n";} ?> 
    <td>Start Time</td>
    <td> &nbsp; </td>
<?php if (CON_NUM_DAYS>1) { echo "<td>End Day</td>\n";} ?> 
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
    for ($i=1; $i<=AVAILABILITY_ROWS; $i++) {
        echo "  <tr> <!-- Row $i -->\n";
        if (CON_NUM_DAYS>1) {
            echo "    <td><select xclass=\"span2\" name=\"availstartday_$i\">\n";
            $sel = isset($partAvail["availstartday_$i"])?"":" selected";
            echo "        <option value=0$sel>&nbsp;</option>\n";
            for ($j=1; $j<=CON_NUM_DAYS; $j++) {
                $sel = ($partAvail["availstartday_$i"]==$j)?" selected":"";
                $day = $daymap["long"][$j];
                echo "        <option value=$j $sel>$day</option>\n";
                }
            echo "        </select></td>\n";
            }
        echo "    <td><select xclass=\"span2\" name=\"availstarttime_$i\">\n";
        $timeindex=(isset($partAvail["availstarttime_$i"]))?$partAvail["availstarttime_$i"]:0;
		// use XSLT to render <option> tags
		$variablesNode->setAttribute("option","start");
		$variablesNode->setAttribute("index",$timeindex);
		echo ($xslt->transformToXML($timesXML));
		// end XSLT
        echo "        </select></td>\n";
        echo "    <td> Until </td>\n";
        if (CON_NUM_DAYS>1) {
            echo "    <td><select xclass=\"span2\" name=\"availendday_$i\">\n";
             $sel = isset($partAvail["availendday_$i"])?"":" selected";
            echo "        <option value=0$sel>&nbsp;</option>\n";
            for ($j=1; $j<=CON_NUM_DAYS; $j++) {
                $sel = ($partAvail["availendday_$i"]==$j)?" selected":"";
                $day = $daymap["long"][$j];
                echo "        <option value=$j $sel>$day</option>\n";
                }
            echo "        </select></td>\n";
            }
        echo "    <td><select xclass=\"span2\" name=\"availendtime_$i\">\n";
        $timeindex=(isset($partAvail["availendtime_$i"]))?$partAvail["availendtime_$i"]:0;
		// use XSLT to render <option> tags
		$variablesNode->setAttribute("option","end");
		$variablesNode->setAttribute("index",$timeindex);
		echo ($xslt->transformToXML($timesXML));
		// end XSLT
        echo "        </select></td>\n";
        echo "    </tr>\n";
        }
?>
</table>
<?php showCustomText("<div>","note_after_times","</div>"); ?>
<hr style="margin-top:5px">

<div class="row-fluid">
  <div class="span6">
    <label>Please don't schedule me for a session that conflicts with:</label>
    <textarea class="span12" name="preventconflict" rows=3 cols=72><?php
        echo htmlspecialchars($partAvail["preventconflict"],ENT_NOQUOTES);?></textarea>
  </div>

  <div class="span6">
    <label>Other constraints or conflicts that we should know about?</label>
    <textarea class="span12" name="otherconstraints" rows=3 cols=72><?php
        echo htmlspecialchars($partAvail["otherconstraints"],ENT_NOQUOTES);?></textarea>
  </div>
</div>

<?php
    if (MY_AVAIL_KIDS===TRUE) {
        echo("<div class=\"row-fluid padded\">\n");
        echo "<P>We are looking for a rough count of children attending FastTrack (programming for children";
        echo " ages 6-13).</P>\n";
        $x=$partAvail["numkidsfasttrack"];
        echo("  <div class=\"span12\">\n");
        echo("    <label class=\"control-label\">Please indicate how many children will be attending with you:</label>");
        echo "    <INPUT class=\"span1\" id=\"kids\" size=2 name=\"numkidsfasttrack\" value=\"$x\"></label>\n";
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
