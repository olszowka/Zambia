## This script modifies two tables for changes to log of session edits.  
ALTER TABLE SessionEditHistory ADD COLUMN editdescription TEXT NULL AFTER statusid;
INSERT INTO SessionEditCodes  
        (sessioneditcode, description, display_order)
    VALUES
        (4, 'Add to schedule', 5),
        (5, 'Remove from schedule', 6),
        (6, 'Created by import', 3);
UPDATE SessionEditCodes
        SET display_order=4 WHERE sessioneditcode=3;
INSERT INTO PatchLog (patchname) VALUES ('31_session_edit_history.sql');
