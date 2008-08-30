<?php
   global $participant,$message,$message_error,$message2,$congoinfo;
   $title="Brainstorm View";
   require_once('BrainstormCommonCode.php');
   brainstorm_header($title);
?>

<?php if ($message_error!="") { ?>
	<P class="errmsg"><?php echo $message_error; ?></P>
	<?php } ?>
<?php if ($message!="") { ?>
	<P class="regmsg"><?php echo $message; ?></P>
	<?php } ?>
<?php
    $chpw=($participant["password"]=="4cb9c8a8048fd02294477fcb1a41191a");
    $chint=($participant["interested"]==0);
    if (may_I('BrainstormSubmit')) { ?>
<p> Here you can submit new suggestions or look at existing ideas for 
panels, events, movies, films, presentations, speeches, concerts, etc.  
<p> As suggestions come in and we read through them, we will rework 
them, combine similar ideas into a single item, 
split large ones into pieces that will fit in an hour, etc.    
Please expect the suggestions you submit to evolve over time.  
<p> Also, please note that we always have more suggestions than are 
physically possible with the space and time we have, so not 
everything will make it.   We do save good ideas for future conventions. 
<ul> 
<li> <a href="BrainstormSearchSession.php">Search </a> for similar ideas or get inspiration.
<li> Email <?php echo "<a href=\"mailto:".BRAINSTORM_EMAIL."\">".BRAINSTORM_EMAIL."</a> ";?> to suggest modifications on existing suggestion.
<li> <a href="BrainstormCreateSession.php">Enter a new suggestion. </a>
<li> See the list of <a href="BrainstormReportAll.php">All</a> suggestions (we've seen some and not see others).
<li> See the list of <a href="BrainstormReportUnseen.php"> New </a> suggestions that have been entered recently (may not be fit for young eyes, we haven't see these yet). 
<li> See the list of <a href="BrainstormReportReviewed.php">Reviewed </a>suggestions we are currently working through.
<li> See the list of <a href="BrainstormReportLikely.php">Likely to Occur</a> suggestions we are currently matching participants to. 
<li> See the list of <a href="BrainstormReportLikely.php">Scheduled</a> suggestions.  These are very likely to happen at con.
<li> Email <?php echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> ";?> to volunteer to help process these ideas.
<?php 
      if(may_I('Participant')) {
           echo '<li> <a href="welcome.php">Return To Participant View</a>';
           }
      echo "</ul>\n";
      } // end of if brainstorming permitted
   else { // Brainstorming not permitted ?>
<P> We are not accepting suggestions at this time for <?php echo CON_NAME;?>.
<P> You may still use the "Search Sessions" tab to view the sessions which have been selected and to read their precis.  Note,
 many of these sessions will still not be scheduled if there is too little participant interest or if a suitable location and time
 slot is not available. </P> 
<?php } //end of if brainstorming not permitted ?>
<p> Thank you and we look forward to reading your suggestions.

<p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </P>
<?php brainstorm_footer(); ?>
