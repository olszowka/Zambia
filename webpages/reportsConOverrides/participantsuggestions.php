<?php
// Copyright (c) 2026 Peter Olszowka. All rights reserved. See copyright document for more details.
$report['description'] = 'What panel ideas did each participant suggest';
$report['categories']['Boskone Central'] = 900;
$report['queries']['participants'] =<<<'EOD'
SELECT
        P.badgeid, P.pubsname, PS.paneltopics, PS.otherideas, PS.suggestedguests 
    FROM
                  Participants P
        JOIN CongoDump CD USING (badgeid)
        JOIN ParticipantSuggestions PS USING (badgeid)
    WHERE
           IFNULL(PS.paneltopics, '') != ''
        OR IFNULL(PS.otherideas, '') != ''
        OR IFNULL(PS.suggestedguests, '') != ''
    ORDER BY
        IF(INSTR(P.pubsname, CD.lastname) > 0, CD.lastname, SUBSTRING_INDEX(P.pubsname, ' ', -1)),
        CD.firstname;
EOD;
