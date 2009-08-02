<?php
require_once ('db_functions.php');
require_once('StaffCommonCode.php');

require_once('fpdf.php');

class PDF extends FPDF
{
	private $participant;
	private $localPage;
	
	public function setParticipant($p) {
		$this->participant = $p;
	}
	
	public function setLocalPage($p) {
		$this->localPage = $p;
	}
	
	function Header()
	{
		$this->SetFont('Arial','B',15);
		$this->Cell(0,0,$this->participant." - ".$this->localPage,0,0,'C');
		//Line break
		$this->Ln(10);
	}
}

if (prepare_db()===false) {
	$message="Error connecting to database.";
	exit ();
}

$SQL = "SELECT
        P.badgeid, P.pubsname, CD.email, B.starttime, B.title, B.parts,
        if(C.pubsname=P.pubsname,'Yourself',if(C.pubsname is not null, C.pubsname, 'N/A')) as moderator,
        B.description, B.dur, B.roomname, B.languagestatusname, B.trackname,
        if(isnull(D.svcs),'None',D.svcs) as services, B.sessionid
    FROM
        Participants P join
        CongoDump CD using (badgeid) join
        ParticipantOnSession POS using (badgeid) join
           (SELECT
                    S.sessionid, S.title, if(S.progguiddesc!='',S.progguiddesc,S.pocketprogtext) as description,
                    DATE_FORMAT(ADDTIME('2009-08-06 00:00:00',SCH.starttime),'%a %l:%i %p') as starttime,
                    SCH.starttime as starttime2, DATE_FORMAT(S.duration,'%k:%i hrs:min') as dur,
                    R.roomname, L.languagestatusname, T.trackname, A.parts
                FROM
                    Sessions S join
                    Schedule SCH using (sessionid) join
                    Tracks T using (trackid) join
                    LanguageStatuses L using (languagestatusid) join
                    Rooms R using (roomid) join
                       (SELECT
                                SCH.sessionid, GROUP_CONCAT(P.pubsname SEPARATOR ', ') AS parts
                            FROM
                                Schedule SCH join
                                ParticipantOnSession using (sessionid) join
                                Participants P using (badgeid) join
                                CongoDump CD using (badgeid)
                            GROUP BY
                                SCH.scheduleid) as A using (sessionid)
                 ) as B using (sessionid) left join
               (SELECT
                        P2.pubsname, POS2.sessionid
                    FROM
                        Participants P2 join
                        ParticipantOnSession POS2 using (badgeid)
                    WHERE
                        POS2.moderator=1) as C using (sessionid) left join
           (SELECT
                    S.sessionid, GROUP_CONCAT(SV.servicename SEPARATOR ', ') as svcs
                FROM
                    Sessions S join
                    SessionHasService SHS using (sessionid) join
                    Services SV using (serviceid)
                GROUP BY
                    sessionid) as D using (sessionid)
                    ORDER BY P.pubsname, B.starttime2";
//ORDER BY CD.lastname asc, CD.firstname asc, B.starttime2";

$LINE_HEIGHT = 4;
$FONT_SIZE = 8;

if(may_I('create_participant')) {
	$result = mysql_query( $SQL ) or die("Couldnt execute query.".mysql_error());
	if (!$result) throw new Exception("Couldn't execute query.".mysql_error());
	$resultrow = mysql_fetch_array($result,MYSQL_ASSOC);
	
	$pdf=new PDF('P','mm','Letter'); //, 'in', 'letter');
	$pdf->SetFont('Arial');
	$pdf->SetFontSize($FONT_SIZE);
	$pdf->SetAutoPageBreak(false);
	
	$prevName = "";
	$currentPage = $pdf->PageNo();
	$partPageNbr = 1;
	while ($resultrow) {
		$currentName = $resultrow['pubsname'];
		if ($currentName != ' ') {
			if (($currentName != $prevName) || ($currentPage != $pdf->PageNo()) || ($pdf->GetY() > 220)) {
				if ($currentName != $prevName) {
					$partPageNbr = 1;
				} else {
					$partPageNbr += 1;
				}
				$pdf->setParticipant($currentName);
				$pdf->setLocalPage($partPageNbr);
				$pdf->AddPage();
				$prevName = $currentName;
				$currentPage = $pdf->PageNo();
			}
			$pdf->SetFont('Arial', 'B');
			$pdf->Write($LINE_HEIGHT,"Title:  ".$resultrow['title']." ".$pdf->GetY()."\n");
			$pdf->SetFont('Arial');
			$pdf->Write($LINE_HEIGHT,"When:  ".$resultrow['starttime']."\n");
			$pdf->Write($LINE_HEIGHT,"Duration:  ".$resultrow['dur']."\n");
			$pdf->Write($LINE_HEIGHT,"Location:  ".$resultrow['roomname']."\n");
			$pdf->Write($LINE_HEIGHT,"Description:  ".$resultrow['description']."\n");
			$pdf->Write($LINE_HEIGHT,"Language:  ".$resultrow['languagestatusname']."\n");
			$pdf->Write($LINE_HEIGHT,"Track:  ".$resultrow['trackname']."\n");
			$pdf->Write($LINE_HEIGHT,"Moderator:  ".$resultrow['moderator']."\n");
			$pdf->Write($LINE_HEIGHT,"All participants:  ".$resultrow['parts']."\n");
			$pdf->Write($LINE_HEIGHT,"AV/Internet request:  ".$resultrow['services']."\n");
			$pdf->Write($LINE_HEIGHT,"Session ID:  ".$resultrow['sessionid']."\n");
			$pdf->Write($LINE_HEIGHT,"\n");
		}
		$resultrow=mysql_fetch_array($result,MYSQL_ASSOC);
	}

	$pdf->Output();

}
    
?>
