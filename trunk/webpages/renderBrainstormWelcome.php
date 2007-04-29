<?php
   global $participant,$message,$message_error,$message2,$congoinfo;
   $title="Welcome";
   require_once('data_functions.php');
   require_once('BrainstormCommonCode.php');
   require_once('BrainstormHeader.php');
   require_once('BrainstormFooter.php');
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
?>
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
<li> Search for similar ideas or get inspiration.
<li> Email <?php echo "<a href=\"mailto:".BRAINSTORM_EMAIL."\">".BRAINSTORM_EMAIL."</a> "?> to suggest modifications on existing suggestion.
<li> Enter a new suggestion 
<li> See the <a href="BrainstormReportUnseen.php"> list of suggestions that have been entered recently </a> (may not be fit for young eyes, we haven't see these yet). 
<li> See the <a href="BrainstormReportVetted.php">list of suggestions we are currently sorting through</a> (we have see these).
<li> See the <a href="BrainstormReportAll.php">list of all suggestions</a> (we've seen some and not see others).
<li> Email <?php echo "<a href=\"mailto:".PROGRAM_EMAIL."\">".PROGRAM_EMAIL."</a> "?> to volunteer to help process these ideas. 
</ul>

<p> Thank you and we look forward to reading your suggestions.

<p>- <a href="mailto: <?php echo PROGRAM_EMAIL; ?>"><?php echo PROGRAM_EMAIL; ?> </a> </P>
<?php brainstorm_footer(); ?>
