<?php
// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
    global $participant, $message_error, $message2, $congoinfo, $title;
    $title = "Search Sessions";
    require_once('StaffCommonCode.php');
    staff_header($title, true);
?>
</div>
<div class="container">

<div class="card mt-2">
    <div class="card-header">
        <h5 class="mb-0">Session Search</h5>
        <small class="text-muted">(shows same data as Precis View for each session)</small>
    </div>
    <div class="card-body">
    <form method=POST action="ShowSessions.php">
        <fieldset>
            <div class="row">
                <div class="col-md-6">
<?php
    if (TRACK_TAG_USAGE !== "TAG_ONLY") {
?>
            <div class="form-group">
                <label for="track" class="control-label">Track: </label>
                <select class="form-control" id="track" name="track" class="span2">
                    <?php populate_select_from_table("Tracks",0,"ANY",true); ?>
                </select>
            </div>
<?php
    } else {
?>
           <input id="track" type="hidden" name="track" Value="0"/>
<?php
    }
?>
            <div class="form-group">
                <label for="type">Type: </label>
                <select class="form-control" id="type" name="type">
                    <?php populate_select_from_table("Types",0,"ANY",true); ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status: </label>
                <select class="form-control" id="status" name="status">
                    <?php populate_select_from_table("SessionStatuses",0,"ANY",true); ?>
                </select>
            </div>
            <div class="form-group">
                <label for="sessionid">Session ID: </label>
                <input class="form-control" id="sessionid" type="text" name="sessionid" size="10" />
                <p class="help-inline text-muted">Leave blank for any</p>
            </div>
            <div class="form-group">
                <label for="divisionid">Division:</label>
                <select class="form-control" id="divisionid" name="divisionid">
                    <?php populate_select_from_table("Divisions",0,"ANY",true); ?>
                </select>
            </div>
            <div class="form-group">
                <label for="title">Title:</label>
                <!-- "searchtitle" happens to be the id of the field in the nav bar -->
                <input class="form-control" type="text" id="title" name="searchtitle" size="25" />
                <p class="help-inline text-muted">Leave blank for any</p>
            </div>
<?php
    if (TRACK_TAG_USAGE !== "TRACK_ONLY") {
?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="tags" class="control-label">Tags:</label>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <select class="form-control" id="tags" name="tags[]" multiple="multiple">
                        <?php populate_multiselect_from_table("Tags",""); ?>
                    </select>
                    <div class="text-muted">Select no tags to match everything</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <div>
                    <label>
                        <input class="form-check-input" type="radio" id="tagmatch1" name="tagmatch" value="any"/>
                        <span>Match Any Selected</span>
                    </label>
                    </div>
                    <div>
                    <label>
                        <input class="form-check-input" type="radio" id="tagmatch2" name="tagmatch" value="all"/>
                        <span>Match All Selected</span>
                    </label>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label for="hashtag">Hashtag: </label>
                <input class="form-control" id="hashtag" type="text" name="hashtag" size="10" />
                <p><small class="help-inline text-muted">Leave blank for any</small></p>
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
</div>
</div>
</div>
</div>
<div class="container-fluid">
<?php
    staff_footer(); 
?>
