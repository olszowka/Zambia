<?php
    global $participant,$message_error,$message2,$congoinfo;
    global $partAvail,$availability;
    $title="Search Panels";
    require ('BrainstormCommonCode.php'); // initialize db; check login;
    //                                  set $badgeid from session
    if (!may_I('BS_sear_sess')) {
        $message_error="You do not currently have permission to view this page.<BR>\n";
        RenderError($title,$message_error);
        exit();
        }
    brainstorm_header($title);

?>

<FORM method=POST action="BrainstormSearchSession_POST.php">
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

<P>Clicking Search without making any selections will display all panels.
</FORM>
</BODY>
</HTML>
