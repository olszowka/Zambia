<?php 
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

	function getParticipantContactInfo($id) {
		$SQL = "select email, postaddress, phone, regtype from CongoDump where badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		echo "<table>";
		echo "<tr><td colspan=5><b>Email:</b>" . htmlentities($row[email]) . "</td></tr>";
		echo "<tr><td colspan=5><b>Postal Address:</b>" . htmlentities($row[postaddress]) . "</td></tr>";
		echo "<tr><td colspan=5><b>Phone:</b>" . htmlentities($row[phone]) . "</td></tr>";
		echo "</table>";
	}

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

$id = $_GET["id"];

if ($id) {
getParticipantInfo($id);
getParticipantContactInfo($id);
} else {
	echo "No data";
}

?>
