<?php 
	header("Content-type: text/html");

    require_once ('db_functions.php');
    require_once ('data_functions.php');
	
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }
    
    //SELECT * FROM Sessions S
    function getSessionName($id) {
		$SQL = "SELECT title FROM Sessions S where S.sessionid =".$id."";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		
		echo "<table width='500'>";
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<tr><td><b>" . htmlentities($row[title]) . "</b></td></tr>";
		}
		echo "</table>";
    	
    }

	//SELECT * FROM SessionHasService S, Services Sv where S.serviceid = Sv.serviceid;
	function getServices($id) {
		$SQL = "SELECT * FROM SessionHasService S, Services Sv where S.serviceid = Sv.serviceid AND S.sessionid =".$id." ";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		
		echo "<table width='600'>";
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<tr>";
			echo "<td>" . htmlentities($row[servicename]) . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
$id = $_GET["id"];

echo "<div>";
if ($id) {
	getSessionName($id);
	getServices($id);
} else {
	echo "No data";
}
echo "</div>";

?>
