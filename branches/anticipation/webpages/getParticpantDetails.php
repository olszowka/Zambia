<?php 
    require_once ('db_functions.php');

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

$id = $_GET["id"];

$args = explode('_', $id);

$SQL  = "SELECT postmail, french, other_fr, language_fr, english, other_en, language_en ";
$SQL .= "FROM anticipation.rawdata WHERE mbox LIKE '".$args[0]."' AND  message_number LIKE '" . $args[1] . "'";
$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());

// we should set the appropriate header information
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
              header("Content-type: application/xhtml+xml;charset=utf-8"); 
} else {
          header("Content-type: text/xml;charset=utf-8");
}
echo "<?xml version='1.0' encoding='utf-8'?>";

echo "<rows>";
// be sure to put text data in CDATA
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	echo "<row>";
	echo "<cell>". $row[postmail]."</cell>\n";
	echo "<cell>". $row[french]."</cell>";
	echo "<cell>". $row[other_fr]."</cell>";
	echo "<cell><![CDATA[". $row[language_fr]."]]></cell>";
	echo "<cell>". $row[english]."</cell>";
	echo "<cell>". $row[other_en]."</cell>";
	echo "<cell><![CDATA[". $row[language_en]."]]></cell>";
	echo "</row>";
}
echo "</rows>";				
?>
