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

	// SELECT * FROM SessionEditHistory S
   	function getEditHistory($id) {
		$SQL = "SELECT name, timestamp  FROM SessionEditHistory S where S.sessionid =".$id." order by S.timestamp desc";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		
		echo "<table width='600'>";
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<tr>";
			echo "<td>" . htmlentities($row[timestamp]) . "</td>";
			echo "<td>" . htmlentities($row[name]) . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
$id = $_GET["id"];

echo "<div>";
if ($id) {
	getSessionName($id);
	getEditHistory($id);
} else {
	echo "No data";
}
echo "</div>";

?>
