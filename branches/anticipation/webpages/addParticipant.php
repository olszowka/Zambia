<?php 
    require_once ('db_functions.php');
	
	/*
	 *  Functions to support the addition of time information for a participant
	 */
	function createAvailSQL($badgeid, $day, $range) {
		$sql = "INSERT into ParticipantAvailabilityTimes set badgeid='". $badgeid ."' ";
		$sql .= ", availabilitynum = " . $day;
		$sql .= ", " . $range;

		return $sql;
	}
	
	function createAvailTimesForDay($badgeid, $day, $availnum, $times) {
		$ranges = array(
		"starttime = '".(($day-1)*24 + 9).":00:00', endtime = '".(($day-1)*24 + 10).":00:00'",
		"starttime = '".(($day-1)*24 + 10).":00:00', endtime = '".(($day-1)*24 + 12).":00:00'",
		"starttime = '".(($day-1)*24 + 12).":00:00', endtime = '".(($day-1)*24 + 17).":00:00'",
		"starttime = '".(($day-1)*24 + 17).":00:00', endtime = '".(($day-1)*24 + 18).":00:00'",
		"starttime = '".(($day-1)*24 + 18).":00:00', endtime = '".(($day-1)*24 + 19).":00:00'",
		"starttime = '".(($day-1)*24 + 19).":00:00', endtime = '".(($day-1)*24 + 20).":00:00'",
		"starttime = '".(($day-1)*24 + 20).":00:00', endtime = '".(($day-1)*24 + 22).":00:00'",
		"starttime = '".(($day-1)*24 + 22).":00:00', endtime = '".(($day-1)*24 + 24).":00:00'",
		"starttime = '".(($day)*24 + 0).":00:00', endtime = '".(($day)*24 + 2).":00:00'"
		);

		$indx = 0;
		foreach($times as &$time) {
			if ($times[$indx] == 1) {
				if ($indx == 8) {
					$day += 1;
				}
				$sql = createAvailSQL($badgeid, $availnum, $ranges[$indx]);
				$availnum += 1;
				$result = mysql_query( $sql );
				if (!$result) throw new Exception("Couldn't execute query.".mysql_error()); 
			}
			$indx += 1;
		}
		
		return $availnum;
	}
	
	/*
	 * 
	 */
	function addtoCongo($badgeid, $longname, $email, $postmail) {
		// Add them CongoDump
		$badgeid = $badgeid;
		$names = explode(' ', $longname);
		$CONGOSQL  = "INSERT into CongoDump set badgeid='". $badgeid ."' ";
		$CONGOSQL .= ", firstname='". mysql_real_escape_string($names[0]) ."' ";
		// Take a guess that the last name is the rest of the string... works for the majority but not all ....
		array_shift($names);
		$CONGOSQL .= ", lastname='". mysql_real_escape_string(implode(' ', $names)) ."' ";
		$CONGOSQL .= ", email='". mysql_real_escape_string($email) ."' ";
		$CONGOSQL .= ", postaddress='". mysql_real_escape_string($postmail) ."' ";
		$CONGOSQL .= ", badgename='". mysql_real_escape_string($longname) ."' ";
		$result = mysql_query( $CONGOSQL );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
	}
	
	/*
	 * 
	 */
	function addToParticipants($badgeid, $particbio, $panelfr, $panelIntFr, $panelEn, $panelIntEn, $moderate, $masque,
							$french, $english, $other, $otherLang, $pubsname) {
		// Add to Participants
		$PARTICPANTSQL  = "INSERT into Participants set badgeid='". $badgeid ."' ";
		$PARTICPANTSQL .= ", password='ffff', interested='1'";
		$PARTICPANTSQL .= ", bio =\"". mysql_real_escape_string($particbio) ."\" ";
		$PARTICPANTSQL .= ", pubsname=\"".mysql_real_escape_string($pubsname)."\"";
		$PARTICPANTSQL .= ", willpartfre ='".$panelfr."' ";
		$PARTICPANTSQL .= ", willpartfretrans ='".$panelIntFr."' ";
		$PARTICPANTSQL .= ", willparteng ='".$panelEn."' ";
		$PARTICPANTSQL .= ", willpartengtrans ='".$panelIntEn."' ";
		$PARTICPANTSQL .= ", willmoderate ='".$moderate."' ";
		$PARTICPANTSQL .= ", masque ='".$masque."' ";
		$PARTICPANTSQL .= ", speaksFrench ='".$french."' ";
		$PARTICPANTSQL .= ", speaksEnglish ='".$english."' ";
		$PARTICPANTSQL .= ", speaksOther ='".$other."' ";
		$PARTICPANTSQL .= ", otherLangs ='".mysql_real_escape_string($otherLang)."' ";
		$result = mysql_query( $PARTICPANTSQL );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
	}
	
	/*
	 * 
	 */
	function addToPermissionRoles($badgeid) {
		// Add to UserHasPermissionRole
		$PERMSQL = "INSERT into UserHasPermissionRole set badgeid='". $badgeid ."', permroleid='3' ";
		$result = mysql_query( $PERMSQL );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error()); 
	}
	
	/*
	 * 
	 */
	function addAvailabilityDays($badgeid, $maxitems) {
		$SQL = "INSERT into ParticipantAvailability set badgeid='". $badgeid ."' ";
		$SQL .= ", maxprog='".$maxitems."'";
		$result = mysql_query( $SQL );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
	}
	
	/*
	 * 
	 */
	function getParticpantDetails($args) {
		/*
		 * Get the data from the participant forms, this is one bloody big table and not normalised... because it is imported from CSV
		 */		
		$SQL = "SELECT mbox, message_number, name, email, postmail, french, other_fr, language_fr, english, other_en, language_en, ";
		$SQL .= "panel_fr, panel_interp_fr, panel_en, panel_interp_en, moderate, ";
		$SQL .= "englishlit, frenchlit, academictrack, fantrack, mediatrack, gamingtrack, gameknow, visualarts, visualartsknow, culturetrack, cultureknow, ";
		$SQL .= "costumetrack, filktrack, techtrack, kidstrack, kidstrackknow, quiztrack, creativewrite, kaffeeklatsch, readingtrack, speclknow, ";
		$SQL .= "talkfav, readfav, tvfav, listenfav, talknofav, nosharepanel, demoskills, speclneeds, recentpub, particbio, itemsperday, itemcountother, masque, ";
		$SQL .= "thto10am, thtonoon, thnoonto5pm, thaftr5pm, thaftr6pm, thaftr7pm, th8pm10pm, th10pm12am, th12amfri2am, ";
		$SQL .= "frito10am, fritonoon, frinoonto5pm, friaftr5pm, friaftr6pm, friaftr7pm, fri8pm10pm, fri10pm12am, fri12amsat2am, ";
		$SQL .= "satto10am, sattonoon, satnoonto5pm, sataftr5pm, sataftr6pm, sataftr7pm, sat8pm10pm, sat10pm12am, sat12amsun2am, ";
		$SQL .= "sunto10am, suntonoon, sunnoonto5pm, sunaftr5pm, sunaftr6pm, sunaftr7pm, sun8pm10pm, sun10pm12am, sun12ammon2am, ";
		$SQL .= "monto10am, montonoon, monnoonto5pm ";
		$SQL .= "FROM ".PARTICIPANT_SOURCE.".rawdata WHERE mbox LIKE '".$args[0]."' AND  message_number LIKE '" . $args[1] . "'";

		// We should have one row for the participant
		$result = mysql_query( $SQL );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		
		return $row;
	}
	
	function getLastBadgeId() {
		$SQL = "SELECT MAX(badgeid) FROM Participants WHERE badgeid>='2' AND badgeid NOT IN ('53159','6499')";
		$result = mysql_query( $SQL );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
//		mysql_result($result,0);
		return mysql_result($result,0);
	}

	function setLastBadgeId($badgeid) {
		$SQL = "UPDATE LastBadgeId set badgeid = '" . $badgeid . "' where id like 'last'";
		$result = mysql_query( $SQL );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
	}
	
	function setParticpantImported($mbox, $message_number, $badgeid) {
		$sql = "INSERT into Imported set badgeid ='". $badgeid ."' ";
		$sql .= ", mbox = '" . $mbox . "'";
		$sql .= ", message_number = '" . $message_number . "'";
		$result = mysql_query( $sql );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
	}

	function isParticpantImported($mbox, $message_number) {
		$sql = "SELECT * from Imported where mbox like '". $mbox ."' AND message_number like '" . $message_number ."'";
		$result = mysql_query( $sql );
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		$row = mysql_fetch_array($result,MYSQL_ASSOC);
		return $row != null;
	}
	
	function insertKnowledge($badgeid, $infoid, $info) {
		if ($info != null) {
			$sql = "INSERT into ParticipantGeneralInfo set badgeid ='". $badgeid ."' ";
			$sql .= ", infoid = '" . $infoid . "'";
			$sql .= ", infovalue = '" . mysql_real_escape_string($info) . "'";
			$result = mysql_query( $sql ) ;
			if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
		}
	}

	function insertTrackInterest($badgeid, $trackid) {
		$sql = "INSERT into ParticipantTrackInterest set badgeid ='". $badgeid ."' ";
		$sql .= ", trackid = '" . $trackid . "'";
		$result = mysql_query( $sql ) ;
		if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
	}

	/* ---------------------------------------------------------------------------------------------
	 * 
	 */
	header("Content-type: application/xhtml;charset=latin-1");
	echo("<div id='import'>");

    if (prepare_db()===false) {
        $message="Error connecting to database.";
		echo "<div id='error'>Error connecting to database.</div> </br>";
		echo("</div>");
        exit ();
    }

	$ids = $_GET["ids"];
	
	$badgeid = getLastBadgeId();
	$oldbadgeid = $badgeid;

	foreach ($ids as &$id) {
		$args = explode('_', $id);

		$row = getParticpantDetails($args);
		
		if ( !isParticpantImported($row[mbox], $row[message_number]) ) {
			try {
                // Add them CongoDump
                $badgeid = $badgeid+1;
                echo $row[name]." => ".$badgeid."<br/>";
                setParticpantImported($row[mbox], $row[message_number], $badgeid);
                addtoCongo($badgeid, $row[name], $row[email], $row[postmail]);
                
                // Add to Participants, with language interests
                if ($row[other_fr] != null)
                {
                    $other = $row[other_fr];
                } else
                {
                    $other = $row[other_en];
                };
                
                if ($row[other_fr] != null)
                {
                    $otherLang = $row[language_fr];
                } else
                {
                    $otherLang = $row[language_en];
                };
                
                addToParticipants($badgeid, $row[particbio],
                $row[panel_fr], $row[panel_interp_fr], $row[panel_en], $row[panel_interp_en], $row[moderate], $row[masque],
                $row[french], $row[english], $other, $otherLang, $row[name]
                );
                
                // Add to UserHasPermissionRole
                addToPermissionRoles($badgeid);
                
                // Interests
                insertKnowledge($badgeid, 1, $row[speclknow]);
                insertKnowledge($badgeid, 2, $row[talkfav]);
                insertKnowledge($badgeid, 3, $row[tvfav]);
                insertKnowledge($badgeid, 4, $row[readfav]);
                insertKnowledge($badgeid, 5, $row[listenfav]);
                insertKnowledge($badgeid, 6, $row[talknofav]);
                insertKnowledge($badgeid, 7, $row[nosharepanel]);
                insertKnowledge($badgeid, 8, $row[demoskills]);
                insertKnowledge($badgeid, 9, $row[speclneeds]);
                insertKnowledge($badgeid, 10, $row[recentpub]);
                insertKnowledge($badgeid, 11, $row[gameknow]);
                insertKnowledge($badgeid, 12, $row[visualartsknow]);
                insertKnowledge($badgeid, 13, $row[cultureknow]);
                insertKnowledge($badgeid, 14, $row[kidstrackknow]);
                
                // Tracks
				if ($row[englishlit]) insertTrackInterest($badgeid, 2);
				if ($row[frenchlit]) insertTrackInterest($badgeid, 3);
				if ($row[academictrack]) insertTrackInterest($badgeid, 4);
				if ($row[techtrack]) insertTrackInterest($badgeid, 5);
				if ($row[culturetrack]) insertTrackInterest($badgeid, 6);
				if ($row[fantrack]) insertTrackInterest($badgeid, 7);
				if ($row[gamingtrack]) insertTrackInterest($badgeid, 8);
				if ($row[mediatrack]) insertTrackInterest($badgeid, 9);
				if ($row[kidstrack]) insertTrackInterest($badgeid, 11);
				if ($row[filktrack]) insertTrackInterest($badgeid, 12);
				if ($row[costumetrack]) insertTrackInterest($badgeid, 13);
				if ($row[visualarts]) insertTrackInterest($badgeid, 14);
				if ($row[creativewrite]) insertTrackInterest($badgeid, 16);
				if ($row[quiztrack]) insertTrackInterest($badgeid, 19);
				if ($row[kaffeeklatsch]) insertTrackInterest($badgeid, 20);
				if ($row[readingtrack]) insertTrackInterest($badgeid, 21);

                // Times , $row[itemcountother]
                addAvailabilityDays($badgeid, $row[itemsperday]);
                
                $num = createAvailTimesForDay($badgeid, 1, 1, array ($row[thto10am], $row[thtonoon], $row[thnoonto5pm], $row[thaftr5pm], $row[thaftr6pm], $row[thaftr7pm], $row[th8pm10pm], $row[th10pm12am], $row[th12amfri2am]));
                $num = createAvailTimesForDay($badgeid, 2, $num, array ($row[frito10am], $row[fritonoon], $row[frinoonto5pm], $row[friaftr5pm], $row[friaftr6pm], $row[friaftr7pm], $row[fri8pm10pm], $row[fri10pm12am], $row[fri12amsat2am]));
                $num = createAvailTimesForDay($badgeid, 3, $num, array ($row[satto10am], $row[sattonoon], $row[satnoonto5pm], $row[sataftr5pm], $row[sataftr6pm], $row[sataftr7pm], $row[sat8pm10pm], $row[sat10pm12am], $row[sat12amsun2am]));
                $num = createAvailTimesForDay($badgeid, 4, $num, array ($row[sunto10am], $row[suntonoon], $row[sunnoonto5pm], $row[sunaftr5pm], $row[sunaftr6pm], $row[sunaftr7pm], $row[sun8pm10pm], $row[sun10pm12am], $row[sun12ammon2am]));
                createAvailTimesForDay($badgeid, 5, $num, array ($row[monto10am], $row[montonoon], $row[monnoonto5pm]));
			} catch (Exception $exception) {
				echo "<div id='error'>Problem importing user". $row[name].", exception: ".$exception->getMessage()."</div> </br>";
			};
		}
	}

	if ($oldbadgeid != $badgeid)
		setLastBadgeId($badgeid) ;
	echo("</div>");
?>
