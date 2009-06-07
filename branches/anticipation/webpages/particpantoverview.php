<?php 
    require_once ('db_functions.php');
	
	function getCongoDump($id) {
		$SQL = "select firstname, lastname, email, regtype from CongoDump where badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		echo "Firstname: " . $row[firstname];
		echo "Last name: " . $row[lastname];
		echo "Email: " . $row[email];
		echo "Reg Type: " . $row[regtype];
	};
	
	function convertToYN($arg) {
		switch ($arg) {
			case "0" : return "no"; break;
			case "1" : return "yes"; break;
			default : return "unknown";
		}
	}
	
	function getGeneralInfo($id) {
//		SELECT infoid, infovalue FROM zambiademo.ParticipantGeneralInfo P
		$SQL = "select g.info_description, i.infovalue from ParticipantGeneralInfo i, GeneralInfoRef g where badgeid = '".$id."' and i.infoid = g.infoid";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());

		echo "<table border='1'>";
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<tr><td width='150'><em>" . $row[info_description] . "</em></td><td>" . $row[infovalue] . "</td></tr>";
		}
		echo "</table>";
	}
	
	function getAvailabilityTimes($id) {
		global $daymap;

		$SQL = "select availabilitynum, starttime, endtime from ParticipantAvailabilityTimes where badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		
		$day = 1;
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		if ($row) {
			echo "<table border=1>";
			echo "<caption>Availability</caption>";
			echo "<tr><td>".$daymap["long"][$day]."</td>";
			while  ($row) {
				if (($row[starttime] - ($day-1)*24) < 24) {
					echo "<td>" . ($row[starttime] - ($day-1)*24) . " to " . ($row[endtime] - ($day-1)*24) . "</td>";
				} else  {
					echo "</tr><tr>";
					$day += 1;
					echo "<td>" . $daymap["long"][$day] . "</td>";
				}
				$row = mysql_fetch_array($result,MYSQL_ASSOC);
			}
			echo "</tr>";
			echo "</table>";
		} else {
			echo "Availability has not been entered<br/>";
		}
		
	}
	
	function getTrackInterest($id) {
		global $daymap;

		$SQL = "select t.trackname from ParticipantTrackInterest i, Tracks t where badgeid = '".$id."' and i.trackid = t.trackid";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		
		echo "<table width='200'><caption>Track Interests</caption>";
		while ($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
			echo "<tr><td>" . $row[trackname] . "</td></tr>";
		}
		echo "</table>";
	}
	
	function getParticipantInfo($id) {
		$SQL = "select bio, editedbio, pubsname, willparteng, willpartendtrans, willpartfre, willpartfretrans, willmoderate, masque, speaksFrench, speaksEnglish, speaksOther, otherLangs from Participants where badgeid = '".$id."'";
		$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		echo "<table>";
		echo "<tr>";
		echo "<td colspan=5><b>Publication  Name:</b>" . $row[pubsname] . "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><b>Speaks French:</b> ".convertToYN($row[speaksFrench])."</td>";
		echo "<td><b>Speaks English:</b> ".convertToYN($row[speaksEnglish])."</td>";
		echo "<td><b>Speaks Other:</b> ".convertToYN($row[speaksOther])."</td>";
		echo "<td><b>Language(s):</b> ".$row[otherLangs]. "</td>";
		echo "</tr>";
		echo "</table>";
		echo "<table>";
		echo "<tr>";
		echo "<td colspan=5><b>Will Particpate in:</b></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td><b>English:</b> ".convertToYN($row[willparteng])."</td>";
		echo "<td><b>English Translated:</b> ".convertToYN($row[willpartendtrans])."</td>";
		echo "<td><b>French:</b> ".convertToYN($row[willpartfre])."</td>";
		echo "<td><b>French Translated:</b> ".convertToYN($row[willpartfretrans])."</td>";
		echo "<td><b>Will Moderate:</b> ".convertToYN($row[willmoderate])."</td>";
		echo "<td><b>In Masquerade:</b> ".convertToYN($row[masque])."</td>";
		echo "</tr>";
		echo "</table>";

//		echo "<table>";
//		echo "<tr>";
//		echo "<td>";
//		if ($row[editedbio]) {
//			echo $row[editedbio];
//		} else {
//			echo $row[bio];
//		}
//		echo "<td>";
//		echo "</tr>";
//		echo "</table>";
	}

    if (prepare_db()===false) {
        $message="Error connecting to database.";
        exit ();
    }

$id = $_GET["id"];

if ($id) {
//getCongoDump($id);
getParticipantInfo($id);
echo "<table>";
echo "<tr><td align='top'>";
getTrackInterest($id);
echo "</td><td><br/></td>";
echo "<td align='top'>";
getAvailabilityTimes($id);
echo "</td></tr>";
echo "</table>";
getGeneralInfo($id);
} else {
	echo "No data";
}

?>
