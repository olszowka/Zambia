<?php
require('PDF_Label.php');

$MAX_LINE_CHAR = 80;
$MAX_LINES = 15;

require_once ('db_functions.php');
require_once('StaffCommonCode.php');

if (prepare_db()===false) {
	$message="Error connecting to database.";
	exit ();
}


$SQL = "select
         if ((P.pubsname is NULL), ' ', P.pubsname) as 'Participant',
		 S.pubsno as pubsno,
         DATE_FORMAT(ADDTIME('2009-08-06 00:00:00',starttime),'%a %l:%i %p') as 'StartTime',
         R.roomname as Roomname,
         S.title as Title
    from Sessions S
    JOIN Schedule SCH
          USING (sessionid)
    JOIN Rooms R
          USING (roomid)
         LEFT JOIN ParticipantOnSession POS
                ON SCH.sessionid=POS.sessionid
         LEFT JOIN Participants P
                ON POS.badgeid=P.badgeid
         LEFT JOIN Tracks T
                ON T.trackid=S.trackid
order by P.pubsname, SCH.starttime";

function truncate($string, $limit) {
	if(strlen($string) <= $limit) return $string;

	$string = substr($string, 0, $limit);
	if(false !== ($breakpoint = strrpos($string, $break))) {
		$string = substr($string, 0, $breakpoint);
	}

	return $string;
}

if(may_I('create_participant')) {
// Avery 05395
//2-1/3" x 3-3/8"
$pdf = new PDF_Label(array('paper-size'=>'letter', 'metric'=>'in', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>4, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>3.375, 'height'=>2.33, 'font-size'=>6));

$pdf->AddPage();

$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
$row = mysql_fetch_array($result,MYSQL_ASSOC);

$resultrow=mysql_fetch_array($result,MYSQL_ASSOC);

$text = "";
$count = 0;
$currentName = $resultrow['Participant'];
$prevName = $currentName;
while ($resultrow) {
	$currentName = $resultrow['Participant'];
	if ($currentName && $currentName != ' ') {
		if (($currentName != $prevName) || ($count > $MAX_LINES)) {
			if ( $text != "") {
				$text .= sprintf("%s","Green Room 515ab Programme ops 515c");
				$pdf->Add_Label($text);
			}
			$prevName = $currentName;
			$text = sprintf("%s\n",$currentName);
			$count = 0;
		}
		
		$str = sprintf("%s %s %s %s", $resultrow['StartTime'], $resultrow['Roomname'], $resultrow['Title'], $resultrow['pubsno'] );
		$str = truncate($str, $MAX_LINE_CHAR);
		$text .= sprintf("%s\n", $str );
		$count ++;
	}

	$resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
	if (!$resultrow) {
		$text .= sprintf("%s","Green Room 515ab Programme ops 515c");
		$pdf->Add_Label($text);
	}
}

$pdf->Output();
}
?> 
