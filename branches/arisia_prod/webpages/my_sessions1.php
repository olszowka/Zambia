<?php
    global $participant,$message_error,$message2,$congoinfo;
    global $partAvail,$availability;
    $title="Search Sessions";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (!may_I('search_panels')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    participant_header($title);

?>

<div class="row-fluid">
  <FORM class="form-inline padded" method=POST action="SearchMySessions1.php">
        <label for="track">Track:</label>
          <SELECT class="tcell" name="track">
            <?php $query = "SELECT trackid, trackname FROM Tracks WHERE selfselect=1 ORDER BY display_order"; populate_select_from_query($query, '0', "ANY",false); ?>
          </SELECT>

        <label for="title">Title Search:</label>
        <INPUT name="title" placeholder="Session title"> </INPUT>

        <BUTTON class="btn btn-primary" type=submit value="search">Search</BUTTON>

<P>On the following page, you can select sessions for participation. You must <strong>SAVE</strong> your changes before leaving the page or your selections will not be recorded.
<P>Clicking Search without making any selections will display all sessions.
</FORM>
</div>
<?php
    participant_footer($title);
?>
