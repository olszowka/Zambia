## This script does all the schema changes to support the updated session validation
ALTER TABLE SessionStatuses
    ADD COLUMN validate TINYINT NOT NULL DEFAULT 0 AFTER statusname,
    ADD COLUMN may_be_scheduled TINYINT NOT NULL DEFAULT 0 AFTER validate;
UPDATE SessionStatuses
    SET validate=1, may_be_scheduled=1 WHERE statusname in ("Vetted", "Scheduled", "Assigned");
INSERT INTO PatchLog (patchname) VALUES ('22_validate_session.sql');
