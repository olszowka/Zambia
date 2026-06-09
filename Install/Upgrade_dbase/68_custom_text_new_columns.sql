## This script updates CustomText table to add `active` flag.
##
##  Created by Peter Olszowka on June 8, 2026
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE CustomText
    ADD COLUMN `active` TINYINT(1) NOT NULL DEFAULT 1 AFTER `textcontents`,
    ADD COLUMN `html_block_level` TINYINT(1) NOT NULL DEFAULT 1 AFTER `active`;

UPDATE CustomText
    SET html_block_level = 0
    WHERE tag = 'panel_types_not_int';

UPDATE CustomText
    SET html_block_level = 0
    WHERE tag = 'other_role_desc';

UPDATE CustomText
    SET html_block_level = 0
    WHERE tag = 'roles_checkboxes_label';

UPDATE CustomText
    SET html_block_level = 0
    WHERE tag = 'stuff_id_like_to_run';

INSERT INTO PatchLog (patchname) VALUES ('68_custom_text_active_flag.sql');
