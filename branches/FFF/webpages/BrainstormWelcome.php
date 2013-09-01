<?php
   global $participant,$message,$message_error,$message2,$congoinfo;
   $title="Class/Presenter Submission";
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
  //  $mayi=(in_array('BrainstormSubmit',$_SESSION['permission_set']));
  //echo "<P>mayi ".$mayi.":<PRE>".Print_r($_SESSION['permission_set'])."</PRE></P>\n";
    if (may_I('BrainstormSubmit')) {
      if (file_exists("../Local/Verbiage/BrainstormWelcome_0")) {
	echo file_get_contents("../Local/Verbiage/BrainstormWelcome_0");
      } else { ?>
<p> Here you can submit new suggestions or look at existing ideas for 
panels, events, movies, films, presentations, speeches, concerts, etc.  
<p> As suggestions come in and we read through them, we will rework 
them, combine similar ideas into a single item, 
split large ones into pieces that will fit in an hour, etc.    
Please expect the suggestions you submit to evolve over time.  
<p> Also, please note that we always have more suggestions than are 
physically possible with the space and time we have, so not 
everything will make it.   We do save good ideas for future conventions. 
<UL> 
  <LI> <A HREF="BrainstormSearchSession.php">Search </A> for similar ideas or get inspiration.
  <LI> Email <?php echo "<A HREF=\"mailto:".BRAINSTORM_EMAIL."\">".BRAINSTORM_EMAIL."</A> ";?> to suggest modifications on existing suggestion.
  <LI> <A HREF="BrainstormCreateSession.php">Enter a new suggestion.</A>
  <LI> See the list of <A HREF="BrainstormReport.php?status=all">All</A> suggestions (we have seen some and not see others).
  <LI> See the list of <A HREF="BrainstormReport.php?status=unseen">New</A> suggestions that have been entered recently (may not be fit for young eyes, we have not see these yet). 
  <LI> See the list of <A HREF="BrainstormReport.php?status=reviewed">Reviewed</A> suggestions we are currently working through.
  <LI> See the list of <A HREF="BrainstormReport.php?status=likely">Likely to Occur</A> suggestions we are or will allow participants to sign up for. 
  <LI> See the list of <A HREF="BrainstormReport.php?status=scheduled">Scheduled</A> suggestions.  These are very likely to happen at con.
  <LI> Email <?php echo "<A HREF=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</A> ";?> to volunteer to help process these ideas.
<?php 
      if(may_I('Participant')) {
           echo '<li> <a href="welcome.php">Return To Participant View</a>';
           }
      echo "</ul>\n";
      } // end of local words
    } // end of if brainstorming permitted
   else { // Brainstorming not permitted
     if (file_exists("../Local/Verbiage/BrainstormWelcome_1")) {
       echo file_get_contents("../Local/Verbiage/BrainstormWelcome_1");
     } else { ?>
<P> We are not accepting suggestions at this time for <?php echo CON_NAME;?>.
<P> You may still use the "Search Sessions" tab to view the sessions which have been selected and to read their precis.  Note,
 many of these sessions will still not be scheduled if there is too little participant interest or if a suitable location and time
 slot is not available. </P> 
<?php 
     } // end of local words
   } //end of if brainstorming not permitted ?>
<p> Thank you and we look forward to reading your suggestions.

<p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </P>
<?php brainstorm_footer(); ?>
