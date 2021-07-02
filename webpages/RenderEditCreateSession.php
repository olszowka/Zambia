<?php
// Copyright (c) 2011-2021 Peter Olszowka. All rights reserved. See copyright document for more details.
    // This function will output the page with the form to add or create a session
    // Variables
    //     action: "create" or "edit"
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderEditCreateSession ($action, $session, $message1, $message2) {
    global $name, $email, $debug, $title;
    require_once("StaffHeader.php");
    require_once("StaffFooter.php");
    if ($action === "create") {
        $title = "Create New Session";
    } elseif ($action === "edit") {
        $title = "Edit Session";
    } else {
        exit();
    }
    staff_header($title);
    
    // still inside function RenderAddCreateSession
    if (strlen($message1) > 0) {
        echo "<p id=\"message1\" class=\"alert\">".$message1."</p>\n";
    }
    if (strlen($message2) > 0) {
        echo "<p id=\"message2\" class=\"alert alert-error\">".$message2."</p>\n";
        exit(); // If there is a message2, then there is a fatal error.
    }
    if (isset($debug)) {
        echo $debug."<br>\n";
    }  
?>
<div class="row-fluid">
    <form name="sessform" class="form-inline form-more-whitespace" method="POST" action="SubmitEditCreateSession.php">
        <input type="hidden" name="name" value="<?php echo htmlspecialchars($name,ENT_COMPAT);?>" />
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email,ENT_COMPAT);?>" />
        <!-- The pubno field is no longer used on the form, but the code expects it.-->
        <input type="hidden" name="pubno" value="<?php echo htmlspecialchars($session["pubno"],ENT_COMPAT);?>" />
        <div id="buttonBox" class="clearfix">
            <div class="pull-right">
                <button class="btn" type=reset value="reset">Reset</button>
                <button class="btn btn-primary" type=submit value="save" onclick="mysubmit()">Save</button>
            </div>
        </div>
        <div class="row-fluid">
            <div class="control-group">
                <label class="control-label" for="sessionid">Session #: </label>
                <input id="sessionid" type="text" class="span1" size=4 name="sessionid" disabled readonly value="<?php echo htmlspecialchars($session["sessionid"],ENT_COMPAT);?>" />
                <label class="control-label" for="divisionid">Division: </label>
                <select name="divisionid" class="span2">
                    <?php populate_select_from_table("Divisions", $session["divisionid"], "SELECT", FALSE); ?>
                </select>
<?php
    if (TRACK_TAG_USAGE !== "TAG_ONLY") {
?>
                <label class="control-label" for="track">Track: </label>
                <select name="track" class="span2">
                    <?php populate_select_from_table("Tracks", $session["track"], "SELECT", FALSE); ?>
                </select>
<?php
    } else {
?>
                <input type="hidden" name="track" value="<?php echo DEFAULT_TAG_ONLY_TRACK?>"/>
<?php
    }
?>
                <label class="control-label" for="type">Type: </label>
                <select name="type" class="span2">
                    <?php populate_select_from_table("Types", $session["type"], "SELECT", FALSE); ?>
                </select>
                <label class="control-label" for="pubstatusid">Pub. Status: </label>
                <select name="pubstatusid" class="span2">
                    <?php populate_select_from_table("PubStatuses", $session["pubstatusid"], "SELECT", FALSE); ?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="title">Title:</label>
            <input type="text" class="span4" size="50" name="title" value="<?php echo htmlspecialchars($session["title"],ENT_COMPAT);?>" />&nbsp;&nbsp;
            <input class="checkbox adjust" type="checkbox" value="invguest" id="invguest" <?php if ($session["invguest"]) {echo " checked ";} ?> name="invguest" />
            <label class="checkbox inline" for="invguest"> Invited Guests Only</label>&nbsp;&nbsp;&nbsp;
            <input class="checkbox adjust" type="checkbox" value="signup" id="signup" <?php if ($session["signup"]) {echo " checked ";} ?> name="signup" />
            <label class="checkbox inline" for="signup"> Signup Required</label>
            <label class="control-label span4 pull-right" for="kids"> Kids:
                <select name="kids" class="span6">
                    <?php populate_select_from_table("KidsCategories", $session["kids"], "SELECT", FALSE); ?>
                </select>
            </label>
        </div>
