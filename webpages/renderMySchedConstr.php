<?php
require_once('db_functions.php');
require_once('ParticipantHeader.php');
require_once('ParticipantFooter.php');
$firsttime=false;
if (isLoggedIn($firsttime)===false) {
    exit(0);
    }

participant_header($title);
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
<p> For each day you will be attending <?php echo CON_NAME; ?>, please 
indicate the times when you will be available as a program panelist.  
Entering a single time for the whole con is fine.  Splitting a day into 
multiple time slots also is fine.  Keep in mind we will be using this as 
guidance when scheduling your panels.</p>

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
//  $partAvail["availstarttime_$i"], $partAvail["availendtime_$i"] are measured in 1-24 whole hours
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
        echo "    <TD><SELECT name=\"availstarttime_$i\">\n";
        $timeindex=(isset($partAvail["availstarttime_$i"]))?$partAvail["availstarttime_$i"]:0;
        populate_select_from_table("Times", $timeindex, "", true);
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
        echo "    <TD><SELECT name=\"availendtime_$i\">\n";
        $timeindex=(isset($partAvail["availendtime_$i"]))?$partAvail["availendtime_$i"]:0;
        populate_select_from_table("Times", $timeindex, "", true);
        echo "        </SELECT></TD>\n";
        echo "    </TR>\n";
        }
?>
</table>

<hr>

<DIV id="conflict">
    <DIV class="sectionheader">Please don't schedule me for a panel that conflicts with:</DIV>

    <DIV class="entries">
    <TEXTAREA name="preventconflict" rows=3 cols=72><?php
        echo htmlspecialchars($partAvail["preventconflict"],ENT_NOQUOTES);?></TEXTAREA>
        </DIV>
    </DIV>

<p>
    We are looking for a rough count of children attending FastTrack (programming for children ages 6-13).  Please indicate how many children will be attending with you:
    
     <INPUT id="kids" size=2 name="numkidsfasttrack" value="<?php echo $partAvail["numkidsfasttrack"]; ?>" >
</p>

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
