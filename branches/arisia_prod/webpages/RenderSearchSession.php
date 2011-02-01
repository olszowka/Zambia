<?php
    // This function will output the page with the form to search for a session
    // Variables
    //     session: array with all data of record to edit or defaults for create
    //     message1: a string to display before the form
    //     message2: an urgent string to display before the form and after m1
function RenderSearchSession () {
    $TRU=TRUE;
    // still inside function RenderSearchSession
?>
  <FORM method=POST action="ShowSessions.php" class="regform">
    <table>
      <tr class="regform">
        <td class="regform3">Track:</td>
        <td class="regform3">
          <SELECT name="track" class="regform2">
            <?php $query = "SELECT trackid, trackname FROM Tracks ORDER BY display_order"; populate_select_from_query($query, '0', "ANY", $TRU); ?>
          </SELECT>
        </td>
        <td class="regform3">Type:</td>
        <td class="regform3">
          <SELECT name="type" class="regform2">
            <?php $query = "SELECT typeid, typename FROM Types ORDER BY display_order"; populate_select_from_query($query, '0', "ANY", $TRU); ?>
          </SELECT>
        </td>
        <td class="regform3">Status:</td>
        <td class="regform3">
          <SELECT name="status" class="regform2">
            <?php $query = "SELECT statusid, statusname FROM SessionStatuses ORDER BY display_order"; populate_select_from_query($query, '0', "ANY", $TRU); ?>
          </SELECT>
        </td>
        <td class="regform3">
           Session ID:
        </td>
        <td class="regform3">
           <INPUT type="text" name="sessionid" size="10" class="regform2">
        </td>
        <td class="regform3">
           (Leave blank for any)
        </td>
      </tr>
      <tr class="regform">
          <td>Division ID:</td>
          <td><SELECT name="divisionid">
              <?php populate_select_from_table("Divisions",0,"ANY",true); ?>
              </SELECT>
          </td>
          <td>Title:</td>
          <td><INPUT type="text" name="searchtitle" size="25"> </td>
          <td colspan="2">(Leave blank for any)</td>
          <td colspan="3">&nbsp;</td>
      </tr>
      <tr><td colspan=9 align=right><BUTTON type=submit value="search">Search</BUTTON></td></tr>
    </table>
  </FORM>
<?php } ?>