<?php
    if (BILINGUAL === TRUE) {
?>
        <div class="control-group">
            <label for="secondtitle"><?php echo SECOND_TITLE_CAPTION;?></label>
            <input type="text" size="50" class="span4" name="secondtitle" value="<?php echo htmlspecialchars($session["secondtitle"],ENT_COMPAT);?>" />
            <label for="languagestatusid">Session Language: </label>
            <select class="span2" name="languagestatusid">
                <?php populate_select_from_table("LanguageStatuses", $session["languagestatusid"], "SELECT", FALSE);?>
            </select>
        </div>
<?php
    } else {
?>
        <input type="hidden" name="secondtitle" value="<?php echo htmlspecialchars($session["secondtitle"],ENT_COMPAT);?>" />
        <input type="hidden" name="languagestatusid" value="<?php echo htmlspecialchars($session["languagestatusid"],ENT_COMPAT);?>" />
<?php
    }
        // The pocketprogtext field is no longer used on the form, but the code expects it.
?>
        <input type="hidden" name="pocketprogtext" value="<?php echo htmlspecialchars($session["pocketprogtext"],ENT_COMPAT);?>" />
        <div class="row-fluid clearfix">
            <div class="control-group">
                <label class="control-label"  for="atten">Est. Atten.:</label>
                <input type="number" class="span2" size="3" name="atten" value="<?php echo htmlspecialchars($session["atten"],ENT_COMPAT);?>" />
                <label class="control-label"  for="duration">Duration:</label>
                <input type="text" class="span1" size="5" name="duration" value="<?php echo htmlspecialchars($session["duration"],ENT_COMPAT);?>" />
                <label class="control-label"  for="roomset">Room Set: </label>
                <select name="roomset" class="span2">
                    <?php populate_select_from_table("RoomSets", $session["roomset"], "SELECT", FALSE); ?>
                </select>
                <label class="control-label"  for="status">Status:</label>
                <select name="status" class="span2">
                    <?php populate_select_from_table("SessionStatuses", $session["status"], "", FALSE); ?>
                </select>
                <label class="control-label"  for="hashtag">Hashtag:</label>
                <input type="text" class="span2" size="20" name="hashtag" id="hashtag" value="<?php echo htmlspecialchars($session["hashtag"],ENT_COMPAT);?>" />
            </div>
        </div>
        <div class="row-fluid">
            <div class="span6">
                <label class="control-label" for="progguiddesc">Description:</label>
                <textarea class="span12 textlabelarea"
                    rows="4" cols="70" name="progguiddesc"><?php echo htmlspecialchars($session["progguiddesc"],ENT_NOQUOTES);?></textarea>
            </div>
            <div class="span6">
                <label class="dense" for="persppartinfo">Prospective Participant Info:</label>
                <textarea class="span12 textlabelarea"
                          rows="4" cols="70" name="persppartinfo"><?php echo htmlspecialchars($session["persppartinfo"],ENT_NOQUOTES);?></textarea>
            </div>
        </div>
<?php
    if (BILINGUAL === TRUE) {
?>
        <div class="row-fluid">
            <div class="span6">
                <label class="control-label vert-sep vert-sep-above" for="pocketprogtext"><?php echo SECOND_DESCRIPTION_CAPTION;?>: </label>
                <textarea class="span12 textlabelarea"
                    rows="4" cols="70" name="pocketprogtext"><?php echo htmlspecialchars($session["pocketprogtext"],ENT_NOQUOTES);?></textarea>
            </div>
        </div>
<?php
    } else {
                // The pocketprogtext field is no longer used on the form, but the code expects it.
?>
        <input type="hidden" name="pocketprogtext" value="<?php echo htmlspecialchars($session["pocketprogtext"],ENT_COMPAT);?>" />
<?php
    }
