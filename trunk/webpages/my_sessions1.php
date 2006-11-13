<?php
    global $participant,$message_error,$message2,$congoinfo;
    global $partAvail,$availability;
    $title="Search Panels";
    require ('db_functions.php'); //define database functions
    require ('data_functions.php'); //define database functions
    require ('RenderErrorPart.php');  // define function to report error
    require ('PartCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (!may_I('search_panels')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    require_once('ParticipantHeader.php');
    participant_header($title);

?>

<FORM method=POST action="SearchMySessions1.php">
  <table>
    <COL><COL><COL><COL><COL>
    <tr> <!-- trow -->

        <td>Track: </td>
        <td>
          <SELECT class="tcell" name="track">
            <?php $query = "SELECT trackid, trackname FROM Tracks WHERE selfselect=1 ORDER BY display_order"; populate_select_from_query($query, '0', "ANY",false); ?>
          </SELECT>
        </td>

        <td>Title Search:</td>
        <td> <INPUT name="title"> </INPUT> </td>

    </tr> <!-- trow -->

    <td colspan=5, align=right>
        <BUTTON type=submit value="search">Search</BUTTON>
    </td><p>&nbsp;</p>

  </tr>
</table>

<P>On the following page, you can select panels for participation.  You must SAVE your changes before leaving the page or your selections will not be recorded.
<P>Clicking Search without making any selections will display all panels.
</FORM>
</BODY>
</HTML>
