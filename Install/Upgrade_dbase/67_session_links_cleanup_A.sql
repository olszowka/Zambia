## This script updates Sessions table to add links-related fields.
##
##  Created by Peter Olszowka on July 4, 2026
##  Copyright (c) 2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Sessions
    ADD COLUMN `panelistlink` varchar(512) DEFAULT NULL AFTER `meetinglink`,
    ADD COLUMN `captionlink` varchar(512) DEFAULT NULL AFTER `panelistlink`,
    ADD COLUMN `recordinglink` varchar(512) DEFAULT NULL AFTER `captionlink`;

INSERT INTO PatchLog (patchname) VALUES ('67_session_links_cleanup_A.sql');
