<?php
// Copyright (c) 2011-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
    global $participant, $message_error, $message2, $congoinfo;
    global $partAvail, $availability, $title;
    $title="Search Sessions";
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (!may_I('search_panels')) {
        $message_error="You do not currently have permission to view this page.<br>\n";
        RenderError($message_error);
        exit();
        }
    require_once('ParticipantHeader.php');
    require_once('ParticipantFooter.php');
    participant_header($title);
    $query = <<<EOD
SELECT
        P.interested
    FROM
        Participants P
    WHERE
        P.badgeid = '$badgeid';
EOD;
    $results = mysql_query_with_error_handling($query);
    if (!$results) {
        exit(); // Should have existed already anyway.
    }
    $resultsArray = mysql_fetch_array($results, MYSQLI_ASSOC);
    if ($resultsArray["interested"] !== '1') {
        ?>
            <div class="alert alert-block">
                <h4>Warning!</h4>
                <span>
                    You have not indicated in your profile that you will be attending <?php echo CON_NAME; ?>.
                    You will not be able to save your panel choices until you so do.
                </span>
            </div>
        <?php
    }
?>

<div class="row-fluid">
    <form class="form-inline padded" method=POST action="SearchMySessions1.php">
        <label for="track">Track:</label>
        <select class="tcell" name="track">
            <?php
                $query = "SELECT trackid, trackname FROM Tracks WHERE selfselect=1 ORDER BY display_order";
                populate_select_from_query($query, '0', "ANY", false);
            ?>
        </select>

        <label for="title">Title Search:</label>
        <input name="title" placeholder="Session title" />

        <button class="btn btn-primary" type=submit value="search">Search</button>

        <p>On the following page, you can select sessions for participation. You must <strong>SAVE</strong> your changes
            before leaving the page or your selections will not be recorded.
        <p>Clicking Search without making any selections will display all sessions.
    </form>
</div>
<?php
    participant_footer($title);
?>
