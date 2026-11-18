## This script fixes a typo in the CustomText tag for the Declined Participant page:
## 'declined_particpant' becomes 'declined_participant'.
##
##  Created by Peter Olszowka on 2026-11-14;
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
UPDATE `CustomText` SET tag = 'declined_participant' WHERE tag = 'declined_particpant';

INSERT INTO `PatchLog` (patchname) VALUES ('78_fix_declined_participant_tag.sql');
