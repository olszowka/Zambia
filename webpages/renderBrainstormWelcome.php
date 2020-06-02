<?php
// Copyright (c) 2011-2020 Peter Olszowka. All rights reserved. See copyright document for more details.
   global $participant,$message,$message_error,$message2,$congoinfo,$title;
   $title="Brainstorm View";
   require_once('BrainstormCommonCode.php');
   brainstorm_header($title);
?>

<?php if ($message_error!="") { ?>
	<p class="alert alert-error"><?php echo $message_error; ?></p>
	<?php } ?>
<?php if ($message!="") { ?>
	<p class="alert alert-success"><?php echo $message; ?></p>
	<?php } ?>
<?php
    if (empty(DEFAULT_USER_PASSWORD)) {
        $chpw = false;
    } else {
        $chpw = hash_equals($participant["password"], md5(DEFAULT_USER_PASSWORD));
    }
    $chint=($participant["interested"]==0);
    if (may_I('BrainstormSubmit')) { ?>
<p> Here you can submit new suggestions or look at existing ideas for 
    panels, events, movies, films, presentations, speeches, concerts, etc.</p>
<p> As suggestions come in and we read through them, we will rework 
them, combine similar ideas into a single item, 
split large ones into pieces that will fit in a timeslot, etc.    
    Please expect the suggestions you submit to evolve over time.</p>
<p> Also, please note that we always have more suggestions than are 
physically possible with the space and time we have, so not 
    everything will make it.   We do try to save good ideas for future conventions.</p>
<ul> 
    <li> <a href="BrainstormSearchSession.php">Search </a> for similar ideas or get inspiration.</li>
    <li> Email <?php echo "<a href=\"mailto:".BRAINSTORM_EMAIL."\">".BRAINSTORM_EMAIL."</a> ";?> to suggest modifications on existing suggestions.</li>
    <li> <a href="BrainstormCreateSession.php">Enter a new suggestion. </a></li>
    <li> See the list of <a href="BrainstormReportAll.php">All</a> suggestions (we've seen some and not seen others).</li>
    <li> See the list of <a href="BrainstormReportUnseen.php"> New </a> suggestions that have been entered recently (may not be fit for young eyes, we haven't seen these yet).</li>
    <li> See the list of <a href="BrainstormReportReviewed.php">Reviewed </a>suggestions we are currently working through.</li>
    <li> See the list of <a href="BrainstormReportLikely.php">Likely to Occur</a> suggestions we are or will allow participants to sign up for.</li>
    <li> See the list of <a href="BrainstormReportScheduled.php">Scheduled</a> suggestions.  These are very likely to happen at con.</li>
    <li> Email <?php echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> ";?> to volunteer to help process these ideas.</li>
<?php if(may_I('Participant')) { ?>
    <li> <a href="welcome.php">Return To Participant View</a></li>
<?php } ?>
</ul>
<?php
      } // end of if brainstorming permitted
   else { // Brainstorming not permitted ?>
<p> We are not accepting suggestions at this time for <?php echo CON_NAME;?>.</p>
<p> You may still use the "Search Sessions" tab to view the sessions which have been selected and to read their precis.  Note,
 many of these sessions will still not be scheduled if there is too little participant interest or if a suitable location and time
 slot is not available. </p> 
<?php } //end of if brainstorming not permitted ?>
<p> Thank you and we look forward to reading your suggestions.</p>

<p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </p>
<?php brainstorm_footer(); ?>
