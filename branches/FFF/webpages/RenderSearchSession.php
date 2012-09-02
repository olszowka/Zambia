<?php
  // This function will output the page with the form to search for a session
  // Variables
  //     session: array with all data of record to edit or defaults for create
  //     message1: a string to display before the form
  //     message2: an urgent string to display before the form and after m1
function RenderSearchSession () {
  $ReportDB=REPORTDB; // make it a variable so it can be substituted
  $BioDB=BIODB; // make it a variable so it can be substituted

  // Tests for the substituted variables
  if ($ReportDB=="REPORTDB") {unset($ReportDB);}
  if ($BiotDB=="BIODB") {unset($BIODB);}

  $TRU=TRUE;
  // still inside function RenderSearchSession
  if (file_exists("../Local/Verbiage/RenderSearchSession_0")) {
    echo file_get_contents("../Local/Verbiage/RenderSearchSession_0");
  }
?>

  <FORM method=POST action="ShowSessions.php">
    <table>
      <tr>
        <td>Track: </td>
        <td>
          <SELECT name="track">
            <?php $query = "SELECT trackid, trackname FROM $ReportDB.Tracks ORDER BY display_order"; populate_select_from_query($query, '0', "ANY", $TRU); ?>
          </SELECT>
        </td>
        <td>Type:</td>
        <td>
          <SELECT name="type">
            <?php $query = "SELECT typeid, typename FROM $ReportDB.Types ORDER BY display_order"; populate_select_from_query($query, '0', "ANY", $TRU); ?>
          </SELECT>
        </td>
        <td>Status:</td>
        <td>
          <SELECT name="status">
            <?php $query = "SELECT statusid, statusname FROM $ReportDB.SessionStatuses ORDER BY display_order"; populate_select_from_query($query, '0', "ANY", $TRU); ?>
          </SELECT>
        </td>
        <td>
           Session ID:
        </td>
        <td>
           <INPUT type="text" name="sessionid" size="10">
        </td>
        <td>
           (Leave blank for any)
        </td>
      </tr>
      <tr><td colspan=9 align=right><BUTTON type=submit value="search">Search</BUTTON></td></tr>
    </table>
  </FORM>
<?php } ?>
