<?php 
	header('Content-type: text/html');
    require_once ('db_functions.php');
	
	function getParticipantInfo($id) {
		$SQL = "select bio, editedbio, pubsname, willparteng, willpartengtrans, willpartfre, willpartfretrans, willmoderate, masque, speaksFrench, speaksEnglish, speaksOther, otherLangs from Participants where badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		echo "<table>";
		echo "<tr>";
		echo "<td colspan=5><b>Publication  Name:</b>" . htmlentities($row[pubsname]) . "</td>";
		echo "</tr>";
		echo "</table>";

		echo "<table>";
		echo "<tr>";
		echo "<td>";
		if ($row[editedbio]) {
			echo htmlentities($row[editedbio]);
		} else {
			echo htmlentities($row[bio]);
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

/*
<html xmlns="http://www.w3.org/TR/xhtml1/transitional">

 */
echo "<div>";
if ($id) {
getParticipantInfo($id);
} else {
	echo "No data";
}
echo "</div>";

?>
