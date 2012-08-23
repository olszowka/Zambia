<?php
participant_header($title);
if (!isset($daymap)) {
    error_log("zambia-render_my_avail: \$daymap is not set.");
    }
?>

<?php if ($message_error!="") { ?>
    <P class="errmsg"><?php echo $message_error; ?></P>
    <?php } ?>
<?php if ($message!="") { ?>
    <P class="regmsg"><?php echo $message; ?></P>
    <?php } ?>
<DIV id=constraint>
<//?php print_r($partAvail);?>
<FORM name="constrform" method=POST action="SubmitMySchedConstr.php">

    <H2>Number of program items I'm willing to participate in:</H2>
<p> Please indicate the maximum number of panels you are willing to be on.  
You may indicate a total for each day as well as an overall maximum for 
the whole con.  Please note that the tool limits you to <?php echo PREF_TTL_SESNS_LMT;?> or fewer 
total sessions and <?php echo PREF_DLY_SESNS_LMT;?> each day.  There is no need for the numbers to add up.  We'll use this 
for guidance when assigning and scheduling panels. </p>
            <DIV class="regform">
                <SPAN><LABEL for="maxprog">Preferred Total Number of Panels &nbsp;</LABEL><INPUT type="text" size=3 name="maxprog" 
                     value="<?php echo $partAvail["maxprog"];?>">&nbsp;&nbsp;</SPAN></DIV>
<?php
// Don't ask about day limits at all if only 1 day con
          if (CON_NUM_DAYS>1) {
// 1st row on page contains up to 4 days of inputs
              echo "<DIV class=\"regform\">\n";
              for ($i=1; $i<=min(4,CON_NUM_DAYS); $i++) {
                  $D=$daymap["long"][$i];
                  echo "<SPAN><LABEL for=\"maxprogday$i\">$D maximum &nbsp;</LABEL>\n";
                  $N=$partAvail["maxprogday$i"];
                  echo "    <INPUT id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=$N>&nbsp;&nbsp;&nbsp;&nbsp;</SPAN>\n";
                  }
              echo "</DIV>\n";
              }
// 2nd row on page contains up to 4 more if needed
          if (CON_NUM_DAYS>4) {
              echo "<DIV class=\"regform\">\n";
              for ($i=5; $i<=CON_NUM_DAYS; $i++) {
                  $D=$daymap["long"][$i];
                  echo "<SPAN><LABEL for=\"maxprogday$i\">$D maximum &nbsp;</LABEL>\n";
                  $N=$partAvail["maxprogday$i"];
                  echo "    <INPUT id=\"maxprogday$i\" size=3 name=\"maxprogday$i\" value=$N>&nbsp;&nbsp;</SPAN>\n";
                  }
              echo "</DIV>\n";
              }
?>
<hr>

<!-- SCHEDULE availability times -->
<H2>Times I Am Available</H2>

<p> For <?php if (CON_NUM_DAYS>1) {echo "each ";} else {echo "the ";} ?>
day you will be attending <?php echo CON_NAME; ?>, please indicate
the times when you will be available.  Entering a single time for
the whole con is fine.  Splitting a day into multiple time slots
also is fine.  Keep in mind we will be using this as guidance when
scheduling you.</p>

<table>
  <tr> <!-- row one -->
<?php if (CON_NUM_DAYS>1) { echo "<td> Start Day </td>\n";} ?> 
    <td> Start Time </td>
    <td> &nbsp; </td>
<?php if (CON_NUM_DAYS>1) { echo "<td> End Day </td>\n";} ?> 
    <td> End Time </td> 
  </tr> <!-- header row  -->
