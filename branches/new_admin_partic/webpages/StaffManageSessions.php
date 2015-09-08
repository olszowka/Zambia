<?php
	// Guts of page (not header and footer) have been updated to the CSS refactor 1 Feb 2011
	global $participant,$message_error,$message2,$congoinfo;
	$title="Staff - Manage Sessions";
	require_once('db_functions.php');
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	require_once('StaffCommonCode.php');
	staff_header($title);
?>

<FORM method=POST action="ShowSessions.php" class="newform">
	<DIV class="newoverviewtext">On this page you will find the online tools for managing panels, events, films, anime 
		presentations, videos., etc. (which is why we refer to them with the target neutral term "sessions".)</DIV>
	<HR>
	<DIV class="newcommandlink"><A HREF="CreateSession.php">Create a New Session</A></DIV>
		<DIV class="newcommanddescription">Used for creating new sessions.  They are intially created in status "edit me". 
		   	Once created, a second persion edits for content (and uniqueness). This person promotes the session to status 
			"Brainstorm". A third set of eyes does a basic grammar and spelling edit and promotes the session to status
			"Vetted". At that time it is ready for general viewing by prospective panelists.</DIV> 
	<DIV class="newcommandlink"><A HREF="ViewSessionCountReport.php">View Counts of Sessions</A></DIV> 
		<DIV class="newcommanddescription">A quick report broken down by status and then by track to give an idea of
		 	where we are.</DIV> 
	<DIV class="newcommandlink"><A HREF="ViewAllSessions.php">View All Sessions:</A></DIV>
		<DIV class="newcommanddescription">A tabular report on all sessions organized by track.  Key information on each session
		   is visible from the top level and a link takes you down into the details for any session.</DIV> 
	<DIV class="newcommandlink"><A HREF="ViewPrecis.php?showlinks=0">Precis View</A>&nbsp;
	       (<A HREF="ViewPrecis.php?showlinks=1">Precis View With Links</A>)</DIV>
		<DIV class="newcommanddescription">Since the purpose of the Precis is to get participants to signup to be on various
		    panels or to help with various events, this report contains sessions where that status is "brainstorm" or "vetted".
		   Note that sessions marked "invited guest only" are not included in the precis (regardless of status).</DIV>
	<DIV class="newcommandlink"><A HREF="StaffSearchPreviousSessions.php">Import Sessions</A></DIV>
		<DIV class="newcommanddescription">Search the databases of previous cons for sessions to import to the current con.</DIV>
	<BR>
	<DIV class="newoverviewtext">Session Search (shows same data as Precis View for each session):</DIV>

    <DIV class="newformdiv">
        <SPAN class="newformlabelspan"><LABEL for="track" class="newformlabel">Track:</LABEL></SPAN>
        <SPAN class="newformselectspan"><SELECT name="track" class="newformselect">
            <?php populate_select_from_table("Tracks",0,"ANY",true); ?>
            </SELECT></SPAN>
        </DIV>
    <DIV class="newformdiv">
        <SPAN class="newformlabelspan"><LABEL for="type" class="newformlabel">Type:</LABEL></SPAN>
        <SPAN class="newformselectspan"><SELECT name="type" class="newformselect">
            <?php populate_select_from_table("Types",0,"ANY",true); ?>
            </SELECT></SPAN>
        </DIV>
    <DIV class="newformdiv">
        <SPAN class="newformlabelspan"><LABEL for="status" class="newformlabel">Status:</LABEL></SPAN>
        <SPAN class="newformselectspan"><SELECT name="status" class="newformselect">
            <?php populate_select_from_table("SessionStatuses",0,"ANY",true); ?>
            </SELECT></SPAN>
        </DIV>
    <DIV class="newformdiv">
        <SPAN class="newformlabelspan"><LABEL for="sessionid" class="newformlabel">Session ID:</LABEL></SPAN>
        <SPAN class="newforminputspan"><INPUT type="text" name="sessionid" size="10" class="newforminput"></SPAN>
        <SPAN class="newformnotespan">(Leave blank for any)</SPAN>
        </DIV>
    <DIV class="newformdiv">
        <SPAN class="newformlabelspan"><LABEL for="divisionid" class="newformlabel">Division:</LABEL></SPAN>
        <SPAN class="newformselectspan"><SELECT name="divisionid" class="newformselect">
            <?php populate_select_from_table("Divisions",0,"ANY",true); ?>
            </SELECT></SPAN>
        </DIV>
	<DIV class="newformdiv">
        <SPAN class="newformlabelspan"><LABEL for="searchtitle" class="newformlabel">Title:</LABEL></SPAN>
        <SPAN class="newforminputspan"><INPUT type="text" name="searchtitle" size="25" class="newforminput"></SPAN>
        <SPAN class="newformnotespan">(Leave blank for any)</SPAN>
        </DIV>
	<DIV class="newformbuttondiv">
		<BUTTON type="submit" value="search" class="newformbutton">Search</BUTTON>
		</DIV>
	</FORM>
<?php staff_footer(); ?>
