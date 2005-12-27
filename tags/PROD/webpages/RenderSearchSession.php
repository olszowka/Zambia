<?php
    // This function will output the page with the form to search for a session
    // Variables
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderSearchSession () {
    // still inside function RenderSearchSession
?>
  <FORM method=POST action="ShowSessions.php">
    <table>
      <tr>
        <td>Track: </td>
        <td>
          <SELECT name="track">
            <?php $query = "SELECT trackid, trackname FROM Tracks ORDER BY display_order"; populate_select_from_query($query, '0', "ANY",false); ?>
          </SELECT>
        </td>
        <td>Status:</td>
        <td>
          <SELECT name="status">
            <?php $query = "SELECT statusid, statusname FROM SessionStatuses ORDER BY display_order"; populate_select_from_query($query, '0', "ANY",false); ?>
          </SELECT>
        </td>
      </tr>
      <tr><td colspan=4 align=right><BUTTON type=submit value="search">Search</BUTTON></td></tr>
    </table>
  </FORM>
<?php } ?>