<?php
//  Notes on variables:
//  $partAvail["availstarttime_$i"], $partAvail["availendtime_$i"] are measured in GRID_SPACER increments
//     0 is unset, 1 is midnight beginning of day
    for ($i=1; $i<=AVAILABILITY_ROWS; $i++) {
        echo "  <TR> <!-- Row $i -->\n";
        if (CON_NUM_DAYS>1) {
            echo "    <TD><SELECT name=\"availstartday_$i\">\n";
            $sel = isset($partAvail["availstartday_$i"])?"":" selected";
            echo "        <OPTION value=0$sel>&nbsp;</OPTION>\n";
            for ($j=1; $j<=CON_NUM_DAYS; $j++) {
                $sel = ($partAvail["availstartday_$i"]==$j)?" selected":"";
                $day = $daymap["long"][$j];
                echo "        <OPTION value=$j $sel>$day</OPTION>\n";
                }
            echo "        </SELECT></TD>\n";
            }
	//If starttimeindex is set for this $i to the availstarttime for it, otherwise set it to -1
        $starttimeindex=(isset($partAvail["availstarttime_$i"]))?$partAvail["availstarttime_$i"]:-1;
	//If starttimeindex got set, make sure the leading 0 is in place, based on length.
	if (($starttimeindex!=-1) AND (strlen($starttimeindex) < 8)) {$starttimeindex="0".$starttimeindex;}
	//A value of '' is passed, instead of the prevous '0', because of the way strings are read,
        // for midnight anything was coming out as '0'.
        echo "    <TD><SELECT name=\"availstarttime_$i\">\n";
	echo "          <OPTION value=''></OPTION>\n";
	//Iterate across one day in GRID_SPACER increments
	for ($starttime=0; $starttime < 86400; $starttime = $starttime + GRID_SPACER) {
	  //produces HH:MM:SS
	  $indextime=gmdate('H:i:s',$starttime);
	  //produces ?H:MM[ap]m
	  $displaytime=gmdate('g:ia',$starttime);
	  //Noon and midnight fix by request
          if ($displaytime == "12:00am") {$displaytime="Midnight";}
          if ($displaytime == "12:00pm") {$displaytime="Noon";}
	  //Build the rest of the select, with the starttimeindex selected, if it exists
	  echo "          <OPTION value=$indextime";
	  if ($indextime==$starttimeindex) {echo " selected";}
	  echo ">$displaytime</OPTION>\n";
	}
        echo "        </SELECT></TD>\n";
        echo "    <TD> Until </TD>\n";
        if (CON_NUM_DAYS>1) {
            echo "    <TD><SELECT name=\"availendday_$i\">\n";
             $sel = isset($partAvail["availendday_$i"])?"":" selected";
            echo "        <OPTION value=0$sel>&nbsp;</OPTION>\n";
            for ($j=1; $j<=CON_NUM_DAYS; $j++) {
                $sel = ($partAvail["availendday_$i"]==$j)?" selected":"";
                $day = $daymap["long"][$j];
                echo "        <OPTION value=$j $sel>$day</OPTION>\n";
                }
            echo "        </SELECT></TD>\n";
            }
	//If endtimeindex is set for this $i to the availendtime for it, otherwise set it to -1
        $endtimeindex=(isset($partAvail["availendtime_$i"]))?$partAvail["availendtime_$i"]:-1;
	//If endtimeindex got set, make sure the leading 0 is in place, based on length.
	if (($endtimeindex!=-1) AND (strlen($endtimeindex) < 8)) {$endtimeindex="0".$endtimeindex;}
	//A value of '' is passed, instead of the prevous '0', because of the way strings are read,
        // for midnight anything was coming out as '0'.
        echo "    <TD><SELECT name=\"availendtime_$i\">\n";
	echo "          <OPTION value=''></OPTION>\n";
	//Iterate across one day in GRID_SPACER increments
	for ($endtime=0; $endtime < 86400; $endtime = $endtime + GRID_SPACER) {
	  //produces HH:MM:SS
	  $indextime=gmdate('H:i:s',$endtime);
	  //produces ?H:MM[ap]m
	  $displaytime=gmdate('g:ia',$endtime);
	  //Noon and midnight fix by request
          if ($displaytime == "12:00am") {$displaytime="Midnight";}
          if ($displaytime == "12:00pm") {$displaytime="Noon";}
	  //Build the rest of the select, with the endtimeindex selected, if it exists
	  echo "          <OPTION value=$indextime";
	  if ($indextime==$endtimeindex) {echo " selected";}
	  echo ">$displaytime</OPTION>\n";
	}
        echo "        </SELECT></TD>\n";
        echo "    </TR>\n";
        }
?>
</table>

<hr>

<DIV id="conflict">
    <DIV class="sectionheader">Please don't schedule me for a time that conflicts with:</DIV>

    <DIV class="entries">
    <TEXTAREA name="preventconflict" rows=3 cols=72><?php
        echo htmlspecialchars($partAvail["preventconflict"],ENT_NOQUOTES);?></TEXTAREA>
        </DIV>
    </DIV>

<?php
    if (MY_AVAIL_KIDS===TRUE) {
        echo "<P>We are looking for a rough count of children attending FastTrack (programming for children";
        echo " ages 6-13).  Please indicate how many children will be attending with you:\n";
        $x=$partAvail["numkidsfasttrack"];
        echo "<INPUT id=\"kids\" size=2 name=\"numkidsfasttrack\" value=\"$x\">\n</P>";
        }
    ?>

<DIV id="otherconstraints">
    <DIV class="sectionheader">Other constraints or conflicts that we should know about?</DIV>
    <DIV class="entries">
        <TEXTAREA name="otherconstraints" rows=3 cols=72><?php
            echo htmlspecialchars($partAvail["otherconstraints"],ENT_NOQUOTES);?></TEXTAREA>
        </DIV>
    </DIV>

<DIV class="submit">
    <DIV id="submit"><BUTTON class="SubmitButton" type=submit value="Save">Save</BUTTON></DIV>
    </DIV>
</FORM>
</DIV>
<?php participant_footer(); ?>
