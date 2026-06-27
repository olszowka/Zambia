## This script updates CustomText table to add two new entries.
##
##  Created by Peter Olszowka on June 27, 2026
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
INSERT INTO `CustomText`
    (page, tag, textcontents, active, html_block_level)
    VALUES
        ('General Interests', 'people_want_on_sess_label', 'People with whom I\'d like to be on a session: (Leave blank for none)', 0, 0),
        ('General Interests', 'people_dont_want_label', 'People with whom I\'d rather not be on a session: (Leave blank for none)', 0, 0);

INSERT INTO `PatchLog` (patchname) VALUES ('69_my_interests_new_custom_text.sql');
