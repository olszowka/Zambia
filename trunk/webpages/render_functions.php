<?php
//	$Header$
//	Copyright (c) 2011-2016 The Zambia Group. All rights reserved. See copyright document for more details.
//	RenderPrecis display requires:  a populated dataarray containing rows with 
//	$sessionid,$trackname,$typename,$title,$duration,$estatten,$progguiddesc, $persppartinfo IN THAT ORDER
//	it displays the precis view of the data.
function RenderPrecis($result,$showlinks) {
	
	echo "<h4 class=\"alert alert-success center\">Generated by Zambia: ".date('d-M-Y h:i A')."</h4>\n";
	echo "<p>If a room name and time are listed, then the session is on the schedule; otherwise, not.</p>";
	echo "<hr>\n";
	echo "<table class=\"table table-condensed\">\n";
	while (list($sessionid,$trackname,$typename,$title,$duration,$estatten,$progguiddesc,$persppartinfo,$starttime,$roomname,$statusname)
		 =mysql_fetch_array($result, MYSQL_NUM)) {
		echo "<tr>\n";
		echo "  <td rowspan=\"3\" class=\"border0000\" id=\"sessidtcell\" style=\"font-weight:bold\">";
		if ($showlinks){
			echo "<a href=\"StaffAssignParticipants.php?selsess=".$sessionid."\">".$sessionid."</a>";
			}
		echo "&nbsp;&nbsp;</td>\n";
		echo "  <td class=\"border0000\" style=\"font-weight:bold\">".$trackname."</td>\n";
		echo "  <td class=\"border0000\" style=\"font-weight:bold\">".$typename."</td>\n";
		echo "  <td class=\"border0000\" style=\"font-weight:bold\">";
		if ($showlinks){
				echo "<a href=\"EditSession.php?id=".$sessionid."\">".htmlspecialchars($title,ENT_NOQUOTES)."</a>";
				}
			else {
				echo htmlspecialchars($title,ENT_NOQUOTES);
				}
		echo "&nbsp;&nbsp;</td>\n";
		echo "  <td class=\"border0000\" style=\"font-weight:bold\">".$duration."</td>\n";
		echo "  <td class=\"border0000\" style=\"font-weight:bold\">";
		if ($roomname)
				echo $roomname;
			else
				echo "&nbsp;";
		echo "</td>\n";
		echo "  <td class=\"border0000\" style=\"font-weight:bold\">";
		if ($starttime)
				echo $starttime;
			else
				echo "&nbsp;";
		echo "</td>\n";
		echo "    <td class=\"border0000\" style=\"font-weight:bold\">$statusname</td>\n";
		echo "    <td class=\"border0000\" style=\"font-weight:bold\"><a href=\"SessionHistory.php?selsess=$sessionid\">History</a></td>\n";
		echo "</tr>\n";
		echo "<tr><td colspan=\"8\" class=\"border0010\">".htmlspecialchars($progguiddesc,ENT_NOQUOTES)."</td></tr>\n";
		if ($persppartinfo) {
			echo "<tr><td colspan=\"8\" class=\"border0000\"><span class=\"alert\" style=\"padding: 0\">".htmlspecialchars($persppartinfo,ENT_NOQUOTES)."</span></td></tr>\n";
			}
		echo "<tr><td colspan=\"8\" class=\"border0020\">&nbsp;</td></tr>\n";
		}
	echo "</table>\n";
}
// function RenderQueryResult($link,$result)
// Writes output to render the results of a query as a table
//
function RenderQueryResult($link,$result) {
	if (!mysql_info($link)) {
		return(-1); // most recent query failed
		}
	$numrows=$mysql_num_rows($result);
	if ($numrows==0) {
		return(-2); // nothing to display
		}
	$numfields=$mysql_num_fields($result);
	echo "<table><tr>";
	for ($i=0; $i<$numfields; $i++) {
		$x=mysql_field_name($result, $i);
		echo "<th>$x</th>";
		}
	echo "</tr>\n";
	for ($i=0; $i<$numrows; $i++) {
		$row=mysql_fetch_array($result, MYSQL_NUM);
		echo "<tr>";
		for ($j=0; $j<numfields; $j++) {
			$x=$row[$j];
			echo "<td>$x</td>";
			}
		echo "</tr>\n";
		}
	 echo "</table>\n";
	 return(0);
	 }
?>
