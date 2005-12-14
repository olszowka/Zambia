<?php
    require_once ('db_functions.php');
    require_once ('RenderSessionCountReport.php');
    require_once ('StaffRenderError.php');
    $title="View Session Report";
    if (prepare_db()===false) {
        $message="Error connecting to database.";
        StaffRenderError($title,$message);
        exit ();
        }
   $query = <<<EOD
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=1 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=1 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
UNION
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=2 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=2 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
UNION
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=3 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=3 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
UNION
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=4 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=4 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
UNION
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=5 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=5 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
UNION
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=6 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=6 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
UNION
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=7 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=7 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
UNION
select trackname, statusname status, count(*) count 
  from Sessions, Tracks, SessionStatuses 
  where Sessions.trackid=Tracks.trackid 
    and SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=8 
  group by Sessions.statusid, Sessions.trackid 
UNION
select "<b>Total", SessionStatuses.statusname, count(*)
  from Sessions, SessionStatuses 
  where SessionStatuses.statusid=Sessions.statusid 
    and SessionStatuses.statusid=8 
  group by Sessions.statusid
UNION select " ", " ", " " from dual
;
EOD;
    if (($result=mysql_query($query,$link))===false) {
        $message="Error retrieving data from database.";
        StaffRenderError($title,$message);
        exit ();
        }
    RenderSessionCountReport();
    exit();
?>
