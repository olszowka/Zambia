<?php
  function RenderViewSessions() {
    global $result; 
    $title="Session Query Results";
      staff_header($title);
?>
    <TABLE class="table table-condensed table-striped">
        <COL><COL><COL><COL><COL><COL><COL><COL>
        <TR>
            <TH class="border1121">Sess.<BR>ID</TH>
            <TH class="border1121">Track</TH>
            <TH class="border1121">Title</TH>
            <TH class="border1121">Duration</TH>
            <TH class="border1121">Est. Atten.</TH>
            <TH class="border1121">Status</TH>
<!--            <TH class="border1121">Scheduled ?</TH>
            <TH class="border1121">Schedule Session</TH>
-->
            </TR>
<?php
    while (list($sessionid,$trackname,$title,$duration,$estatten,$statusname)= mysql_fetch_array($result, MYSQL_NUM)) {
        echo "        <TR>\n";
        echo "            <TD class=\"border1111\"><A HREF=\"EditSession.php?id=".$sessionid."\">".$sessionid."</A></TD>\n";
        echo "            <TD class=\"border1111\">".$trackname."</TD>\n";
        echo "            <TD class=\"border1111\">".htmlspecialchars($title,ENT_NOQUOTES)."</TD>\n";
        echo "            <TD class=\"border1111\">".$duration."</TD>\n";
        echo "            <TD class=\"border1111\">".$estatten."</TD>\n";
        echo "            <TD class=\"border1111\">".$statusname."</TD>\n";
        echo "<!--            <TD class=\"border1111\">&nbsp;</TD>\n";
        echo "            <TD class=\"border1111\">Schedule</A></TD>\n";
        echo "-->";
        echo "            </TR>\n";
        }
?>
            </TR>
        </TABLE>
<?php
  staff_footer();  }
?>
