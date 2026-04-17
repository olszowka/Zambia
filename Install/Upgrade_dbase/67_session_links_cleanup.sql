## This script updates Sessions table to add links-related fields.
##
##  Created by Peter Olszowka on August 19, 2025
##  Copyright (c) 2025-2026 by Peter Olszowka. All rights reserved. See copyright document for more details.
##
ALTER TABLE Sessions
    ADD COLUMN `meetinglink` varchar(512) DEFAULT NULL AFTER `persppartinfo`,
    ADD COLUMN `panelistlink` varchar(512) DEFAULT NULL AFTER `meetinglink`,
    ADD COLUMN `captionlink` varchar(512) DEFAULT NULL AFTER `panelistlink`,
    ADD COLUMN `recordinglink` varchar(512) DEFAULT NULL AFTER `captionlink`;

INSERT INTO PatchLog (patchname) VALUES ('67_session_links_cleanup.sql');
