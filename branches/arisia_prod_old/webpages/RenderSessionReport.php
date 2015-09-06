<?php
  function RenderSessionReport() {
    global $result; 
    $title="Session Report";
    require_once('db_functions.php');
    require_once('StaffHeader.php');
    require_once('StaffFooter.php');
    require_once('StaffCommonCode.php');
    staff_header($title);
?>
<p> Here are the results of your search.  The report includes Session id, track, title, duration, estimated attendance, pocket program text, notes for prospective participants.
<table class="table table-condensed table-hover">
<?php
    while (list($sessionid,$trackname,$title,$duration,$estatten,$pocketprogtext, $persppartinfo)= mysql_fetch_array($result, MYSQL_NUM)) {
        echo "        <TR>\n";
        echo "            <TD rowspan=3 class=\"border0000\" id=\"sessidtcell\">
<A HREF=\"EditSession.php?id=".$sessionid."\"><b>".$sessionid."</a>&nbsp;&nbsp;</TD>\n";

        echo "            <TD class=\"border0000\"><b>".$trackname."</TD>\n";
        echo "            <TD class=\"border0000\"><b>".htmlspecialchars($title,ENT_NOQUOTES)."</TD>\n";
        echo "            <TD class=\"border0000\"><b>".$duration." hr</TD>\n";
        echo "            <TD rowspan=3 class=\"border0000\">".$estatten."&nbsp;&nbsp;</TD>\n";
        echo "            </TR>\n";
        echo "        <TR><TD colspan=3 class=\"border0010\">".htmlspecialchars($pocketprogtext,ENT_NOQUOTES)."</TD></TR>\n";
        echo "        <TR><TD colspan=3 class=\"border0000\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</TD></TR>\n";
        echo "        <TR><TD colspan=5 class=\"border0020\">&nbsp;</TD></TR>\n";
        }
?>
        </TABLE>
<?php
  staff_footer();    }
?>
