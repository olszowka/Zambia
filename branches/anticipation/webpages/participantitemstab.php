<?php 
	header("Content-type: text/html;");

    require_once ('db_functions.php');
	
	function getParticipantInfo($id) {
		$SQL = "select pubsname from Participants where badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		echo "<table>";
		echo "<tr>";
		echo "<td colspan=5><b>Publication  Name:</b>" . htmlentities($row[pubsname]) . "</td>";
		echo "</tr>";
		echo "</table>";
	}
	
	function getParticipantSessions($id) {
		$SQL = "SELECT title FROM Sessions S, ParticipantOnSession P where P.sessionid = S.sessionid and P.badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		
		echo "<table>";
		
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<tr>";
			echo "<td colspan=2>" . htmlentities($row[title]) . "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
	}

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

$id = $_GET["id"];

echo "<div>";
if ($id) {
getParticipantInfo($id);
getParticipantSessions($id);
} else {
	echo "No data";
}
echo "</div>";

?>
