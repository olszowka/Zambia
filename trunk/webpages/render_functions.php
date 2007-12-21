<?php
// RenderPrecis display requires:  a populated dataarray containing rows with 
// $sessionid,$trackname,$title,$duration,$estatten,$progguiddesc, $persppartinfo IN THAT ORDER
// it displays the precis view of the data.
function RenderPrecis($result,$showlinks) {
    
    echo "<H2>Generated by Zambia: ".date('d-M-Y h:i A')."</H2>\n";
    echo "<hr>\n";
    echo "<TABLE>\n";
    echo "   <COL><COL><COL><COL><COL>\n";
    while (list($sessionid,$trackname,$typename,$title,$duration,$estatten,$progguiddesc,$persppartinfo)
	     =mysql_fetch_array($result, MYSQL_NUM)) {
        echo "<TR>\n";
        echo "  <TD rowspan=3 class=\"border0000\" id=\"sessidtcell\"><b>";
        if ($showlinks){
		echo "<A HREF=\"StaffAssignParticipants.php?selsess=".$sessionid."\">".$sessionid."</A>";
	    }
        echo "&nbsp;&nbsp;</TD>\n";
	echo "  <TD class=\"border0000\"><b>".$trackname."</TD>\n";
	echo "  <TD class=\"border0000\"><b>".$typename."</TD>\n";
        echo "  <TD class=\"border0000\"><b>";
           if ($showlinks){
                   echo "<A HREF=\"EditSession.php?id=".$sessionid."\">".htmlspecialchars($title,ENT_NOQUOTES)."</A>";
	       } else {
                   echo htmlspecialchars($title,ENT_NOQUOTES);
               }
        echo "&nbsp;&nbsp;</TD>\n";
	echo "  <TD class=\"border0000\"><b>".$duration."</TD>\n";
	echo "</TR>\n";
	echo "<TR><TD colspan=4 class=\"border0010\">".htmlspecialchars($progguiddesc,ENT_NOQUOTES)."</TD></TR>\n";
	echo "<TR><TD colspan=4 class=\"border0000\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</TD></TR>\n";
	echo "<TR><TD colspan=5 class=\"border0020\">&nbsp;</TD></TR>\n";
	echo "<TR><TD colspan=5 class=\"border0000\">&nbsp;</TD></TR>\n";
    }
    echo "</TABLE>\n";
}
?>