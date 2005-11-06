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
<FORM name="constrform" method=POST action="SubmitMySchedConstr.php">

    <H2>Number of program items I'm willing to participate in:</H2>
<p> Please indicate the number of panels you are willing to be on each day and let us know the 
total you are interested in as well.  Please note that the tool limits you to 10 or fewer in 
each slot.  There is no need for the numbers to add up. We'll use this for guidance when assigning
and scheduling panels. </p>
<table>
    <tr>
        <td>Friday </td>
        <td>
           <INPUT id=fridaymaxprog size=3 name=fridaymaxprog value="<?php echo $partAvail["fridaymaxprog"]?>">
        </td>
        <td>Saturday </td>
        <td>
           <INPUT  id=saturdaymaxprog size=3 name=saturdaymaxprog value="<?php echo $partAvail["saturdaymaxprog"]?>">
        </td>
        <td>Sunday </td>
        <td>
           <INPUT id=sundaymaxprog size=3 name=sundaymaxprog value="<?php echo $partAvail["sundaymaxprog"]?>">
        </td>
        <td>Total </td>
        <td>
           <INPUT d=maxprog size=3 name=maxprog value="<?php echo $partAvail["maxprog"]?>">
        </td>
    </tr>
</table>

<hr>

<!-- SCHEDULE availability times -->
<H2>Times I Am Available</H2>
<p> For each day you will be in attendance at Arisia, please indicate the times when 
you will be available as a program panelist.  Entering a single time for the whole 
con is fine.  Splitting a day into multiple time slots is fine.  Keep in mind we will 
be using this for scheduling your panels.</p>

