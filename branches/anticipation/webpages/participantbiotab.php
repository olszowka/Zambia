<?php 
    require_once ('db_functions.php');
	
	function getParticipantInfo($id) {
		$SQL = "select bio, editedbio, pubsname, willparteng, willpartendtrans, willpartfre, willpartfretrans, willmoderate, masque, speaksFrench, speaksEnglish, speaksOther, otherLangs from Participants where badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		echo "<table>";
		echo "<tr>";
		echo "<td colspan=5><b>Publication  Name:</b>" . $row[pubsname] . "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<table>";
		echo "<tr>";
		echo "<td>";
		if ($row[editedbio]) {
			echo $row[editedbio];
		} else {
			echo $row[bio];
		}
		echo "<td>";
		echo "</tr>";
		echo "</table>";
	}

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

$id = $_GET["id"];

if ($id) {
getParticipantInfo($id);
} else {
	echo "No data";
}

?>
