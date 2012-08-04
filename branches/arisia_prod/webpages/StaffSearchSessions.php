<?php
	// Guts of page (not header and footer) have been updated to the CSS refactor 1 Feb 2011
	global $participant,$message_error,$message2,$congoinfo;
	$title="Search Sessions";
	require_once('db_functions.php');
	require_once('StaffHeader.php');
	require_once('StaffFooter.php');
	require_once('StaffCommonCode.php');
	staff_header($title);
?>

<div class="row-fluid">
<FORM method=POST action="ShowSessions.php" class="well form-horizontal">
	<fieldset>
    <p class="">Session Search (shows same data as Precis View for each session):</p>
    <DIV class="control-group">
        <LABEL for="track" class="control-label">Track: </LABEL>
        <div class="controls">
          <SELECT name="track" class="span2">
              <?php populate_select_from_table("Tracks",0,"ANY",true); ?>
          </SELECT>
        </div>
    </DIV>
    <DIV class="control-group">
        <LABEL for="type" class="control-label">Type: </LABEL>
        <div class="controls">
          <SELECT name="type" class="span2">
              <?php populate_select_from_table("Types",0,"ANY",true); ?>
          </SELECT>
        </div>
    </DIV>
    <DIV class="control-group">
        <LABEL for="type" class="control-label">Status: </LABEL>
        <div class="controls">
          <SELECT name="type" class="span2">
              <?php populate_select_from_table("SessionStatuses",0,"ANY",true); ?>
          </SELECT>
        </div>
    </DIV>
    <DIV class="control-group">
        <LABEL for="sessionid" class="control-label">Session ID: </LABEL>
        <div class="controls">
          <INPUT type="text" name="sessionid" size="10" class="span1">
        <P class="help-inline">Leave blank for any</P>
        </div>
    </DIV>
    <DIV class="control-group">
        <LABEL for="divisionid" class="control-label">Division:</LABEL>
        <div class="controls">
          <SELECT name="divisionid" class="span2">
              <?php populate_select_from_table("Divisions",0,"ANY",true); ?>
          </SELECT>
        </div>
    </DIV>
    <DIV class="control-group">
        <LABEL for="searchtitle" class="control-label">Title:</LABEL></SPAN>
        <div class="controls">
          <INPUT type="text" name="searchtitle" size="25" class="span3">
          <P class="help-inline">Leave blank for any</P>
        </div>
    </DIV>
    <BUTTON type="submit" value="search" class="btn btn-primary">Search</BUTTON>
  </fieldset>
</FORM>
</div>
<?php staff_footer(); ?>