<table>
  <tr> <!-- row one -->
    <td> Start Day </DIV> 
    <td> Start Time </DIV>
    <td> &nbsp; </DIV>
    <td> End Day </DIV> 
    <td> End Time </DIV> 
  </tr> <!-- row one -->
  <tr> <!-- row two -->  
    <td> <!-- row two, cell one -->
      <SELECT name=availstartday_1> 
        <?php $dayindex=(isset($availability[0]['startday']))?$availability[0]['startday']+1:0; ?>
        <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
        <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
        <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
        <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
      </SELECT>
    </td> 
    <td> 
      <SELECT name=availstarttime_1>
        <?php
          $timeindex=(isset($availability[0]['starttime']))?$availability[0]['starttime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td> 
    <td> Until </td>
    <td>
      <SELECT name=availendday_1>
        <?php $dayindex=(isset($availability[0]['endday']))?$availability[0]['endday']+1:0; ?>
        <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
        <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
        <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
        <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
      </SELECT>
    </td>
    <td>
      <SELECT name=availendtime_1>
        <?php
          $timeindex=(isset($availability[0]['endtime']))?$availability[0]['endtime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td>
  </tr> <!-- Row 1 -->
  <tr> <!-- Row 2 -->
    <td>
        <SELECT name=availstartday_2> 
          <?php $dayindex=(isset($availability[1]['startday']))?$availability[1]['startday']+1:0; ?>
          <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
          <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
          <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
          <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
        </SELECT>
    </td>
    <td>
        <SELECT name=availstarttime_2>
          <?php
            $timeindex=(isset($availability[1]['starttime']))?$availability[1]['starttime']+1:0;
            populate_select_from_table("Times", $timeindex, "", true);
          ?>
          </SELECT>
    </td>
    <td> Until </td>
    <td>
         <SELECT name=availendday_2>
            <?php $dayindex=(isset($availability[1]['endday']))?$availability[1]['endday']+1:0; ?>
            <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
            <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
            <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
            <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
         </SELECT>
    </td>
    <td>
         <SELECT name=availendtime_2>
            <?php
               $timeindex=(isset($availability[1]['endtime']))?$availability[1]['endtime']+1:0;
               populate_select_from_table("Times", $timeindex, "", true);
               ?>
            </SELECT>
    </td>
  </tr> <!-- Row 2 -->
  <tr> <!-- Row 3 -->
    <td>
        <SELECT name=availstartday_3> 
          <?php $dayindex=(isset($availability[2]['startday']))?$availability[2]['startday']+1:0; ?>
          <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
          <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
          <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
          <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
        </SELECT>
    </td>
    <td>
        <SELECT name=availstarttime_3>
          <?php
            $timeindex=(isset($availability[2]['starttime']))?$availability[2]['starttime']+1:0;
            populate_select_from_table("Times", $timeindex, "", true);
          ?>
          </SELECT>
    </td>
    <td> Until </td>
    <td>
         <SELECT name=availendday_3>
            <?php $dayindex=(isset($availability[2]['endday']))?$availability[2]['endday']+1:0; ?>
            <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
            <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
            <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
            <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
            </SELECT>
    </td>
    <td>
         <SELECT name=availendtime_3>
            <?php
               $timeindex=(isset($availability[2]['endtime']))?$availability[2]['endtime']+1:0;
               populate_select_from_table("Times", $timeindex, "", true);
               ?>
            </SELECT>
    </td>
  </tr> <!-- Row 3 -->
  <tr> <!-- Row 4 -->
    <td>
        <SELECT name=availstartday_4> 
          <?php $dayindex=(isset($availability[3]['startday']))?$availability[3]['startday']+1:0; ?>
          <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
          <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
          <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
          <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
        </SELECT>
    </td>
    <td>
      <SELECT name=availstarttime_4>
        <?php
          $timeindex=(isset($availability[3]['starttime']))?$availability[3]['starttime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td>
    <td> Until </td>
    <td>
      <SELECT name=availendday_4>
        <?php $dayindex=(isset($availability[3]['endday']))?$availability[3]['endday']+1:0; ?>
        <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
        <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
        <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
        <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
      </SELECT>
    </td>
    <td>
      <SELECT name=availendtime_4>
        <?php
          $timeindex=(isset($availability[3]['endtime']))?$availability[3]['endtime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td>
  </tr> <!-- Row 4 -->
  <tr> <!-- Row 5 -->
    <td>
        <SELECT name=availstartday_5> 
          <?php $dayindex=(isset($availability[4]['startday']))?$availability[4]['startday']+1:0; ?>
          <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
          <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
          <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
          <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
        </SELECT>
    </td>
    <td>
      <SELECT name=availstarttime_5>
        <?php
          $timeindex=(isset($availability[4]['starttime']))?$availability[4]['starttime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td>
    <td> Until </td>
    <td>
      <SELECT name=availendday_5>
        <?php $dayindex=(isset($availability[4]['endday']))?$availability[4]['endday']+1:0; ?>
        <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
        <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
        <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
        <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
      </SELECT>
    </td>
    <td>
      <SELECT name=availendtime_5>
        <?php
          $timeindex=(isset($availability[4]['endtime']))?$availability[4]['endtime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td>
  </tr> <!-- Row 5 -->
  <tr> <!-- Row 6 -->
    <td>
        <SELECT name=availstartday_6> 
          <?php $dayindex=(isset($availability[5]['startday']))?$availability[5]['startday']+1:0; ?>
          <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
          <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
          <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
          <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
        </SELECT>
    </td>
    <td>
      <SELECT name=availstarttime_6>
        <?php
          $timeindex=(isset($availability[5]['starttime']))?$availability[5]['starttime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td>
    <td> Until </td>
    <td>
      <SELECT name=availendday_6>
        <?php $dayindex=(isset($availability[5]['endday']))?$availability[5]['endday']+1:0; ?>
        <OPTION value=0 <?php if ($dayindex==0) {echo"selected ";}?> >&nbsp;</OPTION>
        <OPTION value=1 <?php if ($dayindex==1) {echo"selected ";}?> >Friday</OPTION>
        <OPTION value=2 <?php if ($dayindex==2) {echo"selected ";}?> >Saturday</OPTION>
        <OPTION value=3 <?php if ($dayindex==3) {echo"selected ";}?> >Sunday</OPTION>
      </SELECT>
    </td>
    <td>
      <SELECT name=availendtime_6>
        <?php
          $timeindex=(isset($availability[5]['endtime']))?$availability[5]['endtime']+1:0;
          populate_select_from_table("Times", $timeindex, "", true);
        ?>
      </SELECT>
    </td>
  </tr> <!-- Row 6 -->
</table>

<hr>

<DIV id=conflict>
    <DIV class=sectionheader>Please do not schedule me to conflict with:</DIV>

    <DIV class=entries>
    <TEXTAREA name=preventconflict rows=3 cols=72><?php
        echo htmlspecialchars($partAvail["preventconflict"],ENT_NOQUOTES);?></TEXTAREA>
        </DIV>
    </DIV>


<DIV id=otherconstraints>
    <DIV class=sectionheader>Other constraints or conflicts that we should know about?</DIV>
    <DIV class=entries>
        <TEXTAREA name=otherconstraints rows=3 cols=72><?php
            echo htmlspecialchars($partAvail["otherconstraints"],ENT_NOQUOTES);?></TEXTAREA>
        </DIV>
    </DIV>

<p> We are looking for a rough count of children attending FastTrack (programming for children ages 6-13).  Please indicate how many children will be attending with you:
        <INPUT id=kids size=2 name=numkidsfasttrack value="<?php echo $partAvail["numkidsfasttrack"]; ?>" >
        </p>

<DIV class="submit">
    <DIV id="submit"><BUTTON type=submit value="Save">Save</BUTTON></DIV>
    </DIV>
</FORM>
</DIV>
<?php participant_footer(); ?>
