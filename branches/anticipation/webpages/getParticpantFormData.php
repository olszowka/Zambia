<?php 
    require_once ('db_functions.php');

	$page = $_GET['page']; 
	$limit = $_GET['rows']; 
	$sidx = $_GET['sidx']; 
	$sord = $_GET['sord']; 
	if(!$sidx) $sidx =1; 

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

// calculate the number of rows for the query. We need this for paging the result 
$result = mysql_query("SELECT COUNT(*) AS count FROM ".PARTICIPANT_SOURCE.".rawdata"); 
$row = mysql_fetch_array($result,MYSQL_ASSOC); 
$count = $row['count']; 

// calculate the total pages for the query 
if( $count > 0 ) { 
              $total_pages = ceil($count/$limit); 
} else { 
              $total_pages = 0; 
} 

// if for some reasons the requested page is greater than the total 
// set the requested page to total page 
if ($page > $total_pages) $page=$total_pages;

// calculate the starting position of the rows 
$start = $limit*$page - $limit;

// if for some reasons start position is negative set it to 0 
// typical case is that the user type 0 for the requested page 
if($start <0) $start = 0; 

// the actual query for the grid data 
$SQL = "SELECT raw.mbox, raw.message_number, raw.mail_date, raw.name, raw.lang, raw.email, imp.badgeid from ".PARTICIPANT_SOURCE.".rawdata raw" .
	" left join Imported imp on raw.mbox = imp.mbox AND raw.message_number = imp.message_number ORDER BY $sidx $sord LIMIT $start , $limit";
$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 

// we should set the appropriate header information
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
              header("Content-type: application/xhtml+xml;charset=latin-1"); 
} else {
          header("Content-type: text/xml;charset=latin-1");
}
echo "<?xml version='1.0' encoding='latin-1'?>";
echo "<rows>";
echo "<page>".$page."</page>";
echo "<total>".$total_pages."</total>";
echo "<records>".$count."</records>";

// be sure to put text data in CDATA
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
echo "<row id='".$row[mbox]."_".$row[message_number]."'>";            
            echo "<cell>". $row[mail_date]."</cell>";
            echo "<cell><![CDATA[". htmlentities($row[name])."]]></cell>";
            echo "<cell>". $row[lang]."</cell>";
            echo "<cell><![CDATA[". htmlentities($row[email])."]]></cell>";
            echo "<cell>". $row[badgeid]."</cell>";
echo "</row>";
}
echo "</rows>"; 
?>
