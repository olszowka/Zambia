<?php
// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
    global $participant, $message_error, $message2, $congoinfo, $title;
    $title = "Search Sessions";
    require_once('StaffCommonCode.php');
    staff_header($title);
?>

<div class="row-fluid">
    <form method=POST action="ShowSessions.php" class="well form-horizontal">
        <fieldset>
            <p class="">Session Search (shows same data as Precis View for each session):</p>
<?php
    if (TRACK_TAG_USAGE !== "TAG_ONLY") {
?>
            <div class="control-group">
                <label for="track" class="control-label">Track: </label>
                <div class="controls">
                    <select id="track" name="track" class="span2">
                        <?php populate_select_from_table("Tracks",0,"ANY",true); ?>
                    </select>
                </div>
            </div>
<?php
    } else {
?>
           <input id="track" type="hidden" name="track" Value="0"/>
<?php
    }
?>
            <div class="control-group">
                <label for="type" class="control-label">Type: </label>
                <div class="controls">
                  <select id="type" name="type" class="span2">
                      <?php populate_select_from_table("Types",0,"ANY",true); ?>
                  </select>
                </div>
            </div>
            <div class="control-group">
                <label for="status" class="control-label">Status: </label>
                <div class="controls">
                    <select id="status" name="status" class="span2">
                        <?php populate_select_from_table("SessionStatuses",0,"ANY",true); ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="sessionid" class="control-label">Session ID: </label>
                <div class="controls">
                    <input id="sessionid" type="text" name="sessionid" size="10" class="span1" />
                <p class="help-inline">Leave blank for any</p>
                </div>
            </div>
            <div class="control-group">
                <label for="divisionid" class="control-label">Division:</label>
                <div class="controls">
                    <select id="divisionid" name="divisionid" class="span2">
                        <?php populate_select_from_table("Divisions",0,"ANY",true); ?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label for="searchtitle" class="control-label">Title:</label>
                <div class="controls">
                    <input type="text" id="searchtitle" name="searchtitle" size="25" class="span3" />
                    <p class="help-inline">Leave blank for any</p>
                </div>
            </div>
<?php
    if (TRACK_TAG_USAGE !== "TRACK_ONLY") {
?>
            <div class="control-group control-group-horizontal">
                <label for="tags" class="control-label">Tags:</label>
                <div class="controls">
                    <select id="tags" name="tags[]" class="span2" multiple="multiple">
                        <?php populate_multiselect_from_table("Tags",""); ?>
                    </select>
                    <label>
                        <input type="radio" id="tagmatch1" name="tagmatch" value="any"/>
                        <span>Match Any Selected</span>
                    </label>
                    <label>
                        <input type="radio" id="tagmatch2" name="tagmatch" value="all"/>
                        <span>Match All Selected</span>
                    </label>
                    <label>Select no tags to match everything</label>
                </div>
            </div>
<?php
    } else {
?>
            <input id="tags" type="hidden" name="tags[]" value="" />
<?php
    }
?>
            <button type="submit" value="search" class="btn btn-primary">Search</button>
        </fieldset>
    </form>
</div>
<?php
    staff_footer(); 
?>
