<?php
    function RenderSessionCountReport() {
      global $result;
      $title="Session Count Report";
      require_once('db_functions.php');
      require_once('StaffHeader.php');
      require_once('StaffFooter.php');
      require_once('StaffCommonCode.php');
      staff_header($title);
?>
    <TABLE>
        <COL><COL>
        <TR>
            <TH class="y1">Track</TH>
            <TH class="y2">Status</TH>
            <TH class="y3">Number of<BR>Sessions</TH>
            </TR>
<?php
    while (list($track,$status,$count)= mysql_fetch_array($result, MYSQL_NUM)) {
        echo "        <TR>\n";
        echo "            <TD class=\"x1\">".$track."</TD>\n";
        echo "            <TD class=\"x2\">".$status."&nbsp;&nbsp;&nbsp;</TD>\n";
        echo "            <TD class=\"x3\">".$count."&nbsp;&nbsp;&nbsp;</TD>\n";
        echo "            </TR>\n";
        }
?>
        </TABLE>
<?php staff_footer();  } ?>
