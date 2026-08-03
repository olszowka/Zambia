<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$report['queries']['schedule'] =<<<'EOD'
SELECT
        DATE_FORMAT(S.duration,'%i') as durationmin, DATE_FORMAT(S.duration,'%k') as durationhrs,
        R.roomid, R.roomname, S.sessionid, S.title,
        DATE_FORMAT(ADDTIME('$ConStartDatim$',SCH.starttime),'%a %l:%i %p') AS starttime
    FROM
             Sessions S
        JOIN Schedule SCH USING (sessionid)
        JOIN Rooms R USING (roomid)
    WHERE
            R.roomid = 7 /* Dragon's Lair */
        AND S.pubstatusid = 2 /* public */
    ORDER BY
        SCH.starttime, R.roomname;
EOD;
