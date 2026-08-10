## This script adds CustomText entries for the 3 labels on the My Suggestions page,
## making them customizable, defaulting to inactive so the built-in defaults are used.
##
##  Created by Peter Olszowka on 2026-08-10;
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
INSERT IGNORE INTO `CustomText`
    (page, tag, textcontents, active, html_block_level)
    VALUES
    ('My Suggestions','paneltopics_label','Program Topic Ideas:',0,0),
    ('My Suggestions','otherideas_label','Other Programming Ideas:',0,0),
    ('My Suggestions','suggestedguests_label','Suggested Guests (please provide addresses and other contact information if possible):',0,0);

INSERT INTO `PatchLog` (patchname) VALUES ('75_my_suggestions_custom_text.sql');
