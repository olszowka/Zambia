<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$report['categories']['Boskone Central'] = 320;
$report['columns'] = array(
    null,
    array("orderData" => array(2, 1)),
    array("visible" => false),
    array("orderData" => array(4, 3)),
    array("visible" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false),
    array("orderable" => false)
);
$report['queries']['availability'] =<<<'EOD'
SELECT
        PAD.badgeid, PAD.day, PAD.maxprog
    FROM
                  Participants P
        LEFT JOIN ParticipantAvailabilityDays PAD USING(badgeid)
    WHERE
        P.interested = 1 /* interested */;
EOD;
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, CONCAT(CD.firstname,' ',CD.lastname) AS name, CONCAT(CD.lastname, CD.firstname) AS nameSort,
        IF(instr(P.pubsname, CD.lastname) > 0, CD.lastname, substring_index(P.pubsname, ' ', -1)) AS pubsnameSort,
        PA.maxprog
    FROM
                  Participants P
             JOIN CongoDump CD USING (badgeid)
        LEFT JOIN ParticipantAvailability PA USING(badgeid)
    WHERE
            P.interested = 1 /* interested */
        AND EXISTS (SELECT *
                        FROM
                                 Schedule SCH
                            JOIN ParticipantOnSession POS USING (sessionid)
                        WHERE
                            POS.badgeid = P.badgeid
                    );
EOD;