?>
        <div class="row-fluid vert-sep vert-sep-above">
            <div class="span5"> <!-- Features Box; -->
                <label>Required Room Features:</label>
                <div class="borderBox">
                    <div class="clearfix">
                        <label for="featsrc" class="pull-left">Possible Features:</label>
                        <label for="featdest[]" class="pull-right">Selected Features:</label>
                    </div>
                    <div class="clearfix">
                        <select class="span5" style="float: left;" id="featsrc" name="featsrc" size=6 multiple>
                            <?php populate_multisource_from_table("Features", $session["featdest"]); ?>
                        </select>
                        <div class="span2">
                            <button class="btn" onclick="fadditems(document.sessform.featsrc,document.sessform.featdest)"
                                name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</button>
                            <button class="btn" onclick="fdropitems(document.sessform.featsrc,document.sessform.featdest)"
                                name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</button>
                        </div>
                        <select class="span5" style="float: left;" id="featdest" name="featdest[]" size=6 multiple >
                            <?php populate_multidest_from_table("Features", $session["featdest"]); ?>
                        </select>
                    </div>
                </div>
            </div> <!-- Features -->
            <div class="span5" style="float: left;"> <!-- Services Box; -->
                <label>Required Room Services:</label>
                <div class="borderBox">
                    <div class="clearfix">
                        <label for="servsrc" class="pull-left">Possible Services:</label>
                        <label for="servdest[]" class="pull-right">Selected Services:</label>
                    </div>
                    <div class="clearfix">
                        <select class="span5" style="float: left;" id="servsrc" name="servsrc" size=6 multiple>
                            <?php populate_multisource_from_table("Services", $session["servdest"]); ?>
                        </select>
                        <div class="span2">
                            <button class="btn" onclick="fadditems(document.sessform.servsrc,document.sessform.servdest)"
                                name="additems" value="additems" type="button">&nbsp;&rarr;&nbsp;</button>
                            <button  class="btn" onclick="fdropitems(document.sessform.servsrc,document.sessform.servdest)"
                                name="dropitems" value="dropitems" type="button">&nbsp;&larr;&nbsp;</button>
                        </div>
                        <select class="span5" style="float: left;" id="servdest" name="servdest[]" size=6 multiple >
                            <?php populate_multidest_from_table("Services", $session["servdest"]); ?>
                        </select>
                    </div>
                </div>
            </div> <!-- Services -->
<?php
    if (TRACK_TAG_USAGE !== "TRACK_ONLY") {
?>
            <div class="span2" style="float: left;"> 
                <label class="control-label" for="tagdest">Tags:
                    <select class="span12" id="tagdest" name="tagdest[]" multiple>
                        <?php populate_multiselect_from_table("Tags", $session["tagdest"]); ?>
                    </select>
                </label>
            </div>
<?php
    } else {
?>
            <input type="hidden" name="tagdest[]" value="" />
<?php
    }
?>
        </div>
        <hr class="nospace" />
        <div class="row-fluid form-vertical">
            <div class="span4">
                <label class="control-label" for="notesforpart">Notes for Participants:</label>
                <textarea class="textlabelarea span12"
                    rows="3" cols="70" name="notesforpart" ><?php echo htmlspecialchars($session["notesforpart"],ENT_NOQUOTES);?></textarea>
            </div>
            <div class="span4">
                <label class="control-label" for="servnotes">Notes for Tech and Hotel:</label>
                <textarea class="textlabelarea span12"
                    rows="3" cols="70" name="servnotes" ><?php echo htmlspecialchars($session["servnotes"],ENT_NOQUOTES);?></textarea>
            </div>
            <div class="span4">
                <label class="control-label" for="notesforprog">Notes for Programming Committee:</label>
                <textarea class="textlabelarea span12"
                    rows="3" cols="70" name="notesforprog" ><?php echo htmlspecialchars($session["notesforprog"],ENT_NOQUOTES);?></textarea>
            </div>
        </div>
        <div id="buttonBox" class="clearfix">
            <div class="pull-right">
                <button class="btn" type=reset value="reset">Reset</button>
                <button class="btn btn-primary" type=submit value="save" onclick="mysubmit()">Save</button>
            </div>
        </div>
        <input type="hidden" name="action" value="<?php echo ($action === "create") ? "create" : "edit"; ?>" />
    </form>
</div>
<?php
    staff_footer();
}
?>
