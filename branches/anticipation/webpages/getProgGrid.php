<?php 
    require_once ('db_functions.php');
	$page = $_GET['page']; 
	$limit = $_GET['rows']; 
	$sidx = $_GET['sidx']; 
	$sord = $_GET['sord']; 
	$showall = false;
	if(!$sidx) $sidx =1; 

	if(isset($_GET["nm_mask"])) $nm_mask = mb_convert_encoding ( $_GET['nm_mask'], 'latin1', 'utf8' ); else $nm_mask = ""; 
	
	if(isset($_GET["rm_mask"])) $rm_mask = mb_convert_encoding ( $_GET['rm_mask'], 'latin1', 'utf8' ); else $rm_mask = ""; 
	
	if(isset($_GET["showall"])) $showall = true;
	
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }
    
$SQLWHERE = "";    
   
$BASESQLSELECT = "select * from (SELECT S.sessionid as sessionid, SD.starttime as starttime, S.title as title, ADDDATE('2009-08-06', INTERVAL (HOUR(SD.starttime) DIV 24) DAY) as date, " 
	. "ADDTIME(CONCAT(HOUR(SD.starttime) MOD 24, ':', MINUTE(SD.starttime)), 0) as start, "
	. "ADDTIME(CONCAT(HOUR(SD.starttime) MOD 24, ':', MINUTE(SD.starttime)), S.duration) as end, "
	. " (SELECT R.roomname from Rooms R where R.roomid = SD.roomid) as room, GROUP_CONCAT(P.pubsname) participants ";
	
$BASESQL = "FROM Schedule SD, Sessions S "
	. "left join (ParticipantOnSession PS CROSS JOIN Participants P) on (PS.sessionid = S.sessionid AND P.badgeid = PS.badgeid) "
	. " where S.statusid = 5 and S.sessionid = SD.sessionid ";
	
$SQLGROUP = " group by S.sessionid ) as temp "; // order by SD.starttime, SD.roomid"

if ($nm_mask) {
	$SQLWHERE .= " where (temp.title like '%".$nm_mask."%' OR temp.participants like '%".$nm_mask."%') ";
}

if ($rm_mask) {
	if ($nm_mask) {
		$SQLWHERE .= " AND ";
	} else {
		$SQLWHERE .= " WHERE ";
	}
	$SQLWHERE .= " (temp.room like '%".$rm_mask."%') ";
}

// calculate the number of rows for the query. We need this for paging the result 
$SQL = "SELECT COUNT(*) AS count from (" . $BASESQLSELECT . $BASESQL . $SQLGROUP . $SQLWHERE .") as rr" ;

$result = mysql_query($SQL);

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
$SQL = $BASESQLSELECT . $BASESQL . $SQLGROUP . $SQLWHERE;

//if (!$showall) {
//	$SQL .= "AND d.interested != '2' ";
//}
//if ($nm_mask) {
//	$SQL .= "AND (c.firstname like '%".$nm_mask."%' OR c.lastname like '%".$nm_mask."%' OR d.pubsname like '%".$nm_mask."%')";
//}
$SQL .=" ORDER BY $sidx $sord LIMIT $start , $limit";

/*
 * NOTE: since the tables are latin1 and browsers expect UTF8 (especially when using AJAX)
 * we ask the DB to send the data back as UTF8
 */
$result = mysql_query( $SQL ) or die("Couldn't execute query.".mysql_error()); 

// we should set the appropriate header information
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
              header("Content-type: application/xhtml+xml"); 
} else {
          header("Content-type: text/xml");
}
echo "<?xml version='1.0'?>";
echo "<rows>";
echo "<page>".$page."</page>";
echo "<total>".$total_pages."</total>";
echo "<records>".$count."</records>";

// be sure to put text data in CDATA
while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
	echo "<row id='".$row[sessionid]."'>";
	echo "<cell><![CDATA[". htmlentities($row[date])."]]></cell>";
	echo "<cell><![CDATA[". htmlentities($row[start])."]]></cell>";
	echo "<cell><![CDATA[". htmlentities($row[end])."]]></cell>";
	echo "<cell><![CDATA[". htmlentities($row[room])."]]></cell>";
	echo "<cell><![CDATA[". htmlentities($row[title])."]]></cell>";
	echo "<cell><![CDATA[". htmlentities($row[participants])."]]></cell>";
	echo "</row>";
}
echo "</rows>"; 
?>
