<?php
// Copyright (c) 2005-2018 Peter Olszowka. All rights reserved. See copyright document for more details.
global $title;
require_once('db_functions.php');
require_once('RenderSessionCountReport.php');
require_once('error_functions.php');
$title = "View Session Report";
if (prepare_db() === false) {
    $message = "Error connecting to database.";
    RenderError($message);
    exit();
}
$query = <<<EOD
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=1 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=1 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=2 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=2 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=3 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=3 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=4 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=4 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=5 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=5 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=6 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=6 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=7 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=7 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=8 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=8 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=9 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=9 
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
UNION
SELECT trackname, statusname status, count(*) count 
  FROM Sessions, Tracks, SessionStatuses 
  WHERE Sessions.trackid=Tracks.trackid 
    AND SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=10 
  GROUP BY Sessions.statusid, Sessions.trackid 
UNION
SELECT "<b>Total", SessionStatuses.statusname, count(*)
  FROM Sessions, SessionStatuses 
  WHERE SessionStatuses.statusid=Sessions.statusid 
    AND SessionStatuses.statusid=10
  GROUP BY Sessions.statusid
UNION SELECT " ", " ", " " FROM dual
;
EOD;
if (!$result = mysqli_query_exit_on_error($query)) {
    exit(); // Should have exited already
}
RenderSessionCountReport($result);
exit();
?>
