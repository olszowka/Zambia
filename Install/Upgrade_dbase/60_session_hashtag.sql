ALTER TABLE Sessions
    ADD COLUMN hashtag VARCHAR(50) DEFAULT NULL;

INSERT INTO PatchLog (patchname) VALUES ('60_session_hashtag.sql');